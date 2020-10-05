{{-- Editing the question. --}}
<div class="card mb-3" v-if="editing">
    <div class="card-header">
        <div class="level">
            <input type="text" value="{{ $thread->title }}" class="form-control">
        </div>
    </div>
    <div class="card-body">
        <div class="form-group">
            <textarea class="form-control" rows="10">{{ $thread->body }}</textarea>
        </div>
    </div>

    <div class="card-footer">
        <div class="level">
            <button class="btn btn-sm btn-primary" @click="editing = false">Update</button>
            <button class="btn btn-sm btn-secondary ml-1" @click="editing = false">Cancel</button>

            @can('update',$thread)
            <form action="{{ $thread->path() }}" method="post" class="ml-auto">
                {{ csrf_field() }}
                {{ method_field('DELETE') }}
                <button type="submit" class="btn btn-link">Delete Thread</button>
            </form>
            @endcan
        </div>
    </div>
</div>

{{-- Viewing the question. --}}
<div class="card mb-3" v-else>
    <div class="card-header">
        <div class="level">
            <img src="{{ $thread->creator->avatar_path }}" alt="{{ $thread->creator->name }}" width="25" height="25"
                class="mr-2">
            <span class="flex">
                <a href="{{ route('profile',$thread->creator->name)}}">{{$thread->creator->name}}</a>
                posted:
                {{ $thread->title }}
            </span>
        </div>
    </div>
    <div class="card-body">
        {{ $thread->body }}
    </div>

    <div class="card-footer">
        <button class="btn btn-sm btn-primary" @click="editing = true">Edit</button>
    </div>
</div>