<?php

namespace Tests\Feature;

use App\Models\Blotter;
use App\Models\CaseFile;
use App\Models\CaseHearing;
use App\Models\DocumentRequest;
use App\Models\DocumentType;
use App\Models\Payment;
use App\Models\Resident;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResidentPortalServicesTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_sent_to_login_and_resumes_the_requested_resident_page(): void
    {
        [$user] = $this->createResident('guest-target@example.test');

        $this->get(route('resident.cases'))
            ->assertRedirect(route('login'));

        $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ])
            ->assertOk()
            ->assertJsonPath('redirect', route('resident.cases'));
    }

    public function test_eblotter_submission_uses_the_authenticated_resident_and_filed_status(): void
    {
        [$user, $resident] = $this->createResident('reporter@example.test');
        [, $otherResident] = $this->createResident('other-reporter@example.test');

        $response = $this->actingAs($user)->post(route('resident.blotters.store'), [
            'incident_date' => '2026-10-16T09:30',
            'incident_type' => 'Noise Disturbance',
            'incident_location' => 'Bucandala 1',
            'narrative' => 'A detailed report of the incident.',
            'complainant_resident_id' => $otherResident->id,
            'complainant_name' => 'Forged Name',
            'complainant_email' => $otherResident->email,
            'complainant_contact' => '0000000000',
            'recorded_by' => $otherResident->user_id,
            'status' => 'settled',
            'blotter_no' => 'FORGED-001',
            'remarks' => 'This resident-submitted value must not be saved.',
        ]);

        $blotter = Blotter::query()->firstOrFail();
        $response->assertRedirect(route('resident.blotters.show', $blotter));
        $this->assertSame($resident->id, $blotter->complainant_resident_id);
        $this->assertSame($user->id, $blotter->recorded_by);
        $this->assertSame('Test Resident', $blotter->complainant_name);
        $this->assertSame($user->email, $blotter->complainant_email);
        $this->assertSame($resident->contact_no, $blotter->complainant_contact);
        $this->assertSame('filed', $blotter->status);
        $this->assertSame('Noise Disturbance', $blotter->incident_type);
        $this->assertNull($blotter->respondent_name);
        $this->assertNull($blotter->remarks);
        $this->assertStringStartsWith('BLT-'.now()->year.'-', $blotter->blotter_no);
    }

    public function test_resident_cannot_view_another_residents_blotter_details(): void
    {
        [$user] = $this->createResident('viewer@example.test');
        [, $owner] = $this->createResident('owner@example.test');
        $blotter = $this->createBlotter($owner, 'BLT-2026-000001');

        $this->actingAs($user)
            ->get(route('resident.blotters.show', $blotter))
            ->assertNotFound();
    }

    public function test_case_history_includes_all_owned_blotters_and_case_hearing_details(): void
    {
        [$user, $resident] = $this->createResident('case-owner@example.test');
        [, $otherResident] = $this->createResident('different-case-owner@example.test');

        $activeBlotter = $this->createBlotter($resident, 'BLT-2026-000011');
        $activeCase = $this->createCase($activeBlotter, 'CASE-2026-000011', 'scheduled');
        CaseHearing::create([
            'case_id' => $activeCase->id,
            'scheduled_at' => '2026-11-05 14:30:00',
            'location' => 'Barangay Hall',
            'status' => 'scheduled',
            'notes' => 'Bring the requested documents.',
        ]);

        $filedOnly = $this->createBlotter($resident, 'BLT-2026-000012');
        $settled = $this->createBlotter($resident, 'BLT-2026-000013');
        $settledCase = $this->createCase($settled, 'CASE-2026-000013', 'settled');
        $settledCase->update([
            'closed_at' => now(),
            'resolution_summary' => 'The parties settled the matter.',
        ]);
        $otherResidentCase = $this->createBlotter($otherResident, 'BLT-2026-000014');
        $this->createCase($otherResidentCase, 'CASE-2026-000014', 'ongoing');

        $response = $this->actingAs($user)->get(route('resident.cases'));

        $response->assertOk()
            ->assertSee('CASE-2026-000011')
            ->assertSee('Nov 05, 2026 02:30 PM')
            ->assertSee('Bring the requested documents.')
            ->assertSee('CASE-2026-000013')
            ->assertSee('Closed As')
            ->assertSee('The parties settled the matter.')
            ->assertSee('BLT-2026-000012')
            ->assertDontSee('CASE-2026-000014');
        $this->assertDatabaseMissing('cases', ['blotter_id' => $filedOnly->id]);
    }

    public function test_document_request_uses_server_fee_status_and_resident_and_creates_pending_payment(): void
    {
        [$user, $resident] = $this->createResident('document-requester@example.test');
        [, $otherResident] = $this->createResident('someone-else@example.test');
        $documentType = DocumentType::create([
            'name' => 'Barangay Clearance',
            'fee' => 125,
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->post(route('resident.document-requests.store'), [
            'document_type_id' => $documentType->id,
            'purpose_type' => 'Employment',
            'resident_id' => $otherResident->id,
            'fee_amount' => 0,
            'status' => 'released',
            'released_by' => $otherResident->user_id,
        ]);

        $documentRequest = DocumentRequest::query()->firstOrFail();
        $response->assertRedirect(route('resident.document-requests'));
        $this->assertSame($resident->id, $documentRequest->resident_id);
        $this->assertSame(125.0, (float) $documentRequest->fee_amount);
        $this->assertSame('pending', $documentRequest->status);
        $this->assertSame('resident', $documentRequest->requested_via);
        $this->assertSame('Employment', $documentRequest->purpose);
        $this->assertSame('pending', Payment::query()->firstOrFail()->status);
    }

    public function test_document_request_page_limits_types_and_accepts_each_supported_purpose(): void
    {
        [$user] = $this->createResident('purpose-options@example.test');
        $residency = DocumentType::create([
            'name' => 'Certificate of Residency',
            'fee' => 0,
            'status' => 'active',
        ]);
        $clearance = DocumentType::create([
            'name' => 'Barangay Clearance',
            'fee' => 0,
            'status' => 'active',
        ]);
        DocumentType::create([
            'name' => 'Certificate of Indigency',
            'fee' => 0,
            'status' => 'active',
        ]);

        $this->actingAs($user)->get(route('resident.document-requests'))
            ->assertOk()
            ->assertSee('Certificate of Residency')
            ->assertSee('Barangay Clearance')
            ->assertDontSee('Certificate of Indigency');

        $purposes = [
            'Employment',
            'School or Educational Requirement',
            'Scholarship Application',
            'Financial Assistance',
            'Medical Assistance',
            'Business Requirement',
            'Government Transaction',
            'Personal Requirement',
            'Others',
        ];

        foreach ($purposes as $index => $purpose) {
            $response = $this->post(route('resident.document-requests.store'), [
                'document_type_id' => $index % 2 === 0 ? $residency->id : $clearance->id,
                'purpose_type' => $purpose,
                'other_purpose' => $purpose === 'Others' ? 'Community activity' : null,
            ]);
            $response->assertRedirect(route('resident.document-requests'));
        }

        $this->assertDatabaseCount('document_requests', count($purposes));
        $this->assertDatabaseHas('document_requests', ['purpose' => 'Others: Community activity']);
    }

    public function test_document_requests_reject_other_document_types_and_require_text_for_other_purpose(): void
    {
        [$user] = $this->createResident('document-validation@example.test');
        $unsupportedType = DocumentType::create([
            'name' => 'Certificate of Indigency',
            'fee' => 0,
            'status' => 'active',
        ]);

        $this->actingAs($user)
            ->from(route('resident.document-requests'))
            ->post(route('resident.document-requests.store'), [
                'document_type_id' => $unsupportedType->id,
                'purpose_type' => 'Others',
            ])
            ->assertRedirect(route('resident.document-requests'))
            ->assertSessionHasErrors(['document_type_id', 'other_purpose']);
        $this->assertDatabaseCount('document_requests', 0);
    }

    private function createResident(string $email): array
    {
        $user = User::factory()->create([
            'email' => $email,
            'role' => 'resident',
        ]);

        $resident = Resident::create([
            'first_name' => 'Test',
            'last_name' => 'Resident',
            'address_line' => 'Bucandala 1',
            'contact_no' => '09123456789',
            'user_id' => $user->id,
        ]);

        return [$user, $resident];
    }

    private function createBlotter(Resident $resident, string $blotterNo): Blotter
    {
        return Blotter::create([
            'blotter_no' => $blotterNo,
            'incident_date' => now()->subDay(),
            'incident_type' => 'Noise Disturbance',
            'incident_location' => 'Bucandala 1',
            'narrative' => 'Test incident narrative.',
            'complainant_resident_id' => $resident->id,
            'complainant_name' => $resident->full_name,
            'respondent_name' => 'Respondent Name',
            'recorded_by' => $resident->user_id,
            'status' => 'filed',
        ]);
    }

    private function createCase(Blotter $blotter, string $caseNo, string $status): CaseFile
    {
        return CaseFile::create([
            'case_no' => $caseNo,
            'blotter_id' => $blotter->id,
            'status' => $status,
            'opened_at' => now(),
        ]);
    }
}
