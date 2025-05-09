@extends('layouts.master.master')

@section('title', 'Staff Information')

@section('content')
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
    {{-- <h4>Edit Staff Member</h4> --}}
    <form action="{{ route('dashboard.staff.update', $staff->id) }}" method="post">
        @method('put')
        @csrf
        <div class="container-fluid p-0">

            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Staff Member Name:</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                            name="name" placeholder="Enter staff name"
                            value="{{ old('name', $staff->user->name) }}">
                        @error('name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Email:</label>
                        <input type="text" class="form-control @error('email') is-invalid @enderror"
                            name="email" placeholder="Enter staff email"
                            value="{{ old('email', $staff->user->email) }}">
                        @error('email')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">New Password (leave blank to keep current):</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" name="password"
                            placeholder="Enter new password">
                        @error('password')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Confirm New Password:</label>
                        <input type="password" class="form-control" name="password_confirmation"
                            placeholder="Confirm new password">
                    </div>
                </div>
            </div>



            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Role:</label>
                        <select name="role" class="form-select @error('role') is-invalid @enderror">
                            <option selected disabled>--select--</option>
                            <option @selected(old('role', $staff->role) == 'admin') value="admin">Admin</option>
                            <option @selected(old('role', $staff->role) == 'dentist') value="dentist">Dentist</option>
                            <option @selected(old('role', $staff->role) == 'assistant') value="assistant">Assistant</option>
                            <option @selected(old('role', $staff->role) == 'receptionist') value="receptionist">Receptionist</option>
                        </select>
                        @error('role')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Phone Number:</label>
                        <input type="tel" class="form-control @error('phone') is-invalid @enderror" name="phone"
                            placeholder="Enter staff phone" value="{{ old('phone', $staff->phone) }}">
                        @error('phone')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">License Number:</label>
                        <input type="text" class="form-control @error('license_number') is-invalid @enderror"
                            name="license_number" placeholder="Enter staff license number"
                            value="{{ old('license_number', $staff->license_number) }}">
                        @error('license_number')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Working Hours:</label>
                        <input type="number" class="form-control @error('working_hours') is-invalid @enderror"
                            name="working_hours" placeholder="Enter staff Working Hours"
                            value="{{ old('working_hours', $staff->working_hours) }}">
                        @error('working_hours')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Active:</label>
                        <select name="is_active" class="form-select @error('is_active') is-invalid @enderror">
                            <option value="1" @selected(old('is_active', $staff->is_active) == '1')>Active</option>
                            <option value="0" @selected(old('is_active', $staff->is_active) == '0')>Not Active</option>
                        </select>
                        @error('is_active')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>
            </div>
        <button type="submit" class="btn btn-primary m-2">Save Changes</button>
        </div>
        </div>

    </form>
@endsection
