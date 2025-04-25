@extends('layouts.master.master')

@section('title', 'Categories')

@section('content')
    <div class="container-fluid">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center py-3">
                <h4 class="mb-0">Categories</h4>
                @can('categories.create')
                    <a href="{{ route('dashboard.inventory.categories.create') }}" class="btn btn-dark btn-sm">
                        <i class="fas fa-plus fa-sm"></i> New Category
                    </a>
                @endcan
            </div>

            <!-- Flash Messages -->
            @if (session()->has('success'))
                <div id="flash-msg" class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session()->has('error'))
                <div id="flash-msg" class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table small">
                    <thead>
                        <tr class="text-center">
                            <th>ID</th>
                            <th>Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            <tr class="text-center">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $category->name }}</td>
                                <td>
                                    @can('categories.update')
                                        <a class="btn btn-outline-primary btn-sm"
                                            href="{{ route('dashboard.inventory.categories.edit', $category->id) }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endcan
                                    @can('categories.delete')
                                        <button class="btn btn-outline-danger btn-sm delete-btn" data-id="{{ $category->id }}"
                                            data-name="{{ $category->name }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">No data found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Links -->
            <div class="mt-3">
                {{ $categories->links() }}
            </div>
        </div>
    </div>

    <!-- Hidden Delete Form -->
    <form id="delete-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle delete button clicks
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const categoryId = this.getAttribute('data-id');
                    const categoryName = this.getAttribute('data-name');

                    Swal.fire({
                        title: 'Are you sure?',
                        html: `You are about to delete the category: <strong>${categoryName}</strong>`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel',
                        reverseButtons: true,
                        focusCancel: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const form = document.getElementById('delete-form');
                            form.action =
                                "{{ route('dashboard.inventory.categories.destroy', '') }}/" +
                                categoryId;
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endpush
