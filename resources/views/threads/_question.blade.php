{{-- Editing the question. --}}
<div class="card mb-3" v-if="editing">
    <div class="card-header">
        <div class="level">
            <input type="text" class="form-control" v-model="form.title">
        </div>
    </div>
    <div class="card-body">
        <div class="form-group">
            <wysiwyg v-model="form.body" :value="form.body"></wysiwyg>
            {{-- <textarea class="form-control" rows="10" v-model="form.body"></textarea> --}}
        </div>
    </div>

    <div class="card-footer">
        <div class="level">
            <button class="btn btn-sm btn-primary" @click="update">Update</button>
            <button class="btn btn-sm btn-secondary ml-1" @click="resetForm">Cancel</button>

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
                posted: <span v-text="title"></span>
            </span>
        </div>
    </div>
    <div class="card-body" v-html="body"></div>

    <div class="card-footer" v-if="authorize('owns', thread)">
        <button class="btn btn-sm btn-primary" @click="editing = true">Edit</button>
    </div>
</div>