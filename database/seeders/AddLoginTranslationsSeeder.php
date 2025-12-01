<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddLoginTranslationsSeeder extends Seeder
{
    public function run(): void
    {
        $translations = [
            [
                'lang' => 'en',
                'lang_key' => 'invalid_email_or_password',
                'lang_value' => 'Invalid email or password',
            ],
            [
                'lang' => 'ar',
                'lang_key' => 'invalid_email_or_password',
                'lang_value' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة',
            ],
        ];

        foreach ($translations as $translation) {
            DB::table('translations')->updateOrInsert(
                ['lang' => $translation['lang'], 'lang_key' => $translation['lang_key']],
                ['lang_value' => $translation['lang_value']]
            );
        }
    }
}
