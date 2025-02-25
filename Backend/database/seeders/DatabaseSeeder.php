<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $sql = File::get(database_path('stundusaraksts.sql'));
        DB::unprepared($sql);
    }
}