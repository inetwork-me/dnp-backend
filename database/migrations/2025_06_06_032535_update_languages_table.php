<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateLanguagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration will:
     *  1. Create the `languages` table if it does not exist at all.
     *  2. If it does exist, ensure it has exactly these columns with the correct type/default:
     *      - id            (bigIncrements)
     *      - code          (string, unique, not null)
     *      - name          (string, unique, not null)
     *      - app_lang_code (string, unique, not null)
     *      - rtl           (boolean, default=false)
     *      - status        (boolean, default=true)
     *      - is_default    (boolean, default=false)
     *      - created_at    (timestamp)
     *      - updated_at    (timestamp)
     */
    public function up()
    {
        // 1) If the table does _not_ exist, create it from scratch:
        if (!Schema::hasTable('languages')) {
            Schema::create('languages', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('code')->unique();
                $table->string('name')->unique();
                $table->string('app_lang_code')->unique();
                $table->boolean('rtl')->default(false);
                $table->boolean('status')->default(true);
                $table->boolean('is_default')->default(false);
                $table->timestamps();
            });

            return;
        }

        // 2) If the table _does_ exist, modify it as needed:
        Schema::table('languages', function (Blueprint $table) {
            // Note: Laravel's Blueprint does not allow "if column type is wrong, change it" directly.
            // We can use raw SQL or doctrine/dbal to alter column types if absolutely necessary.
            // For simplicity, we will:
            //   • Add missing columns if they don't exist.
            //   • If they do exist but have the wrong default or are not nullable, we alter them.
            //
            // To alter column types in-place, you must have "doctrine/dbal" installed:
            //   composer require doctrine/dbal
            //
            // Then you can do $table->boolean('rtl')->default(false)->change(); and so on.
        });

        // 2A) Add any _missing_ columns to languages:
        if (!Schema::hasColumn('languages', 'code')) {
            Schema::table('languages', function (Blueprint $table) {
                $table->string('code')->unique()->after('id');
            });
        }

        if (!Schema::hasColumn('languages', 'name')) {
            Schema::table('languages', function (Blueprint $table) {
                $table->string('name')->unique()->after('code');
            });
        }

        if (!Schema::hasColumn('languages', 'app_lang_code')) {
            Schema::table('languages', function (Blueprint $table) {
                $table->string('app_lang_code')->unique()->after('name');
            });
        }

        if (!Schema::hasColumn('languages', 'rtl')) {
            Schema::table('languages', function (Blueprint $table) {
                $table->boolean('rtl')->default(false)->after('app_lang_code');
            });
        }

        if (!Schema::hasColumn('languages', 'status')) {
            Schema::table('languages', function (Blueprint $table) {
                $table->boolean('status')->default(true)->after('rtl');
            });
        }

        if (!Schema::hasColumn('languages', 'is_default')) {
            Schema::table('languages', function (Blueprint $table) {
                $table->boolean('is_default')->default(false)->after('status');
            });
        }

        if (!Schema::hasColumn('languages', 'created_at')) {
            Schema::table('languages', function (Blueprint $table) {
                $table->timestamps();
            });
        } else {
            // If created_at exists but updated_at is missing, add updated_at
            if (!Schema::hasColumn('languages', 'updated_at')) {
                Schema::table('languages', function (Blueprint $table) {
                    $table->timestamp('updated_at')->nullable()->after('created_at');
                });
                // Fill existing rows' updated_at with created_at value (so it’s not NULL).
                DB::table('languages')
                    ->whereNull('updated_at')
                    ->update(['updated_at' => DB::raw('created_at')]);
            }
        }

        // 2B) Now, if you have Doctrine DBAL installed, you can “change” columns that exist but have the wrong type/default.
        // For example, if someone created `rtl` as an integer or nullable, you can force it to boolean NOT NULL default(false):
        //
        //   Schema::table('languages', function (Blueprint $table) {
        //       $table->boolean('rtl')->default(false)->change();
        //       $table->boolean('status')->default(true)->change();
        //       $table->boolean('is_default')->default(false)->change();
        //       $table->string('app_lang_code')->unique()->change();
        //       // etc.
        //   });
        //
        // However, note that for `->change()` to work, you must first run:
        //    composer require doctrine/dbal
        //
        // Then you can uncomment and run the `->change()` lines to enforce correct types/defaults.

        if (
            class_exists(\Doctrine\DBAL\Driver\PDOSqlite\Driver::class) ||
            class_exists(\Doctrine\DBAL\Driver\PDOMySql\Driver::class) ||
            class_exists(\Doctrine\DBAL\Driver\PDOPgSql\Driver::class)
        ) {
            // If Doctrine DBAL is installed, enforce the column types/defaults:
            Schema::table('languages', function (Blueprint $table) {
                $table->string('code')->unique()->change();
                $table->string('name')->unique()->change();
                $table->string('app_lang_code')->unique()->change();
                $table->boolean('rtl')->default(false)->change();
                $table->boolean('status')->default(true)->change();
                $table->boolean('is_default')->default(false)->change();
                // Ensure timestamps exist as timestamps:
                $table->timestamp('created_at')->nullable(false)->change();
                $table->timestamp('updated_at')->nullable(false)->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * In the “down” method, you can decide whether to remove columns or just leave them.
     * Usually, for a “fix‐up” migration, you might not want to drop columns on rollback.
     * We’ll drop only the columns that this migration explicitly added, if they didn’t exist before.
     */
    public function down()
    {
        if (!Schema::hasTable('languages')) {
            return;
        }

        // Only drop columns if they exist and if they were added by this migration.
        // (Dropping is optional—comment out anything you don’t want to revert.)
        if (Schema::hasColumn('languages', 'is_default')) {
            Schema::table('languages', function (Blueprint $table) {
                $table->dropColumn('is_default');
            });
        }

        if (Schema::hasColumn('languages', 'status')) {
            Schema::table('languages', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }

        if (Schema::hasColumn('languages', 'rtl')) {
            Schema::table('languages', function (Blueprint $table) {
                $table->dropColumn('rtl');
            });
        }

        if (Schema::hasColumn('languages', 'app_lang_code')) {
            Schema::table('languages', function (Blueprint $table) {
                $table->dropColumn('app_lang_code');
            });
        }

        if (Schema::hasColumn('languages', 'name')) {
            Schema::table('languages', function (Blueprint $table) {
                $table->dropColumn('name');
            });
        }

        if (Schema::hasColumn('languages', 'code')) {
            Schema::table('languages', function (Blueprint $table) {
                $table->dropColumn('code');
            });
        }

        // If you added timestamps manually, drop them:
        if (Schema::hasColumn('languages', 'created_at')) {
            Schema::table('languages', function (Blueprint $table) {
                $table->dropColumn(['created_at', 'updated_at']);
            });
        }

        // If you created the table from scratch in this migration, drop it entirely:
        // (Uncomment if you want full rollback to remove the table.)
        //
        // Schema::dropIfExists('languages');
    }
}
