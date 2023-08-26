<?php

use Illuminate\Database\Seeder;

class ChecklistTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('checklists')->truncate();

        DB::table('checklists')->insert([
            'name' => 'Test checklist 1',
            'description' => 'only for test',
            'status' => 0,
            'created_at' => Date('Y-m-d H:i:s'),
            'updated_at' => Date('Y-m-d H:i:s')
        ]);
    }
}
