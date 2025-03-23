@extends('layouts.master.master')

@section('title', 'Visits')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Edit Visit</h4>
        </div>

        <div class="card-body">
            <form action="{{ route('dashboard.visits.update', $visit->id) }}" method="post">
                @method('put')
                @csrf

                <!-- Row 1: Appointment and Appointment Status -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Appointment:</label>
                            <select name="appointment_id" class="form-select @error('appointment_id') is-invalid @enderror">
                                <option selected disabled>--select--</option>
                                @foreach ($appointments as $appointment)
                                    <option value="{{ $appointment->id }}" @selected(old('appointment_id', $visit->appointment_id) == $appointment->id)>
                                        {{ $appointment->appointment_date }}
                                    </option>
                                @endforeach
                            </select>
                            @error('appointment_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Appointment Status:</label>
                            <select name="status" class="form-select @error('appointment_status') is-invalid @enderror">
                                <option selected disabled>--select--</option>
                                <option @selected(old('appointment_status', $appointment->status) == 'scheduled') value="scheduled">Scheduled</option>
                                <option @selected(old('appointment_status', $appointment->status) == 'walk_in') value="walk_in">Walk In</option>
                                <option @selected(old('appointment_status', $appointment->status) == 'completed') value="completed">Completed</option>
                                <option @selected(old('appointment_status', $appointment->status) == 'rescheduled') value="rescheduled">Rescheduled</option>
                                <option @selected(old('appointment_status', $appointment->status) == 'canceled') value="canceled">Canceled</option>
                            </select>
                            @error('appointment_status')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Row 2: Service and Patient -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Service:</label>
                            <select name="service_id" class="form-select @error('service_id') is-invalid @enderror">
                                <option selected disabled>--select--</option>
                                @foreach ($services as $service)
                                    <option value="{{ $service->id }}" @selected(old('service_id', $appointment->service_id) == $service->id)>
                                        {{ $service->service_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('service_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Patient:</label>
                            <select name="patient_id" class="form-select @error('patient_id') is-invalid @enderror">
                                <option selected disabled>--select--</option>
                                @foreach ($patients as $patient)
                                    <option value="{{ $patient->id }}" @selected(old('patient_id', $appointment->patient_id) == $patient->id)>
                                        {{ $patient->fname }}
                                    </option>
                                @endforeach
                            </select>
                            @error('patient_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Row 3: Staff and Visit Date -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Staff:</label>
                            <select name="staff_id" class="form-select @error('staff_id') is-invalid @enderror">
                                <option selected disabled>--select--</option>
                                @foreach ($staff as $staff)
                                    <option value="{{ $staff->id }}" @selected(old('staff_id', $appointment->staff_id) == $staff->id)>
                                        {{ $staff->user->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('staff_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">

                        <label>Visit Date:</label>
                        <input type="datetime-local" name="visit_date" class="form-control" value="{{ old('visit_date', $appointment->appointment_date) }}" placeholder='Visit Date' >


                    </div>
                </div>

                <!-- Row 4: Chief Complaint and Diagnosis -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <x-form.textarea label='Chief Complaint:' name='cheif_complaint' :value='$visit->cheif_complaint' placeholder="Enter Chief Complaint" />
                    </div>
                    <div class="col-md-6">
                        <x-form.textarea label='Diagnosis:' name='diagnosis' :value='$visit->diagnosis' placeholder="Diagnosis" />
                    </div>
                </div>

                <!-- Row 5: Treatment Notes and Next Visit Notes -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <x-form.textarea label='Treatment Notes:' name='treatment_notes' :value='$visit->treatment_notes' placeholder="Treatment Notes" />
                    </div>
                    <div class="col-md-6">
                        <x-form.textarea label='Next Visit Notes:' name='next_visit_notes' :value='$visit->next_visit_notes' placeholder="Enter Next Visit Notes" />
                    </div>
                </div>

                <!-- Row 6: Submit Button -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
