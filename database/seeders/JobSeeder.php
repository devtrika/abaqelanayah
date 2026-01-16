<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Queue jobs for background processing
        
        DB::table('jobs')->insert([
            [
                'queue' => 'default',
                'payload' => json_encode([
                    'uuid' => \Illuminate\Support\Str::uuid(),
                    'displayName' => 'App\\Jobs\\SendNotification',
                    'job' => 'Illuminate\\Queue\\CallQueuedHandler@call',
                    'maxTries' => null,
                    'maxExceptions' => null,
                    'failOnTimeout' => false,
                    'backoff' => null,
                    'timeout' => null,
                    'retryUntil' => null,
                    'data' => [
                        'commandName' => 'App\\Jobs\\SendNotification',
                        'command' => serialize(new \stdClass())
                    ]
                ]),
                'attempts' => 0,
                'reserved_at' => null,
                'available_at' => now()->timestamp,
                'created_at' => now()->timestamp,
            ],
            [
                'queue' => 'emails',
                'payload' => json_encode([
                    'uuid' => \Illuminate\Support\Str::uuid(),
                    'displayName' => 'App\\Jobs\\SendEmail',
                    'job' => 'Illuminate\\Queue\\CallQueuedHandler@call',
                    'maxTries' => 3,
                    'maxExceptions' => null,
                    'failOnTimeout' => false,
                    'backoff' => null,
                    'timeout' => null,
                    'retryUntil' => null,
                    'data' => [
                        'commandName' => 'App\\Jobs\\SendEmail',
                        'command' => serialize(new \stdClass())
                    ]
                ]),
                'attempts' => 1,
                'reserved_at' => now()->subMinutes(5)->timestamp,
                'available_at' => now()->addMinutes(5)->timestamp,
                'created_at' => now()->subMinutes(10)->timestamp,
            ],
            [
                'queue' => 'processing',
                'payload' => json_encode([
                    'uuid' => \Illuminate\Support\Str::uuid(),
                    'displayName' => 'App\\Jobs\\ProcessPayment',
                    'job' => 'Illuminate\\Queue\\CallQueuedHandler@call',
                    'maxTries' => 5,
                    'maxExceptions' => null,
                    'failOnTimeout' => true,
                    'backoff' => [60, 120, 300],
                    'timeout' => 300,
                    'retryUntil' => null,
                    'data' => [
                        'commandName' => 'App\\Jobs\\ProcessPayment',
                        'command' => serialize(new \stdClass())
                    ]
                ]),
                'attempts' => 0,
                'reserved_at' => null,
                'available_at' => now()->addMinutes(2)->timestamp,
                'created_at' => now()->timestamp,
            ],
        ]);

        $this->command->info('Jobs seeded successfully!');
    }
}
