<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DefaultShippingService;

class SetupDefaultShipping extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shipping:setup-defaults';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set up default shipping configuration for Aramex';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Setting up default shipping configuration...');
        
        try {
            $results = DefaultShippingService::setupDefaults();
            
            foreach ($results as $result) {
                $this->line("✓ {$result}");
            }
            
            $this->newLine();
            $this->info('Default shipping configuration completed successfully!');
            
            // Show current setup
            $this->showCurrentSetup();
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('Failed to setup default shipping: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
    
    private function showCurrentSetup()
    {
        $this->info('Current shipping methods:');
        
        $methods = \App\Models\ShippingMethod::with(['carrier', 'rates'])
            ->orderBy('sort_order')
            ->get();
            
        if ($methods->isEmpty()) {
            $this->warn('No shipping methods found');
            return;
        }
        
        foreach ($methods as $method) {
            $price = $method->rates->first()?->base_price ?? 'Live rates';
            $this->line("- {$method->name} ({$method->carrier->name}) - \${$price}");
        }
    }
}