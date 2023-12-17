<?php

use Illuminate\Database\Seeder;

class FitnessUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'name' => 'Администратор',
                'login' => 'admin',
                'password' => 123456,
                'fit_role_id' => 1,
                'gym_id' => 1,
            ]
        ];

        foreach ($users as $user) {
            \App\Models\FitUser::create($user);
        }
    }
}
