<?php

namespace App\Console\Commands;

use App\Http\Models\Province;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SyncProvinces extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:provinces';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Province Data From External API (OPEN API)';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    
    public function handle()
    {
        $response = Http::get('https://open-api.my.id/api/wilayah/provinces');

        if ($response->successful()) {
            $provinces = $response->json();

            foreach ($provinces as $province) {
                Province::updateOrCreate([
                    'code' => $province['id'],
                    'name' => $province['name'],
                ]);
            }
            
            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Provinsi berhasil didapatkan.',
                'data' => $provinces,
            ], 200);

            $this->info('Provinces data synced succesfully');
        } else {
            
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data Provinsi.'
            ]);

            $this->info('Failed to fetch provinces data');
        }
    }
}
