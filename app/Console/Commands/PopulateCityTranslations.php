<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\City;
use App\Models\CityTranslation;

class PopulateCityTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'city:populate-translations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate city translations with Arabic names';

    /**
     * Egyptian city name translations
     */
    protected $translations = [
        'Cairo' => 'القاهرة',
        'Giza' => 'الجيزة',
        'Alexandria' => 'الإسكندرية',
        'Qalyubia' => 'القليوبية',
        'Banha' => 'بنها',
        'Mansoura' => 'المنصورة',
        'Tanta' => 'طنطا',
        'Zagazig' => 'الزقازيق',
        'Damietta' => 'دمياط',
        'Ismailia' => 'الإسماعيلية',
        'Suez' => 'السويس',
        'Port Said' => 'بورسعيد',
        'Aswan' => 'أسوان',
        'Luxor' => 'الأقصر',
        'Sohag' => 'سوهاج',
        'Qena' => 'قنا',
        'Asyut' => 'أسيوط',
        'Minya' => 'المنيا',
        'Hurghada' => 'الغردقة',
        'Arish' => 'العريش',
        'Fayoum' => 'الفيوم',
        'Beni Suef' => 'بني سويف',
        'Kafr El Sheikh' => 'كفر الشيخ',
        'Monufia' => 'المنوفية',
        'Gharbia' => 'الغربية',
        'Dakahlia' => 'الدقهلية',
        'Sharqia' => 'الشرقية',
        'Beheira' => 'البحيرة',
        'New Valley' => 'الوادي الجديد',
        'Matruh' => 'مطروح',
        'Red Sea' => 'البحر الأحمر',
        'North Sinai' => 'شمال سيناء',
        'South Sinai' => 'جنوب سيناء',
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Populating city translations...');

        $cities = City::all();
        $updated = 0;
        $created = 0;

        foreach ($cities as $city) {
            $cityName = $city->name;

            // Update English translation
            $enTranslation = CityTranslation::updateOrCreate(
                ['city_id' => $city->id, 'lang' => 'en'],
                ['name' => $cityName]
            );

            if ($enTranslation->wasRecentlyCreated) {
                $created++;
            } else {
                $updated++;
            }

            // Update Arabic translation
            $arabicName = $this->translations[$cityName] ?? $cityName;

            $arTranslation = CityTranslation::updateOrCreate(
                ['city_id' => $city->id, 'lang' => 'ar'],
                ['name' => $arabicName]
            );

            if ($arTranslation->wasRecentlyCreated) {
                $created++;
            } else {
                $updated++;
            }

            $this->line("Processed: {$cityName} -> {$arabicName}");
        }

        $this->info("\nCompleted!");
        $this->info("Created: {$created} translations");
        $this->info("Updated: {$updated} translations");

        return 0;
    }
}
