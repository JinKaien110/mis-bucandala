<?php

namespace App\Providers;

use App\Models\Resident;
use App\Models\DocumentType;
use App\Models\DocumentRequest;
use App\Models\Blotter;
use App\Models\CaseFile;
use App\Models\Household;
use App\Models\HouseholdMember;
use App\Models\BarangayOfficial;
use App\Models\Event;
use App\Models\Pet;
use App\Models\Announcement;
use App\Observers\AuditObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Bridge\Brevo\Transport\BrevoTransportFactory;
use Symfony\Component\Mailer\Transport\Dsn;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register AuditObserver for automatic activity logging
        Resident::observe(AuditObserver::class);
        DocumentType::observe(AuditObserver::class);
        DocumentRequest::observe(AuditObserver::class);
        Blotter::observe(AuditObserver::class);
        CaseFile::observe(AuditObserver::class);
        Household::observe(AuditObserver::class);
        HouseholdMember::observe(AuditObserver::class);
        BarangayOfficial::observe(AuditObserver::class);
        Event::observe(AuditObserver::class);
        Pet::observe(AuditObserver::class);
        Announcement::observe(AuditObserver::class);
          Mail::extend('brevo', function () {
            return (new BrevoTransportFactory)->create(
                Dsn::fromString(
                    'brevo+api://' . rawurlencode(config('services.brevo.key')) . '@default'
                )
            );
        });
    }
}
