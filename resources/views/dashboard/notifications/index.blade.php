@extends('layouts.master.master')
@section('title', 'Notifications')


@section('css')
<style>
    .notification-item:hover {
        background-color: #f8f9fa !important;
    }

    .notification-icon {
        font-size: 1.2rem;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }

    .icon-inventory {
        background-color: rgba(255, 193, 7, 0.2);
        color: #ff9800;
    }

    .icon-appointment {
        background-color: rgba(13, 110, 253, 0.2);
        color: var(--primary-color);
    }
</style>
@endsection

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Notifications</h5>
                @if($notifications->where('read_at', null)->count() > 0)
                    <form action="{{ route('notifications.markAllRead') }}" method="POST">
                        @csrf
                        @can('notifications.mark_all_read')
                        <button type="submit" class="btn btn-sm btn-outline-secondary">
                            Mark All as Read
                        </button>
                        @endcan
                    </form>
                @endif
            </div>
            <div class="card-body">
                @forelse($notifications as $notification)
                    <div class="notification-item p-3 mb-2 border-bottom {{ $notification->read_at ? 'bg-light' : 'bg-white' }}">
                        <div class="d-flex">
                            <div class="me-3">
                                @if(Str::contains($notification->type, 'ReorderNotification'))
                                    <div class="notification-icon icon-inventory">
                                        <i class="fas fa-boxes"></i>
                                    </div>
                                @elseif(Str::contains($notification->type, 'NewAppointmentNotification'))
                                    <div class="notification-icon icon-appointment">
                                        <i class="fas fa-calendar-check"></i>
                                    </div>
                                @else
                                    <div class="notification-icon">
                                        <i class="fas fa-bell"></i>
                                    </div>
                                @endif
                            </div>

                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <p class="mb-1 @if(!$notification->read_at) fw-semibold @endif">
                                            {{ $notification->data['message'] }}
                                        </p>

                                        @if(isset($notification->data['quantity']) && isset($notification->data['reorder_level']))
                                            <p class="mb-1 text-muted small">
                                                Current stock: <span class="badge bg-warning text-dark">
                                                    {{ $notification->data['quantity'] }}/{{ $notification->data['reorder_level'] }}
                                                </span>
                                            </p>
                                        @elseif(isset($notification->data['appointment_date']))
                                            <p class="mb-1 text-muted small">
                                                <strong>Patient:</strong> {{ $notification->data['patient_name'] ?? 'Not Recorded' }} |
                                                <strong>Service:</strong> {{ $notification->data['service_name'] ?? 'Not Recorded' }} |
                                                <span class="badge bg-info text-dark">{{ $notification->data['status'] }}</span>
                                            </p>
                                        @endif

                                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>

                                    @if(!$notification->read_at)
                                        <div>
                                            <a href="{{ route('notifications.markAsRead', $notification->id) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                Mark as Read
                                            </a>
                                        </div>
                                    @endif
                                </div>


                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="fas fa-bell-slash fa-4x text-muted mb-3"></i>
                        <p class="text-center text-muted">No notifications found</p>
                    </div>
                @endforelse

                <div class="d-flex justify-content-center mt-3">
                    {{ $notifications->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
