<?php

namespace App\Console\Commands;

use App\Models\Blotter;
use App\Models\CaseFile;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BackfillCasesFromBlotters extends Command
{
    /*
     * The name and signature of the console command.
     */
    protected $signature = 'cases:backfill-from-blotters {--dry-run : Do not write anything}';

    /**
     * The console command description.
     */
    protected $description = 'Ensure every blotter has a corresponding cases row, so analytics (Case Types) can count all blotters. Backfills cases fields from blotters when missing.';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $this->info('Starting backfill: cases <- blotters');
        $this->info('Mode: ' . ($dryRun ? 'DRY RUN (no writes)' : 'WRITE')); 

        $casesTableColumns = $this->getTableColumns('cases');
        $needsColumns = array_flip($casesTableColumns);

        // We only populate columns that exist in the cases table.
        $requiredForUpsert = array_values(array_intersect(
            ['case_no', 'blotter_id', 'status', 'opened_at', 'closed_at', 'resolution_summary', 'handled_by', 'archived_at'],
            $casesTableColumns
        ));

        if (empty($requiredForUpsert)) {
            $this->error('Could not find any expected columns in cases table. Aborting.');
            return 1;
        }

        $totalBlotters = Blotter::count();
        $this->info("Total blotters: {$totalBlotters}");

        // Backfill only missing cases rows.
        // Blotter->case is hasOne(CaseFile), so absence means no cases row.
        $query = Blotter::query();
        $query->whereDoesntHave('case');

        // Also include archived blotters? requirement didn't specify.
        // For analytics, most dashboards exclude archived for blotters/cases.
        // Keep it consistent by backfilling for NON-archived blotters.
        if (method_exists(Blotter::class, 'scopeNotArchived')) {
            $query->notArchived();
        }

        $missingCount = (clone $query)->count();
        $this->info("Blotters missing cases: {$missingCount}");

        $processed = 0;
        $created = 0;
        $ignoredKeys = [];

        $bar = $this->output->createProgressBar($missingCount);
        $bar->start();

        DB::transaction(function () use ($query, &$processed, &$created, &$ignoredKeys, $dryRun, $bar, $needsColumns) {
            $batchSize = 500;
            $query->chunkById($batchSize, function ($blotters) use (&$processed, &$created, &$ignoredKeys, $dryRun, $bar, $needsColumns) {
                foreach ($blotters as $blotter) {
                    $processed++;

                    // Map blotter fields into cases table columns as best effort.
                    // Cases table schema is limited; we set defaults for required analytic fields.
                    $payload = [];

                    // case_no: deterministic if we cannot read existing (since it's missing).
                    // If a blotter already has a case_no naming convention elsewhere, we can mimic it.
                    // Here: derive from year + blotter id to avoid collisions.
                    // (If you already have a specific case_no generator, adjust mapping.)
                    if (isset($needsColumns['case_no'])) {
                        $year = Carbon::now()->year;
                        $payload['case_no'] = 'CASE-' . $year . '-' . str_pad((string) $blotter->id, 6, '0', STR_PAD_LEFT);
                    }

                    if (isset($needsColumns['blotter_id'])) {
                        $payload['blotter_id'] = $blotter->id;
                    }

                    // status rules:
                    // - If blotter has a related case.status, we wouldn't be here.
                    // - For missing cases, default to 'ongoing' (scheduled considered on going).
                    if (isset($needsColumns['status'])) {
                        $payload['status'] = 'ongoing';
                    }

                    // Ensure analytics date filtering on `cases.opened_at` works.

                    // Also set created_at/updated_at if those columns exist, using blotter.incident_date.
                    if (isset($needsColumns['created_at']) && isset($needsColumns['updated_at'])) {
                        $payload['created_at'] = $blotter->incident_date ? Carbon::parse($blotter->incident_date) : now();
                        $payload['updated_at'] = $payload['created_at'];
                    }

                    // opened_at: use blotter incident_date if available.
if (isset($needsColumns['opened_at'])) {
                        $openedAt = $blotter->incident_date;
                        if ($openedAt) {
                            $payload['opened_at'] = Carbon::parse($openedAt);
                        } else {
                            $payload['opened_at'] = now();
                        }
                    }

                    if (isset($needsColumns['closed_at'])) {
                        $payload['closed_at'] = null;
                    }

                    if (isset($needsColumns['resolution_summary'])) {
                        $payload['resolution_summary'] = null;
                    }

                    if (isset($needsColumns['handled_by'])) {
                        // We don't have handled_by on blotter; default null.
                        $payload['handled_by'] = null;
                    }

                    if (isset($needsColumns['archived_at'])) {
                        // default null
                        $payload['archived_at'] = null;
                    }

                    if ($dryRun) {
                        $created++;
                        $bar->advance();
                        continue;
                    }

                    // Upsert by blotter_id (unique FK from cases->blotters). Use updateOrCreate for safety.
                    // If case_no collides, updateOrCreate will keep existing row; that's OK for backfill.
                    CaseFile::updateOrCreate(
                        ['blotter_id' => $blotter->id],
                        $payload
                    );

                    $created++;
                    $bar->advance();
                }
            });
        });

        $bar->finish();
        $this->newLine();

        $this->info('Backfill completed.');
        $this->info('Processed blotters: ' . $processed);
        $this->info(($dryRun ? 'Would create cases rows' : 'Created cases rows') . ': ' . $created);

        // Note: ignoring/unexpected keys is not relevant here because we map only to existing columns.
        if (!empty($ignoredKeys)) {
            Log::info('[cases:backfill-from-blotters] ignored payload keys', $ignoredKeys);
        }

        return 0;
    }

    private function getTableColumns(string $table): array
    {
        $cols = DB::select("SELECT COLUMN_NAME as name FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ?", [$table]);
        return array_map(fn($c) => $c->name, $cols);
    }
}

