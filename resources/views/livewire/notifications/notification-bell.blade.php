<div wire:poll.5s>
    <a href="#" class="nav-link" data-toggle="dropdown">
        <i class="far fa-bell"></i>
        @if($unreadNotificationsCount > 0)
            <span class="badge badge-warning navbar-badge">{{ $unreadNotificationsCount }}</span>
        @endif
    </a>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <span class="dropdown-item dropdown-header">{{ $unreadNotificationsCount }} Notifications</span>
        <div class="dropdown-divider"></div>
        @forelse($unreadNotifications as $notification)
            <a href="{{ $notification->data['url'] ?? '#' }}" class="dropdown-item" wire:click.prevent="markAsRead('{{ $notification->id }}')">
                <i class="fas fa-envelope mr-2"></i> {{ $notification->data['message'] }}
                <span class="float-right text-muted text-sm">{{ $notification->created_at->diffForHumans() }}</span>
            </a>
        @empty
            <a href="#" class="dropdown-item">
                <i class="fas fa-bell-slash mr-2"></i> No new notifications
            </a>
        @endforelse
        <div class="dropdown-divider"></div>
        <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
    </div>
</div>