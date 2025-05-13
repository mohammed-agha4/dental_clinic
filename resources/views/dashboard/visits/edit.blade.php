@extends('layouts.master.master')

@section('title', 'Visit Information')

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
                                <input type="text" class="form-control"
                                    value="{{ $visit->appointment->appointment_date->format('Y-m-d H:i') ?? 'N/A' }}"
                                    readonly>
                                <input type="hidden" name="appointment_id" value="{{ $visit->appointment_id }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Appointment Status:</label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror">
                                    <option selected disabled>--select--</option>
                                    <option value="scheduled" @selected(old('status', $visit->appointment->status) == 'scheduled')>Scheduled</option>
                                    <option value="walk_in" @selected(old('status', $visit->appointment->status) == 'walk_in')>Walk In</option>
                                    <option value="completed" @selected(old('status', $visit->appointment->status) == 'completed')>Completed</option>
                                    <option value="rescheduled" @selected(old('status', $visit->appointment->status) == 'rescheduled')>Rescheduled</option>
                                    <option value="canceled" @selected(old('status', $visit->appointment->status) == 'canceled')>Canceled</option>
                                </select>
                                @error('status')
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
                                        <option value="{{ $service->id }}" @selected(old('service_id', $visit->appointment->service_id) == $service->id)>
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
                                        <option value="{{ $patient->id }}" @selected(old('patient_id', $visit->appointment->patient_id) == $patient->id)>
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
                                        <option value="{{ $staff->id }}" @selected(old('staff_id', $visit->appointment->staff_id) == $staff->id)>
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
                            <input type="datetime-local" name="visit_date" class="form-control"
                                value="{{ old('visit_date', $visit->appointment->appointment_date) }}"
                                placeholder='Visit Date'>


                        </div>
                    </div>

                    <!-- Row 4: Chief Complaint and Diagnosis -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <x-form.textarea label='Chief Complaint:' name='cheif_complaint' :value='$visit->cheif_complaint'
                                placeholder="Enter Chief Complaint" />
                        </div>
                        <div class="col-md-6">
                            <x-form.textarea label='Diagnosis:' name='diagnosis' :value='$visit->diagnosis'
                                placeholder="Diagnosis" />
                        </div>
                    </div>

                    <!-- Row 5: Treatment Notes and Next Visit Notes -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <x-form.textarea label='Treatment Notes:' name='treatment_notes' :value='$visit->treatment_notes'
                                placeholder="Treatment Notes" />
                        </div>
                        <div class="col-md-6">
                            <x-form.textarea label='Next Visit Notes:' name='next_visit_notes' :value='$visit->next_visit_notes'
                                placeholder="Enter Next Visit Notes" />
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
