<?php

// database/migrations/2025_06_19_000004_rename_name_to_label_on_forms.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RenameNameToLabelOnForms extends Migration
{
    public function up()
    {
        Schema::table('forms', function (Blueprint $table) {
            // add new JSON column
            $table->json('label')->after('id')->nullable();
            // copy old data into it if you want
            DB::table('forms')->get()->each(function ($form) {
                DB::table('forms')
                    ->where('id', $form->id)
                    ->update(['label' => json_encode(['en' => $form->name, 'ar' => ''])]);
            });
            // drop old name column
            $table->dropColumn('name');
        });
    }

    public function down()
    {
        Schema::table('forms', function (Blueprint $table) {
            $table->string('name')->after('id')->nullable();
            // optionally reverse-copy
            DB::table('forms')->get()->each(function ($form) {
                $label = json_decode($form->label, true);
                DB::table('forms')
                    ->where('id', $form->id)
                    ->update(['name' => $label['en'] ?? '']);
            });
            $table->dropColumn('label');
        });
    }
}
