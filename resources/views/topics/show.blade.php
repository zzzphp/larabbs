@extends('layouts.app')

@section('title', $topic->title)
@section('description', $topic->excerpt)

@section('content')

  <div class="row">

    <div class="col-lg-3 col-md-3 hidden-sm hidden-xs author-info">
      <div class="card ">
        <div class="card-body">
          <div class="text-center">
            作者：{{ $topic->user->name }}
          </div>
          <hr>
          <div class="media">
            <div align="center">
              <a href="{{ route('users.show', $topic->user->id) }}">
                <img class="thumbnail img-fluid" src="{{ $topic->user->avatar }}" width="300px" height="300px">
              </a>
            </div>
          </div>
        </div>
        @guest
          @else
        <div class="follow" align="center">
          @if($follow)
            <form action="{{ route('users_relations.destroy', $follow->id) }}" method="POST" onsubmit="return confirm('您确定要取消关注吗？');" accept-charset="UTF-8">
              <input type="hidden" name="follower_id" value="{{ $topic->user_id }}">
              {{ csrf_field() }}
              {{ method_field('DELETE') }}
              <button type="submit" class="btn btn-outline-secondary"><i class="far fa-heart"></i>&nbsp;已关注</button>
            </form>
          @else
            @if(\Illuminate\Support\Facades\Auth::id() !== $topic->user_id)
            <form action="{{ route('users_relations.store') }}" method="POST" accept-charset="UTF-8">
              <input type="hidden" name="follower_id" value="{{ $topic->user_id }}">
              {{ csrf_field() }}
              <button type="submit" class="btn btn-outline-secondary"><i class="far fa-heart"></i>&nbsp;关注Ta</button>
            </form>
              @endif
          @endif
        </div>
          @endguest
      </div>
    </div>

    <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 topic-content">
      <div class="card">
        <div class="card-body">
          <h1 class="text-center mt-3 mb-3">
            {{ $topic->title }}
          </h1>

          <div class="article-meta text-center text-secondary">
            {{ $topic->created_at->diffForHumans() }}
            ⋅
            <i class="far fa-comment"></i>
            {{ $topic->reply_count }}
          </div>

          <div class="topic-body mt-4 mb-4">
            {!! $topic->body !!}
          </div>

          @can('update', $topic)
            <div class="operate">
              <hr>
              <a href="{{ route('topics.edit', $topic->id) }}" class="btn btn-outline-secondary btn-sm" role="button">
                <i class="far fa-edit"></i> 编辑
              </a>
              <form action="{{ route('topics.destroy', $topic->id) }}" method="post"
                    style="display: inline-block;"
                    onsubmit="return confirm('您确定要删除吗？');">
                {{ csrf_field() }}
                {{ method_field('DELETE') }}
                <button type="submit" class="btn btn-outline-secondary btn-sm">
                  <i class="far fa-trash-alt"></i> 删除
                </button>
              </form>
            </div>
          @endcan

        </div>
      </div>

      {{-- 用户回复列表 --}}
      <div class="card topic-reply mt-4">
        <div class="card-body">
          @includeWhen(Auth::check(), 'topics._reply_box', ['topic' => $topic])
          @include('topics._reply_list', ['replies' => $topic->replies()->with('user')->get()])
        </div>
      </div>

    </div>
  </div>
@stop
