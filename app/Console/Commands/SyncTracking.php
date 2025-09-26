<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Shipment;
use App\Services\CarrierTrackingService;

class SyncTracking extends Command
{
    protected $signature = 'tracking:sync {--days=7 : Only sync shipments updated within the last N days}';
    protected $description = 'Fetch and update tracking info for recent shipments';

    public function handle(CarrierTrackingService $service)
    {
        $days = (int)$this->option('days');
        $since = now()->subDays($days);
        $this->info("Syncing shipments updated since {$since->toDateTimeString()}...");

        $q = Shipment::query()
            ->whereNotNull('tracking_number')
            ->where(function($q) use ($since) {
                $q->whereNull('delivered_at')
                  ->orWhere('updated_at', '>=', $since);
            })
            ->orderBy('updated_at', 'desc');

        $count = 0;
        $q->chunk(50, function($chunk) use (&$count, $service) {
            foreach ($chunk as $shipment) {
                $service->fetchAndUpdate($shipment);
                $count++;
                $this->line("Updated shipment #{$shipment->id} ({$shipment->carrier} {$shipment->tracking_number})");
            }
        });

        $this->info("Done. Updated {$count} shipment(s).");
        return Command::SUCCESS;
    }
}
