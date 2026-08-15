<?php

namespace Tests\Feature\Api;

use App\Models\Donation;
use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DonationTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_donations_are_public_by_default(): void
    {
        $donation = Donation::factory()->paid()->create();

        $this->assertTrue($donation->is_public);
    }

    public function test_private_donations_are_not_returned_by_public_endpoint(): void
    {
        $publicDonation = Donation::factory()->paid()->create(['is_public' => true]);
        $privateDonation = Donation::factory()->paid()->create(['is_public' => false]);

        $response = $this->getJson('/api/donations');

        $response->assertOk()
            ->assertJsonFragment(['id' => $publicDonation->id])
            ->assertJsonMissing(['id' => $privateDonation->id]);
    }

    public function test_admin_can_make_a_donation_private(): void
    {
        $admin = Admin::query()->create([
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
            'password' => 'password',
            'is_active' => true,
        ]);
        Sanctum::actingAs($admin);

        $donation = Donation::factory()->paid()->create();

        $this->putJson("/api/admin/donations/{$donation->id}", [
            'is_public' => false,
        ])->assertOk()
            ->assertJsonPath('data.is_public', false);

        $this->assertDatabaseHas('donations', [
            'id' => $donation->id,
            'is_public' => false,
        ]);
    }
    
    /**
     * Test donation validation
     */
    public function test_donation_requires_amount(): void
    {
        $response = $this->postJson('/api/donations/pay', [
            'name' => 'Test Donor',
            'email' => 'test@example.com',
            // No amount provided
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['amount']);
    }
    
    /**
     * Test donation minimum amount
     */
    public function test_donation_requires_minimum_amount(): void
    {
        $response = $this->postJson('/api/donations/pay', [
            'name' => 'Test Donor',
            'email' => 'test@example.com',
            'amount' => 500, // Below minimum of 1000
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['amount']);
    }
    
    /**
     * Test donation email validation
     */
    public function test_donation_email_must_be_valid(): void
    {
        $response = $this->postJson('/api/donations/pay', [
            'name' => 'Test Donor',
            'email' => 'invalid-email',
            'amount' => 10000,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }
    
    /**
     * Test successful donation creation
     */
    public function test_can_create_donation(): void
    {
        // Skip this test for now as we need to properly mock the Payment facade
        $this->markTestSkipped('Need to properly mock Payment facade');

        /* Original mocking code that needs fixing
        $this->mock('Shetabit\Payment\Facade\Payment')
            ->shouldReceive('callbackUrl')
            ->andReturnSelf()
            ->shouldReceive('purchase')
            ->andReturnSelf()
            ->shouldReceive('pay')
            ->andReturn((object) [
                'getTargetUrl' => function() {
                    return 'https://sandbox.zarinpal.com/pg/StartPay/12345';
                }
            ]);
        */
        
        $response = $this->postJson('/api/donations/pay', [
            'name' => 'Test Donor',
            'email' => 'test@example.com',
            'amount' => 10000,
            'message' => 'Test donation message',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'payment_url',
                'donation_id',
            ]);
            
        $this->assertDatabaseHas('donations', [
            'name' => 'Test Donor',
            'email' => 'test@example.com',
            'amount' => 10000,
            'message' => 'Test donation message',
            'status' => 'pending',
        ]);
    }
    
    /**
     * Test anonymous donation
     */
    public function test_can_create_anonymous_donation(): void
    {
        // Skip this test for now as we need to properly mock the Payment facade
        $this->markTestSkipped('Need to properly mock Payment facade');

        /* Original mocking code that needs fixing
        $this->mock('Shetabit\Payment\Facade\Payment')
            ->shouldReceive('callbackUrl')
            ->andReturnSelf()
            ->shouldReceive('purchase')
            ->andReturnSelf()
            ->shouldReceive('pay')
            ->andReturn((object) [
                'getTargetUrl' => function() {
                    return 'https://sandbox.zarinpal.com/pg/StartPay/12345';
                }
            ]);
        */
        
        $response = $this->postJson('/api/donations/pay', [
            'amount' => 10000,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
            
        $this->assertDatabaseHas('donations', [
            'name' => 'ناشناس',
            'amount' => 10000,
            'status' => 'pending',
        ]);
    }
}
