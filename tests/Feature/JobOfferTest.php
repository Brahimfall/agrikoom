<?php

namespace Tests\Feature;

use App\Models\JobOffer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobOfferTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_offers_page_is_displayed(): void
    {
        JobOffer::query()->create([
            'title' => 'Technicien agricole',
            'slug' => 'technicien-agricole',
            'company' => 'AGROPONIC SARL',
            'location' => 'Dakar',
            'summary' => 'Offre agricole de test.',
            'status' => 'active',
        ]);

        $response = $this->get(route('jobs.index'));

        $response->assertOk();
        $response->assertSee("Offres d'emploi AgriKoom", false);
        $response->assertSee('Technicien agricole');
    }

    public function test_job_offer_detail_is_displayed(): void
    {
        $job = JobOffer::query()->create([
            'title' => 'Chef de secteur junior',
            'slug' => 'chef-de-secteur-junior',
            'company' => 'SOBOA',
            'location' => 'Dakar',
            'summary' => 'Offre commerciale de test.',
            'profile' => 'Permis obligatoire.',
            'status' => 'active',
        ]);

        $response = $this->get(route('jobs.show', $job));

        $response->assertOk();
        $response->assertSee('Chef de secteur junior');
        $response->assertSee('Permis obligatoire.');
    }

    public function test_marketplace_shows_job_offer_section(): void
    {
        JobOffer::query()->create([
            'title' => 'Vendeuse',
            'slug' => 'vendeuse',
            'company' => 'Activite agroalimentaire',
            'location' => 'Keur Massar',
            'summary' => 'Offre de vente de test.',
            'status' => 'active',
            'is_featured' => true,
        ]);

        $response = $this->get(route('listings.index'));

        $response->assertOk();
        $response->assertSee('Emploi agricole');
        $response->assertSee('Vendeuse');
    }
}
