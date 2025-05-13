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
                <h6 class="mb-0">Unread Notifications</h6>
                @if (auth()->user()->unreadNotifications->count())
                    <form action="{{ route('notifications.markAllRead') }}" method="POST">
                        @csrf
                        @can('notifications.mark_all_read')
                            <button type="submit" class="btn btn-sm w-100">
                                Mark All Read
                            </button>
                        @endcan
                    </form>
                @endif
            </div>

            @forelse(auth()->user()->unreadNotifications->take($maxNotifications) as $notification)
                <a class="dropdown-item py-2 border-bottom" href="{{ route('notifications.index', $notification->id) }}">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            @if (Str::contains($notification->type, 'ReorderNotification'))
                                <i class="fas fa-boxes fa-lg text-warning"></i>
                            @elseif(Str::contains($notification->type, 'NewAppointmentNotification'))
                                <i class="fas fa-calendar-check fa-lg text-primary"></i>
                            @elseif(Str::contains($notification->type, 'InventoryExpirationNotification'))
                                <i class="fas fa-exclamation-triangle fa-lg text-danger"></i>
                            @else
                                <i class="fas fa-bell fa-lg"></i>
                            @endif
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <p class="mb-0 text-truncate fw-bold">
                                {{ $notification->data['message'] ?? 'New notification' }}
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                @if (isset($notification->data['quantity']) && isset($notification->data['reorder_level']))
                                    <span class="badge bg-warning text-dark">
                                        {{ $notification->data['quantity'] }}/{{ $notification->data['reorder_level'] }}
                                    </span>
                                @elseif (isset($notification->data['status']))
                                    <span class="badge bg-info text-dark">
                                        {{ $notification->data['status'] }}
                                    </span>
                                @elseif (Str::contains($notification->type, 'InventoryExpirationNotification') &&
                                        isset($notification->data['daysRemaining']))
                                    <span class="badge bg-danger text-white">
                                        {{ $notification->data['daysRemaining'] }} days
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="dropdown-item text-center py-4">
                    <i class="fas fa-check-circle text-success mb-2" style="font-size: 2rem;"></i>
                    <p class="mb-0 text-muted">No unread notifications</p>
                </div>
            @endforelse

            @if (auth()->user()->unreadNotifications->count() > $maxNotifications)
                <div class="dropdown-divider my-0"></div>
                <div class="text-center text-muted small py-2 bg-light">
                    {{ auth()->user()->unreadNotifications->count() - $maxNotifications }} more unread notification(s)
                </div>
            @endif

            <div class="dropdown-divider my-0"></div>
            <a class="dropdown-item text-center py-2" href="{{ route('notifications.index') }}">
                <i class="fas fa-list-ul me-1"></i> View All Notifications
            </a>
        </div>
    </li>
@endauth
