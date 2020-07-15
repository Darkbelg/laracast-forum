<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ParticipateInForumTest extends TestCase
{
    use DatabaseMigrations;

    public function test_unauthenticated_user_may_not_add_replies()
    {
        $this->expectException('Illuminate\Auth\AuthenticationException');

        $this->withoutExceptionHandling();
        $this->post('/threads/1/replies', []);
    }

    public function test_an_authenticated_user_may_participate_in_forum_threads()
    {
        $user = factory('App\User')->create();
        $this->be($user);

        $thread = factory('App\Thread')->create();

        $reply = factory('App\Reply')->make();
        $this->withoutExceptionHandling();
        $this->post($thread->path() . '/replies', $reply->toArray());

        $this->get($thread->path())
            ->assertSee($reply->body);
    }
}
