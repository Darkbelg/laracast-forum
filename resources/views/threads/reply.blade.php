<div id="reply-{{ $reply->id }}" class="card mb-3">
    <div class="card-header">
        <div class="level">
            <h5 class="flex">
                <a href="{{ route('profile',$reply->owner->name)}}">{{ $reply->owner->name}}</a> said {{ $reply->created_at->diffForHumans() }} ...
            </h5>
            <div>
                <form action="/replies/{{$reply->id}}/favorites" method="post">
                    {{ csrf_field() }}
                    <button type="submit" class="btn btn-secondary" {{ $reply->isFavorited() ? 'disabled' : ''}}>
                        {{ $reply->favorites_count }} {{ Str::plural('Favorite', $reply->favorites_count )}}
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="card-body">
        {{ $reply->body }}
    </div>

    @can('update',$reply)
        <div class="card-footer">
            <form action="/replies/{{ $reply->id }}" method="post">
            {{ csrf_field() }}
            {{ method_field('DELETE') }}

            <button type="submit" class="btn btn-danger button-small">Delete</button>
            </form>
        </div>
    @endcan
</div>