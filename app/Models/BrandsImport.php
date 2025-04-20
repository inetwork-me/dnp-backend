<?php

namespace App\Models;

use App\Models\Brand;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Str;

class BrandsImport implements ToCollection, WithHeadingRow, ToModel
{
    private $rows = 0;

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            Brand::create([
                'name' => $row['name'],
                'logo' => $this->downloadLogo($row['logo']),
                'slug' => preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $row['name'])).'-'.Str::random(5),
                'meta_title' => $row['meta_title'],
                'meta_description' => $row['meta_description'],
            ]);
        }

        flash(translate('Brands imported successfully'))->success();
        
    }

    public function model(array $row)
    {
        ++$this->rows;
    }

    public function downloadLogo($url)
    {
        try {
            $upload = new Upload;
            $upload->external_link = $url;
            $upload->type = 'image';
            $upload->save();

            return $upload->id;
        } catch (\Exception $e) {
        }
        return null;
    }
}
