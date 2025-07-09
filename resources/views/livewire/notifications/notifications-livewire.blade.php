<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Your Notifications</h3>
                    <div class="card-tools">
                        <button wire:click="markAllAsRead" class="btn btn-sm btn-primary">
                            Mark All as Read <x-spinner for="markAllAsRead" />
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if ($notifications->isEmpty())
                        <p class="p-3">No notifications to display.</p>
                    @else
                        <ul class="list-group list-group-flush" wire:poll.10s="loadNotifications">
                            @foreach ($notifications as $notification)
                                <li class="list-group-item @if (is_null($notification->read_at)) bg-light @endif">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <p class="mb-1">{!! $notification->data['message'] !!}</p>
                                            <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                        </div>
                                        <div>
                                            @if (!is_null($notification->data) && isset($notification->data['url']))
                                                <a href="{{ $notification->data['url'] }}" class="btn btn-sm btn-info mr-2">View</a>
                                            @endif
                                            @if (is_null($notification->read_at))
                                                <button wire:click="markAsRead({{ $notification->id }})" class="btn btn-sm btn-success">
                                                    Mark as Read <x-spinner for="markAsRead({{ $notification->id }})" />
                                                </button>
                                            @else
                                                <span class="badge badge-secondary">Read</span>
                                            @endif
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>