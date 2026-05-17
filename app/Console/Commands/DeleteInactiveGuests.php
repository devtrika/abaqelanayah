<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DeleteInactiveGuests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'guests:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove inactive guest users who have not placed any orders';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $cutoffDate = now()->subHours(24);

        $inactiveGuests = \App\Models\User::where('is_guest', true)
            ->where('created_at', '<', $cutoffDate)
            ->whereDoesntHave('orders')
            ->get();

        $count = $inactiveGuests->count();

        foreach ($inactiveGuests as $guest) {
            $guest->delete();
        }

        $this->info("Cleaned up {$count} inactive guest accounts.");
    }
}
