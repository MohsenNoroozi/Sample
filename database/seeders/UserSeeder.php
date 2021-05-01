<?php

namespace Database\Seeders;

use App\Models\EmailList;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $me = User::create([
            'first_name' => 'Mohsen',
            'last_name' => 'Noroozi',
        ]);
        EmailList::factory()->count(15)->create(['user_id' => $me->id]);

        User::factory(4)
            ->has(EmailList::factory()->count(2), 'lists')
            ->create();
    }
}
