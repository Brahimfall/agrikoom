<?php

namespace Tests\Feature;

use App\Models\JobOffer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminJobOfferManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_open_job_offer_admin(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($user)->get(route('admin.jobs.index'));

        $response->assertForbidden();
    }

    public function test_admin_can_create_job_offer(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route('admin.jobs.store'), [
            'title' => 'Responsable ferme maraichere',
            'company' => 'Ferme Teranga',
            'location' => 'Thies',
            'contract_type' => 'CDI',
            'deadline' => '2026-12-31',
            'summary' => 'Piloter une exploitation maraichere et coordonner les equipes terrain.',
            'details' => 'Planification culturale, suivi production, reporting et respect des normes qualite.',
            'profile' => 'Experience en maraichage et capacite de gestion d equipe.',
            'apply_email' => 'rh@example.com',
            'status' => 'active',
            'is_featured' => '1',
        ]);

        $response->assertRedirect(route('admin.jobs.index'));

        $this->assertDatabaseHas('job_offers', [
            'title' => 'Responsable ferme maraichere',
            'slug' => 'responsable-ferme-maraichere',
            'status' => 'active',
            'is_featured' => true,
        ]);

        $this->get(route('jobs.index'))
            ->assertOk()
            ->assertSee('Responsable ferme maraichere');
    }

    public function test_admin_can_update_job_offer(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $job = JobOffer::query()->create([
            'title' => 'Commercial agricole',
            'slug' => 'commercial-agricole',
            'company' => 'AgriKoom',
            'summary' => 'Developpement commercial.',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.jobs.update', $job), [
            'title' => 'Commercial agricole senior',
            'company' => 'AgriKoom',
            'location' => 'Dakar',
            'contract_type' => 'CDD',
            'summary' => 'Developpement commercial et animation reseau.',
            'details' => null,
            'profile' => null,
            'apply_email' => null,
            'apply_url' => null,
            'status' => 'active',
        ]);

        $response->assertRedirect(route('admin.jobs.index'));

        $this->assertDatabaseHas('job_offers', [
            'title' => 'Commercial agricole senior',
            'slug' => 'commercial-agricole-senior',
            'status' => 'active',
        ]);
    }
}
