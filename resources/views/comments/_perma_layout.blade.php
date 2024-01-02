@extends('layouts.app')

@section('title') Comments @endsection

@section('profile-title') Comment  @endsection

@section('content')

<h1>Comments on {!! ($comment->commentable_type == 'App\Models\User\UserProfile') ? $comment->commentable->user->displayName : $comment->commentable->displayName !!}</h1>
<h5>
    @if(isset($comment->child_id))
        <a href="{{ url('comment/').'/'. $comment->child_id }}" class="btn comment_submit btn-sm mr-2">See Parent</a>
        <a href="{{ url('comment/').'/'. $comment->topComment->id }}" class="btn comment_submit btn-sm mr-2">Go To Top Comment</a>
    @endif
</h5>

<hr>

<div class="comment_container perma py-3">
    <div class="comment_comments perma">
        <div class="d-flex mw-100 row mx-0" style="overflow: hidden;">
            @include('comments._perma_comments', ['comment' => $comment, 'limit' => 0, 'depth' => 0])
        </div>
    </div>
</div>


@endsection
