<?php

namespace App\Http\Controllers;

use App\Thread;
use Illuminate\Http\Request;

class ThreadSubscriptionsController extends Controller
{
    public function store($channelIs, Thread $thread)
    {
        $thread->subscribe();
    }
}
