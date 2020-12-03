
@if (count($relations))
  <ul class="list-group mt-4 border-0 users-relation-list">
    @foreach ($relations as $relation)
      <li class="media">
        <div class="media-left">
          <a href="http://larabbs.test/users/3">
            <img class="media-object img-thumbnail mr-3" style="width: 52px; height: 52px;" src="{{ $relation->user->avatar }}" title="{{ $relation->user->avatar }}">
          </a>
        </div>
        <div class="media-body">
          <div class="media-heading mt-0 mb-1">
            <a href="{{ route('users.show', [$relation->follower_id]) }}" title="{{ $relation->follower_id }}">
              {{ $relation->user->name }}

            </a>
          </div>
          <small class="media-body meta text-secondary">
            {{ $relation->user->introduction }}
            <span> • </span>
            <i class="far fa-clock"></i>
            <span class="timeago" title="关注时间：{{ $relation->created_at }}">{{ $relation->created_at->diffForHumans() }}关注</span>
          </small>
        </div>
      </li>
      <hr>
    @endforeach
  </ul>

@else
  <div class="empty-block">暂无数据 ~_~ </div>
@endif
{{-- 分页 --}}
<div class="mt-4 pt-1">
  {!! $relations->render() !!}
</div>
