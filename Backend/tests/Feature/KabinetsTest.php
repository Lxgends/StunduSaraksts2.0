<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Kabinets;
use Illuminate\Foundation\Testing\RefreshDatabase;

class KabinetsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test creating a new Kabinets resource with valid data
     *
     * @return void
     */
    public function test_create_cesis_102()
    {
        $data = [
            'vieta' => 'Cēsis',
            'skaitlis' => '102',
        ];

        $response = $this->postJson('/api/kabinets', $data);

        $response->assertStatus(201);

        $this->assertDatabaseHas('kabinets', [
            'vieta' => 'Cēsis',
            'skaitlis' => '102',
        ]);
    }

    /**
     * Test creating a new Kabinets resource with an invalid location
     *
     * @return void
     */
    public function test_create_kabinets_invalid_vieta()
    {
        $data = [
            'vieta' => 'Riga',
            'skaitlis' => '102', 
        ];

        $response = $this->postJson('/api/kabinets', $data);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors(['vieta']);
    }

    /**
     * Test creating a new Kabinets resource with Priekuļi location
     *
     * @return void
     */
    public function test_create_priekuli_101()
    {
        $data = [
            'vieta' => 'Priekuļi',
            'skaitlis' => '101',
        ];

        $response = $this->postJson('/api/kabinets', $data);

        $response->assertStatus(201);

        $this->assertDatabaseHas('kabinets', [
            'vieta' => 'Priekuļi',
            'skaitlis' => '101',
        ]);
    }

    /**
     * Test creating a new Kabinets resource with missing data
     *
     * @return void
     */
    public function test_create_kabinets_missing_data()
    {
        $response = $this->postJson('/api/kabinets', [
            'vieta' => '',
            'skaitlis' => '',
        ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors(['vieta', 'skaitlis']);
    }
}
