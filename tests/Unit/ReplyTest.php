<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ReplyTest extends TestCase
{

    use DatabaseMigrations;


    public function test_it_has_an_owner()
    {
        $reply = create('App\Reply');

        $this->assertInstanceOf('App\User',$reply->owner);
    }

    public function test_it_knows_if_it_was_just_published()
    {
        $reply = create('App\Reply');

        $this->assertTrue($reply->wasJustPublished());

        $reply->created_at = Carbon::now()->subMonth();

        $this->assertFalse($reply->wasJustPublished());
    }

    public function test_it_can_detect_all_mentioned_users_in_the_body()
    {
        $reply = create('App\Reply',[
            'body' => '@JaneDoe wants to talk to @JohnDoe'
            ]);

        $this->assertEquals(['JaneDoe','JohnDoe'], $reply->mentionedUsers());
    }
}
