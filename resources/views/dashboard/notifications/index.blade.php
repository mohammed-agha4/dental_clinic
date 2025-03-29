@extends('layouts.master.master')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Notifications</h5>
                <form action="{{ route('notifications.markAllRead') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-secondary">
                        Mark All as Read
                    </button>
                </form>
            </div>
            <div class="card-body">
                @forelse($notifications as $notification)
                    <div
                        class="notification-item p-3 mb-2 border-bottom {{ $notification->read_at ? 'bg-light' : 'bg-white' }}">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="mb-1">
                                    <i class="{{ $notification->data['icon'] ?? 'fas fa-bell' }} me-2"></i>
                                    {{ $notification->data['message'] }}
                                </p>
                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                            </div>
                            <div>
                                <a href="{{ route('notifications.markAsRead', $notification->id) }}"
                                    class="btn btn-sm btn-outline-primary">
                                    Mark as Read
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-muted">No notifications found</p>
                @endforelse
                {{ $notifications->links() }}
            </div>
        </div>
    </div>
@endsection
