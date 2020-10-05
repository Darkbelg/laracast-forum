<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProfileTest extends TestCase
{
     use RefreshDatabase;

     public function test_a_user_has_a_profile()
     {
         $user = create("App\User");

         $this->withoutExceptionHandling();
         $this->get("/profiles/{$user->name}")
            ->assertSee($user->name);
     }

     public function test_profiles_display_all_threads_created_by_the_associated_user()
     {
        $this->signIn();
        
        $thread = create("App\Thread",['user_id' => auth()->id()]);

         $this->withoutExceptionHandling();
         $this->get("/profiles/" . auth()->user()->name)
         ->assertSee($thread->title)
         ->assertSee($thread->body);

     }
}
