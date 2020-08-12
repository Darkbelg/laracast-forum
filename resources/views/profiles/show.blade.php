@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div>
                <h1>
                    {{ $profileUser->name }}
                </h1>

                @can('update', $profileUser)
                <form action="{{ route('avatar', $profileUser) }}" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}

                    <input type="file" name="avatar" id="avatar">

                    <button type="submit" class="btn btn-primary">Add Avatar</button>
                </form>
                @endcan
                <img src="{{ '/storage/' . $profileUser->avatar_path }}" alt="avatar" width="75" height="75">
            </div>
            @forelse ($activities as $date => $activity)
            <h3 class="header">{{ $date }}</h3>
            @foreach ($activity as $record)
            @if (view()->exists("profiles.activities.{$record->type}"))
            @include ("profiles.activities.{$record->type}",['activity' => $record])
            @endif
            @endforeach
            @empty
            <p>There is no activity for this user yet.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection