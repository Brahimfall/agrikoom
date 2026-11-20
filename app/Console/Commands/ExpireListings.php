<?php

namespace App\Console\Commands;

use App\Models\Listing;
use Illuminate\Console\Command;

class ExpireListings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'listings:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire active listings whose expiration date has passed';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $count = Listing::query()
            ->where('status', 'active')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->update(['status' => 'expired']);

        $this->info("Expired {$count} listings.");

        return self::SUCCESS;
    }
}
