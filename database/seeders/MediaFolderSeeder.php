<?php
// database/seeders/MediaFolderSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MediaFolder;

class MediaFolderSeeder extends Seeder
{
    public function run()
    {
        // Creates a “Default” root folder if it doesn’t already exist
        MediaFolder::firstOrCreate(
            ['name' => 'Default', 'parent_id' => null],
            ['order' => 0]
        );
    }
}
