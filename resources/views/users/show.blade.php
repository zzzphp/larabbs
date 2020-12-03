@extends('layouts.app')

@section('title', $user->name . ' 的个人中心')

@section('content')
  <div class="row">

    <div class="col-lg-3 col-md-3 hidden-sm hidden-xs user-info">
      <div class="card ">
        <img class="card-img-top" src="{{ $user->avatar }}" alt="{{ $user->name }}">
        <div class="card-body">
          <h5><strong>个人简介</strong></h5>
          <p>{{ $user->introduction }}</p>
          <hr>
          <h5><strong>注册于</strong></h5>
          <p>{{ $user->created_at->diffForHumans() }}</p>
          <h5><strong>最后活跃</strong></h5>
          <p title="{{  $user->last_actived_at }}">{{ $user->last_actived_at->diffForHumans() }}</p>
          @guest
          @else

              @if($follow)
                <form action="{{ route('users_relations.destroy', $follow->id) }}" method="POST" onsubmit="return confirm('您确定要取消关注吗？');" accept-charset="UTF-8">
                  <input type="hidden" name="follower_id" value="{{ $user->id }}">
                  {{ csrf_field() }}
                  {{ method_field('DELETE') }}
                  <button type="submit" class="btn btn-outline-secondary"><i class="far fa-heart"></i>&nbsp;已关注</button>
                </form>
              @else
                @if(\Illuminate\Support\Facades\Auth::user()->id !== $user->id)
                <form action="{{ route('users_relations.store') }}" method="POST" accept-charset="UTF-8">
                  <input type="hidden" name="follower_id" value="{{ $user->id }}">
                  {{ csrf_field() }}
                  <button type="submit" class="btn btn-outline-secondary"><i class="far fa-heart"></i>&nbsp;关注Ta</button>
                </form>
                @endif
              @endif
              @endguest
        </div>
      </div>
    </div>
    <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
      <div class="card ">
        <div class="card-body">
          <h1 class="mb-0" style="font-size:22px;">{{ $user->name }} <small>{{ $user->email }}</small></h1>
        </div>
      </div>
      <hr>

      {{-- 用户发布的内容 --}}
      <div class="card ">
        <div class="card-body">
          <ul class="nav nav-tabs">
            <li class="nav-item">
              <a class="nav-link bg-transparent {{ active_class(if_query('tab', null)) }}" href="{{ route('users.show', $user->id) }}">
                Ta 的话题
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link bg-transparent {{ active_class(if_query('tab', 'replies')) }}" href="{{ route('users.show', [$user->id, 'tab' => 'replies']) }}">
                Ta 的回复
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link bg-transparent {{ active_class(if_query('tab', 'users_relations')) }}" href="{{ route('users.show', [$user->id, 'tab' => 'users_relations']) }}">
                关注用户
              </a>
            </li>
          </ul>
          @if (if_query('tab', 'replies'))
            @include('users._replies', ['replies' => $user->replies()->with('topic')->recentreplied()->paginate(5)])
          @elseif(if_query('tab', 'users_relations'))
            @include('users._relations', ['relations' => $user->users_relations()->recent()->with('user')->paginate(5)])
            @else
            @include('users._topics', ['topics' => $user->topics()->recent()->paginate(5)])
          @endif
        </div>
      </div>

    </div>
  </div>
@stop
