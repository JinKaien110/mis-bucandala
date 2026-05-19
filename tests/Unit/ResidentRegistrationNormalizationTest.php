<?php

namespace Tests\Unit;

use App\Http\Controllers\Public\ResidentRegistrationController;
use Tests\TestCase;

class ResidentRegistrationNormalizationTest extends TestCase
{
    /**
     * Test address normalization for household matching
     */
    public function test_address_normalization_matches_variations()
    {
        $controller = new ResidentRegistrationController();
        
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('normalizeAddressComponent');
        $method->setAccessible(true);

        // Test normalization of block/blk and lot/lt variations
        $testCases = [
            ['Blk 1 Lot 2', 'blk 1 lt 2'],
            ['BLOCK 1 LOT 2', 'blk 1 lt 2'],
            ['block 1 lot 2', 'blk 1 lt 2'],
            ['BLK 1 LT 2', 'blk 1 lt 2'],
            ['Blk. 1, Lot 2', 'blk 1 lt 2'],
            ['Blk-1 Lot-2', 'blk1 lt2'],
        ];

        foreach ($testCases as [$input, $expected]) {
            $normalized = $method->invoke($controller, $input);
            $this->assertEquals(
                $expected, 
                $normalized, 
                "Input: '{$input}' normalized to '{$normalized}' but expected '{$expected}'"
            );
        }
    }

    /**
     * Test that normalization handles null/empty values
     */
    public function test_normalization_handles_empty_values()
    {
        $controller = new ResidentRegistrationController();
        
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('normalizeAddressComponent');
        $method->setAccessible(true);

        $this->assertEquals('', $method->invoke($controller, null));
        $this->assertEquals('', $method->invoke($controller, ''));
        $this->assertEquals('', $method->invoke($controller, '   '));
    }

    /**
     * Test that normalization removes special characters
     */
    public function test_normalization_removes_special_characters()
    {
        $controller = new ResidentRegistrationController();
        
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('normalizeAddressComponent');
        $method->setAccessible(true);

        // Special chars removed, spaces collapsed - "Test@Address!" -> "testaddress"
        $this->assertEquals('testaddress', $method->invoke($controller, 'Test@Address!'));
        // "Phase#1" -> "phase1" (special char removed)
        $this->assertEquals('phase1', $method->invoke($controller, 'Phase#1'));
    }

    /**
     * Test that street field is removed from controller
     */
    public function test_street_field_is_not_in_required_fields()
    {
        // Check that the validation rules in the controller do NOT include street
        $reflection = new \ReflectionClass(ResidentRegistrationController::class);
        $method = $reflection->getMethod('register');
        $method->setAccessible(true);

        // Read the source file and check for street in validation
        $source = file_get_contents(app_path('Http/Controllers/Public/ResidentRegistrationController.php'));
        
        // Street should NOT be in the validation array (line 163 has it commented)
        $this->assertStringContainsString(
            "// 'street' removed", 
            $source,
            "Street field should be commented out in validation rules"
        );
        
        // Verify it's not in the required fields
        $this->assertStringNotContainsString(
            "'street' => ['required'",
            $source,
            "Street field should not be required"
        );
    }
}
