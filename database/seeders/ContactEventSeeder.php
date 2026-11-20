<?php

namespace Database\Seeders;

use App\Models\ContactEvent;
use App\Models\ContactRequest;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ContactEventSeeder extends Seeder
{
    public function run(): void
    {
        $listings = Listing::with('user')->get();
        if ($listings->isEmpty()) {
            return;
        }

        $users = User::all();
        $channels = ['whatsapp', 'whatsapp', 'whatsapp', 'phone', 'phone', 'form'];

        // If we don't have contact events yet, seed realistic events over the past 60 days
        if (ContactEvent::count() === 0) {
            for ($daysAgo = 60; $daysAgo >= 0; $daysAgo--) {
                // Number of events per day between 3 and 18, with higher numbers on recent days
                $eventsCount = rand(3, 10) + ($daysAgo < 7 ? rand(4, 10) : rand(0, 3));
                $dayDate = now()->subDays($daysAgo);

                for ($i = 0; $i < $eventsCount; $i++) {
                    $listing = $listings->random();
                    $channel = $channels[array_rand($channels)];
                    $user = rand(0, 1) ? $users->random() : null;

                    $createdAt = (clone $dayDate)->setHour(rand(7, 21))->setMinute(rand(0, 59))->setSecond(rand(0, 59));

                    ContactEvent::create([
                        'listing_id' => $listing->id,
                        'seller_id' => $listing->user_id,
                        'user_id' => $user?->id,
                        'channel' => $channel,
                        'ip_address' => '197.'.rand(10, 255).'.'.rand(1, 255).'.'.rand(1, 255),
                        'user_agent' => 'Mozilla/5.0 (Mobile; Android; AgriKoom)',
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]);

                    // Also create a few formal ContactRequests
                    if ($channel === 'form' || rand(0, 4) === 0) {
                        ContactRequest::create([
                            'listing_id' => $listing->id,
                            'buyer_id' => $user?->id,
                            'seller_id' => $listing->user_id,
                            'buyer_name' => $user?->name ?? 'Acheteur Louma '.rand(10, 99),
                            'buyer_phone' => '+221 77 '.rand(100, 999).' '.rand(10, 99).' '.rand(10, 99),
                            'message' => 'Bonjour, je suis très intéressé par votre annonce. Le produit est-il toujours disponible ?',
                            'contacted_at' => $createdAt,
                            'created_at' => $createdAt,
                            'updated_at' => $createdAt,
                        ]);
                    }
                }
            }
        }

        // Add a few users created recently (today, this week, this month) so the metrics look active
        $recentUsersCount = User::where('created_at', '>=', now()->subDays(7))->count();
        if ($recentUsersCount < 5) {
            $names = [
                ['Moussa Diouf', 'moussa.diouf'.rand(100,999).'@agrikoom.sn', '+221 77 123 45 67', 'Thiès', 'producer'],
                ['Fatou Ndiaye', 'fatou.ndiaye'.rand(100,999).'@agrikoom.sn', '+221 78 234 56 78', 'Saint-Louis', 'seller'],
                ['Ibrahima Sall', 'ibrahima.sall'.rand(100,999).'@agrikoom.sn', '+221 76 345 67 89', 'Dakar', 'buyer'],
                ['Awa Sarr', 'awa.sarr'.rand(100,999).'@agrikoom.sn', '+221 70 456 78 90', 'Kaolack', 'both'],
                ['Cheikh Ba', 'cheikh.ba'.rand(100,999).'@agrikoom.sn', '+221 77 567 89 01', 'Louga', 'producer'],
                ['Aminata Diallo', 'aminata.diallo'.rand(100,999).'@agrikoom.sn', '+221 78 678 90 12', 'Ziguinchor', 'buyer'],
            ];

            foreach ($names as $idx => $u) {
                $days = $idx % 4; // today, 1 day ago, 2 days ago, 3 days ago
                User::create([
                    'name' => $u[0],
                    'email' => $u[1],
                    'phone' => $u[2],
                    'location' => $u[3],
                    'actor_type' => $u[4],
                    'phone_verified_at' => rand(0, 1) ? now()->subDays($days) : null,
                    'password' => bcrypt('password'),
                    'created_at' => now()->subDays($days)->setHour(rand(8, 18)),
                    'updated_at' => now()->subDays($days),
                ]);
            }
        }
    }
}

