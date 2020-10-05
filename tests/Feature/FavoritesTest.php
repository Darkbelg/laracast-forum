<?php

namespace Tests\Feature;

use Exception;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FavoritesTest extends TestCase
{

    use RefreshDatabase;

    public function test_a_guest_can_not_favorite_anything()
    {
        $this->post('replies/1/favorites')
            ->assertRedirect('/login');
    }


    public function test_an_authenticated_user_can_favorite_any_reply()
    {
        $this->signIn();

        $this->withoutExceptionHandling();

        $reply = create('App\Reply');

        $this->post('replies/' . $reply->id . '/favorites');

        $this->assertCount(1, $reply->favorites);
    }

    public function test_an_authenticated_user_can_unfavorite_a_reply()
    {
        $this->signIn();

        $this->withoutExceptionHandling();

        $reply = create('App\Reply');

        $this->post('replies/' . $reply->id . '/favorites');

        $this->assertCount(1, $reply->favorites);

        $this->delete('replies/' . $reply->id . '/favorites');

        $this->assertCount(0, $reply->fresh()->favorites);
    }

    public function test_an_authenticated_user_may_only_favorite_a_reply_once()
    {

        $this->withoutExceptionHandling();
        $this->signIn();

        $reply = create('App\Reply');

        try {
            $this->post('replies/' . $reply->id . '/favorites');
            $this->post('replies/' . $reply->id . '/favorites');
        } catch (Exception $e) {
            $this->fail('Did not expect to insert the same record twice.');
        }

        $this->assertCount(1, $reply->favorites);
    }
}
