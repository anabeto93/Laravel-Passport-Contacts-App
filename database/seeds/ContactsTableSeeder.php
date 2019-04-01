<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class ContactsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(app()->environment('local')) {
            factory(\App\Models\Contact::class,100)->create()->each(function ($contact) {
                Log::info('Contact Created'); Log::debug($contact);
            });
        }
    }
}
