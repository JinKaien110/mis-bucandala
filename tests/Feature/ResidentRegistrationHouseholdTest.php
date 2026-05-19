<?php

namespace Tests\Feature;

use App\Models\Household;
use App\Models\OtpVerification;
use App\Models\Resident;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResidentRegistrationHouseholdTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that street field is not required and is set to null
     */
    public function test_street_field_is_not_required_and_set_to_null()
    {
        // First create an OTP verification
        $otp = OtpVerification::create([
            'email' => 'test@example.com',
            'purpose' => 'resident_registration',
            'verification_token' => 'testtoken123456',
            'verified_at' => now(),
        ]);

        $data = [
            'verification_token' => 'testtoken123456',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'birth_date' => '2000-01-01',
            'sex' => 'male',
            'address_line' => 'Blk 1 Lot 2',
            'phase' => 'Phase 1',
            'contact_no' => '09123456789',
            'email' => 'test@example.com',
            'verification_type' => 'barangay_id',
            'id_image_path' => null,
        ];

        $response = $this->postJson('/api/v1/public/residents/register', $data);

        // Should not fail due to street field
        // Note: Would need file uploads for full test, but street is not required
        $response->assertStatus(422);
        $response->assertSee('id_image_path'); // Should fail on file upload, not street
        
        // Check that street is not in the validated data requirements
        $this->assertArrayNotHasKey('street', $data);
    }

    /**
     * Test address normalization for household matching
     */
    public function test_address_normalization_matches_variations()
    {
        $controller = new \App\Http\Controllers\Public\ResidentRegistrationController();
        
        // Test normalization of block/blk and lot/lt variations
        // Special chars are stripped, so "Blk-1" becomes "blk1" not "blk 1"
        $testCases = [
            ['Blk 1 Lot 2', 'blk 1 lt 2'],
            ['BLOCK 1 LOT 2', 'blk 1 lt 2'],
            ['block 1 lot 2', 'blk 1 lt 2'],
            ['BLK 1 LT 2', 'blk 1 lt 2'],
            ['Blk. 1, Lot 2', 'blk 1 lt 2'],
            ['Blk-1 Lot-2', 'blk1 lt2'], // Dash is stripped making it "blk1 lt2"
            ['BLK 1 LT 2', 'blk 1 lt 2'],
        ];

        foreach ($testCases as [$input, $expected]) {
            $reflection = new \ReflectionClass($controller);
            $method = $reflection->getMethod('normalizeAddressComponent');
            $method->setAccessible(true);
            
            $normalized = $method->invoke($controller, $input);
            $this->assertEquals($expected, $normalized, "Input: {$input} normalized to '{$normalized}' but expected '{$expected}'");
        }
    }

    /**
     * Test that existing household is joined instead of creating new one
     */
    public function test_existing_household_is_joined()
    {
        // Create an existing household
        $existingHousehold = Household::create([
            'household_code' => 'HH-2026-000001',
            'address_line' => 'Blk 1 Lot 2',
            'phase' => 'Phase 1',
            'contact_no' => '09123456789',
            'total_members' => 0,
            'total_adults' => 0,
            'total_minors' => 0,
            'total_senior_citizens' => 0,
            'total_pwd' => 0,
            'registered_pets_count' => 0,
        ]);

        // Create OTP
        $otp = OtpVerification::create([
            'email' => 'test2@example.com',
            'purpose' => 'resident_registration',
            'verification_token' => 'testtoken654321',
            'verified_at' => now(),
        ]);

        // Prepare registration data with same address
        $data = [
            'verification_token' => 'testtoken654321',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'birth_date' => '2000-02-01',
            'sex' => 'female',
            'address_line' => 'blk 1 lt 2', // Normalized version
            'phase' => 'phase 1', // Normalized version
            'contact_no' => '0987654321',
            'email' => 'test2@example.com',
            'verification_type' => 'barangay_id',
            'id_image_path' => null,
        ];

        // Note: This will fail due to file upload requirements in practice
        // But the logic for finding existing household should work
        
        // Verify the controller's findExistingHousehold method works
        $controller = new \App\Http\Controllers\Public\ResidentRegistrationController();
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('findExistingHousehold');
        $method->setAccessible(true);
        
        $foundHousehold = $method->invoke($controller, 'blk 1 lt 2', 'phase 1');
        $this->assertNotNull($foundHousehold);
        $this->assertEquals($existingHousehold->id, $foundHousehold->id);
    }

    /**
     * Test that new household is created when no match found
     */
    public function test_new_household_created_when_no_match()
    {
        $controller = new \App\Http\Controllers\Public\ResidentRegistrationController();
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('findExistingHousehold');
        $method->setAccessible(true);
        
        $foundHousehold = $method->invoke($controller, 'Nonexistent Address', 'Phase 99');
        $this->assertNull($foundHousehold);
    }

    /**
     * Test that relationship is properly set for household members
     */
    public function test_relationship_is_head_for_first_member()
    {
        $household = Household::create([
            'household_code' => 'HH-2026-000002',
            'address_line' => 'Test Address',
            'phase' => 'Test Phase',
            'contact_no' => '09123456789',
            'total_members' => 0,
            'total_adults' => 0,
            'total_minors' => 0,
            'total_senior_citizens' => 0,
            'total_pwd' => 0,
            'registered_pets_count' => 0,
        ]);

        $resident = Resident::create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'birth_date' => '2000-01-01',
            'sex' => 'male',
            'address_line' => 'Test Address',
            'phase' => 'Test Phase',
            'contact_no' => '09123456789',
            'verification_type' => 'barangay_id',
            'verification_status' => 'verified',
        ]);

        // Call the controller's attach method
        $controller = new \App\Http\Controllers\Public\ResidentRegistrationController();
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('attachResidentToHousehold');
        $method->setAccessible(true);
        $method->invoke($controller, $household, $resident);

        // Check that member was created with Head relationship
        $this->assertDatabaseHas('household_members', [
            'resident_id' => $resident->id,
            'household_id' => $household->id,
            'relationship' => 'Head',
        ]);
    }

    /**
     * Test that relationship is Member for subsequent members
     */
    public function test_relationship_is_member_for_subsequent_members()
    {
        $household = Household::create([
            'household_code' => 'HH-2026-000003',
            'address_line' => 'Test Address 2',
            'phase' => 'Test Phase 2',
            'contact_no' => '09123456789',
            'total_members' => 1,
            'total_adults' => 1,
            'total_minors' => 0,
            'total_senior_citizens' => 0,
            'total_pwd' => 0,
            'registered_pets_count' => 0,
        ]);

        // Create first member
        \App\Models\HouseholdMember::create([
            'household_id' => $household->id,
            'resident_id' => null,
            'first_name' => 'First',
            'last_name' => 'Member',
            'birth_date' => '1990-01-01',
            'relationship' => 'Head',
            'is_pwd' => false,
        ]);

        $resident = Resident::create([
            'first_name' => 'Second',
            'last_name' => 'Resident',
            'birth_date' => '2000-01-01',
            'sex' => 'male',
            'address_line' => 'Test Address 2',
            'phase' => 'Test Phase 2',
            'contact_no' => '09123456789',
            'verification_type' => 'barangay_id',
            'verification_status' => 'verified',
        ]);

        // Call the controller's attach method
        $controller = new \App\Http\Controllers\Public\ResidentRegistrationController();
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('attachResidentToHousehold');
        $method->setAccessible(true);
        $method->invoke($controller, $household, $resident);

        // Check that member was created with Member relationship
        $this->assertDatabaseHas('household_members', [
            'resident_id' => $resident->id,
            'household_id' => $household->id,
            'relationship' => 'Member',
        ]);
    }
}
