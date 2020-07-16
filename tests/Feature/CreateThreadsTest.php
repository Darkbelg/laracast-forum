<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateThreadsTest extends TestCase
{

    use DatabaseMigrations;

    public function test_guests_may_not_create_threads()
    {
        $this->expectException('Illuminate\Auth\AuthenticationException');

        $thread = make('App\Thread');

        $this->withoutExceptionHandling();
        $this->post('/threads', $thread->toArray());
    }

    public function test_an_authenticated_user_can_create_new_forum_threads()
    {
        //$this->actingAs(create('App\User'));
        $this->signIn();

        $thread = make('App\Thread');
        $this->withoutExceptionHandling();

        $this->post('/threads', $thread->toArray());
        $this->get($thread->path())
            ->assertSee($thread->title)
            ->assertSee($thread->body);
    }
}
