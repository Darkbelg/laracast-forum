<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SubsrcribeToThreadsTest extends TestCase
{

    use DatabaseMigrations;

    public function test_a_user_can_subscribe_to_threads()
    {

        $this->signIn();

        $thread = create('App\Thread');

        $this->withoutExceptionHandling();
        $this->post($thread->path() . '/subscriptions');

        $this->assertCount(1, $thread->fresh()->subscriptions);
    }

    public function test_a_user_can_unsubscribe_from_threads()
    {

        $this->signIn();

        $thread = create('App\Thread');

        $thread->subscribe();

        $this->withoutExceptionHandling();
        $this->delete($thread->path() . '/subscriptions');

        $this->assertCount(0, $thread->subscriptions);
    }
}
