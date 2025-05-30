@extends('layouts.master.master')

@section('title', 'Patient Information')

@section('content')
    <form action="{{ route('dashboard.patients.update', $patient->id) }}" method="post">
        @method('put')
        @csrf
        <div class="container-fluid p-0">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Patient Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fname" class="form-label">First Name:</label>
                                <input type="text" class="form-control" id="fname" name="fname"
                                    value="{{ old('fname', $patient->fname) }}" placeholder="Patient First Name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="lname" class="form-label">Last Name:</label>
                                <input type="text" class="form-control" id="lname" name="lname"
                                    value="{{ old('lname', $patient->lname) }}" placeholder="Patient Last Name">
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Gender:</label>
                            <div class="gender-options">
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" id="gender-male" name="gender"
                                        value="male" @checked(old('gender', $patient->gender) == 'male')>
                                    <label class="form-check-label" for="gender-male">Male</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" id="gender-female" name="gender"
                                        value="female" @checked(old('gender', $patient->gender) == 'female')>
                                    <label class="form-check-label" for="gender-female">Female</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email" class="form-label">Email:</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="{{ old('email', $patient->email) }}" placeholder="Patient Email">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone" class="form-label">Phone:</label>
                                <input type="tel" class="form-control" id="phone" name="phone"
                                    value="{{ old('phone', $patient->phone) }}" placeholder="Patient Phone">
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="DOB" class="form-label">Date Of Birth:</label>
                                <input type="date" class="form-control" id="DOB" name="DOB"
                                    value="{{ old('DOB', $patient->DOB ? \Carbon\Carbon::parse($patient->DOB)->format('Y-m-d') : '') }}">
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="medical_history" class="form-label">Medical History:</label>
                                <textarea class="form-control" id="medical_history" name="medical_history" placeholder="Enter patient medical history">{{ old('medical_history', $patient->medical_history) }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="allergies" class="form-label">Allergies:</label>
                                <textarea class="form-control" id="allergies" name="allergies" placeholder="Enter patient Allergies">{{ old('allergies', $patient->allergies) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="emergency-contact">
                                <div class="h6 my-2" style="font-weight: 600;">Emergency Contact Information</div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="emergency_contact_name" class="form-label">Contact Name:</label>
                                            <input type="text" class="form-control" id="emergency_contact_name" name="emergency_contact_name"
                                                   value="{{ old('emergency_contact_name', $patient->Emergency_contact_name) }}"
                                                   placeholder="Emergency Contact Name">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="Emergency_contact_phone" class="form-label">Contact Phone:</label>
                                            <input type="tel" class="form-control" id="Emergency_contact_phone" name="Emergency_contact_phone"
                                                   value="{{ old('Emergency_contact_phone', $patient->Emergency_contact_phone) }}"
                                                   placeholder="Emergency Contact Phone">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary m-2">Save Changes</button>
    </form>
@endsection
