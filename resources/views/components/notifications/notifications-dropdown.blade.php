@auth
    <li class="nav-item dropdown">
        <a class="nav-link position-relative" href="#" data-bs-toggle="dropdown">
            <i class="fas fa-bell fa-lg"></i>
            @if (auth()->user()->unreadNotifications->count())
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    {{ auth()->user()->unreadNotifications->count() > 99 ? '99+' : auth()->user()->unreadNotifications->count() }}
                    <span class="visually-hidden">unread notifications</span>
                </span>
            @endif
        </a>
        <div class="dropdown-menu dropdown-menu-end shadow-sm" style="width: 320px; max-height: 400px; overflow-y: auto;">
            <div class="d-flex justify-content-between align-items-center px-3 py-2 bg-light border-bottom">
                <h6 class="mb-0">Notifications</h6>
                @if (auth()->user()->unreadNotifications->count())
                    <a href="{{ route('notifications.markAllRead') }}" class="text-decoration-none small">
                        Mark all as read
                    </a>
                @endif
            </div>

            @forelse(auth()->user()->notifications->take($maxNotifications) as $notification)
                <a class="dropdown-item py-2 border-bottom"
                    href="{{ route('notifications.markAsRead', $notification->id) }}">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3 notification-icon">
                            <i class="{{ $notification->data['icon'] ?? 'fas fa-bell' }} fa-lg text-primary"></i>
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <p class="mb-0 text-truncate @if ($notification->unread()) fw-bold @endif">
                                {{ $notification->data['message'] }}
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                @if (isset($notification->data['quantity']) && isset($notification->data['reorder_level']))
                                    <span class="badge bg-warning text-dark">
                                        {{ $notification->data['quantity'] }}/{{ $notification->data['reorder_level'] }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="dropdown-item text-center py-4">
                    <i class="fas fa-check-circle text-success mb-2" style="font-size: 2rem;"></i>
                    <p class="mb-0 text-muted">No new notifications</p>
                </div>
            @endforelse

            @if (auth()->user()->notifications->count() > 5)
                <div class="dropdown-divider my-0"></div>
                <div class="text-center text-muted small py-2 bg-light">
                    {{ auth()->user()->notifications->count() - 5 }} more notification(s)
                </div>
            @endif

            <div class="dropdown-divider my-0"></div>
            <a class="dropdown-item text-center py-2" href="{{ route('notifications.index') }}">
                <i class="fas fa-list-ul me-1"></i> View All Notifications
            </a>
        </div>
    </li>
@endauth
