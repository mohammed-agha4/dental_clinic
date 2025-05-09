@extends('layouts.master.master')

@section('title', 'Edit Profile')

@section('content')

{{-- @dd('storage/app/private/public/',$user->profile->profile_photo) --}}

    @if (session()->has('success'))
        <div id="flash-msg" class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session()->has('error'))
        <div id="flash-msg" class="alert alert-danger alert-dismissible fade show ">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('dashboard.profile.update') }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div class="container-fluid p-0">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Profile Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <x-form.input label='First Name:' type='text' name='first_name' :value='$user->profile->first_name' />
                        </div>
                        <div class="col-md-6">
                            <x-form.input label='Last Name:' type='text' name='last_name' :value='$user->profile->last_name' />
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <x-form.input label='Date Of Birth:' type='date' name='birthday' :value='$user->profile->birthday' />
                        </div>
                        <div class="col-md-6">
                            <label >Profile Photo</label>
                            <input type="file" name="profile_photo" class="form-control">
                            <img src="{{ asset('storage/' . $user->profile->profile_photo) }}" height="60" alt="">
                        </div>

                    </div>


                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Gender:</label>
                            <div class="gender-options">
                                <div class="form-check">
                                    <label><input type="radio" class="form-check-input" id="gender-male" name="gender"
                                        value="male" @checked(old('gender', $user->profile->gender) == 'male')></label>
                                    <label class="form-check-label" for="gender-male">Male</label>
                                </div>
                                <div class="form-check">
                                    <label><input type="radio" class="form-check-input" id="gender-female" name="gender"
                                        value="female" @checked(old('gender', $user->profile->gender) == 'female')></label>
                                    <label class="form-check-label" for="gender-female">Female</label>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="row mb-3">
                        <div class="col-md-6">
                            <x-form.input label='Street Address:' type='text' name='street_address' :value='$user->profile->street_address' />
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <x-form.input label='City:' type='text' name='city' :value='$user->profile->city' />
                        </div>
                    </div>




                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary m-2">Save Changes</button>
    </form>

    </form>
@endsection
