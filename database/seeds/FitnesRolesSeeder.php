<?php

use Illuminate\Database\Seeder;

class FitnesRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            1 => 'Руководитель',
            2 => 'Отдел продаж',
            3 => 'Администратор',
            4 => 'Бармен',
            5 => 'Инструктор'
        ];

        foreach ($roles as $key => $role) {
            \App\Models\FitRoles::create([
                'id' => $key,
                'name' => $role
            ]);
        }
    }
}
