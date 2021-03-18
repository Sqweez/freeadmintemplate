<?php

use Illuminate\Database\Seeder;

class TagsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tags = \App\Tag::select(['id', 'name']);
        $tags->each(function ($tag) {
            \App\Tag::find($tag['id'])->update(['name' => \Illuminate\Support\Str::lower($tag['name'])]);
        });
    }
}
