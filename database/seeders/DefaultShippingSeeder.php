<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DefaultShippingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Setting up default shipping configuration...');
        
        try {
            $results = \App\Services\DefaultShippingService::setupDefaults();
            
            foreach ($results as $result) {
                $this->command->line("✓ {$result}");
            }
            
            $this->command->info('✅ Default shipping setup completed!');
            
        } catch (\Exception $e) {
            $this->command->error('❌ Failed to setup shipping: ' . $e->getMessage());
        }
    }
}
