@extends('layouts.master.master')

@section('title', 'Patients')



@section('content')

    {{-- <h4>Edit Patient</h4> --}}

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
                            <x-form.input label='First Name:' type='text' name='fname' :value='$patient->fname'
                                placeholder='Patient First Name' />
                        </div>
                        <div class="col-md-6">
                            <x-form.input label='Last Name:' type='text' name='lname' :value='$patient->lname'
                                placeholder='Patient Last Name' />
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
                            <x-form.input label='Email:' type='email' name='email' :value='$patient->email'
                                placeholder='Patient Email' />
                        </div>
                        <div class="col-md-6">
                            <x-form.input label='Phone:' type='tel' name='phone' :value='$patient->phone'
                                placeholder='Patient Phone' />
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <x-form.input label='Date Of Birth:' type='date' name='DOB' :value='$patient->DOB'
                                placeholder='Patient Date Of Birth' />
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <x-form.textarea label='Medical History:' name='medical_history' :value='$patient->medical_history'
                                placeholder="Enter patient medical history" class="form-control" />
                        </div>
                        <div class="col-md-6">
                            <x-form.textarea label='Allergies:' name='allergies' :value='$patient->allergies'
                                placeholder="Enter patient Allergies" class="form-control" />
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="emergency-contact">
                                <div class="emergency-title h6">Emergency Contact Information</div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <x-form.input label='Contact Name:' type='text' name='emergency_contact_name'
                                            :value='$patient->emergency_contact_name' placeholder='Emergency Contact Name' />
                                    </div>
                                    <div class="col-md-6">
                                        <x-form.input label='Contact Phone:' type='tel' name='Emergency_contact_phone'
                                            :value='$patient->Emergency_contact_phone' placeholder='Emergency Contact Phone' />
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
