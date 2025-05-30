@extends('layouts.master.master')
@section('title', 'Edit Appointment')

@section('css')
    <style>
        .booking-form {
            max-width: 800px;
            margin: 2rem auto;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            border-radius: 12px;
            overflow: hidden;
            background-color: #fff;
        }

        .form-step {
            display: none;
            padding: 1.5rem;
        }

        .form-step.active {
            display: block;
            animation: fadeIn 0.3s ease-in-out;
        }

        .progress-container {
            padding: 1rem;
            background-color: #f8f9fa;
            border-bottom: 1px solid #eee;
        }

        .progress {
            --bs-progress-height: .5rem;
        }

        .progress-bar {
            height: 6px;
            border-radius: 3px;
            background-color: var(--primary-color);
            transition: width 0.5s ease-in-out;
        }

        .steps-indicator {
            display: flex;
            justify-content: space-between;
            margin-top: 1rem;
        }

        .step-indicator {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 500;
            transition: all 0.3s;
            color: #495057;
            border: 2px solid transparent;
        }

        .step-indicator.active {
            background-color: #e7f1ff;
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .step-indicator.completed {
            background-color: #d1e7dd;
            color: #198754;
            border-color: #198754;
        }

        .step-label {
            font-size: 0.85rem;
            color: #6c757d;
            margin-top: 0.5rem;
            text-align: center;
        }

        .form-label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 0.5rem;
        }

        .form-control:focus,
        .form-select:focus {
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
            border-color: #86b7fe;
        }


        .time-slot-container {
            display: none;
            margin-top: 1rem;
            padding: 1rem;
            border-radius: 8px;
            background-color: #f8f9fa;
        }

        .time-slot-container.active {
            display: block;
        }

        .time-slots {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(90px, 1fr));
            gap: 8px;
            margin-top: 0.75rem;
        }

        .time-slot {
            padding: 8px 4px;
            text-align: center;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.9rem;
        }

        .time-slot:hover:not(.disabled) {
            background-color: #e9ecef;
            border-color: #ced4da;
        }

        .time-slot.selected {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
            font-weight: 500;
        }

        .time-slot.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background-color: #f1f1f1;
        }

        .appointment-status {
            padding: 0.4rem 0.75rem;
            border-radius: 6px;
            display: inline-block;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .status-scheduled {
            background-color: #cff4fc;
            color: #055160;
        }

        .status-rescheduled {
            background-color: #fff3cd;
            color: #664d03;
        }

        .status-completed {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .status-canceled {
            background-color: #f8d7da;
            color: #842029;
        }

        .status-walk_in {
            background-color: #e2e3e5;
            color: #41464b;
        }

        .current-appointment {
            background-color: #e7f1ff;
            border-left: 4px solid var(--primary-color);
            padding: 0.75rem;
            border-radius: 6px;
            margin-bottom: 1rem;
        }

        .form-section {
            margin-bottom: 1.5rem;
        }

        .form-section-title {
            font-size: 1.1rem;
            font-weight: 500;
            margin-bottom: 1rem;
            color: #212529;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #e9ecef;
        }

        .form-navigation {
            display: flex;
            justify-content: space-between;
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid #e9ecef;
        }

        .btn-nav {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
    </style>
@endsection

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

    <div class="container">
        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">Edit Appointment</h2>
                <div class="appointment-status status-{{ $appointment->status }}">
                    {{ ucfirst($appointment->status) }}
                </div>
            </div>
            {{-- <p class="text-muted mt-2">
                Appointment #{{ $appointment->id }} | Created: {{ $appointment->created_at->format('M d, Y') }}
            </p> --}}
        </div>

        <div class="booking-form">
            <div class="progress-container">
                <div class="progress rounded-pill bg-secondary-subtle">
                    <div class="progress-bar" style="width: 33%"></div>
                </div>

                <div class="steps-indicator">
                    <div class="text-center">
                        <div class="step-indicator active mx-auto">1</div>
                        <div class="step-label">Patient</div>
                    </div>
                    <div class="text-center">
                        <div class="step-indicator mx-auto">2</div>
                        <div class="step-label">Medical</div>
                    </div>
                    <div class="text-center">
                        <div class="step-indicator mx-auto">3</div>
                        <div class="step-label">Schedule</div>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('dashboard.appointments.update', $appointment->id) }}"
                id="editAppointmentForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="appointment_id" value="{{ $appointment->id }}">

                <div class="form-step active" id="step1">
                    <div class="form-section">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">First Name</label>
                                <input type="text" name="fname" class="form-control"
                                    value="{{ old('fname', $patient->fname) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="lname" class="form-control"
                                    value="{{ old('lname', $patient->lname) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date Of Birth</label>
                                <input type="date" name="DOB" class="form-control"
                                    value="{{ old('DOB', $patient->DOB ? \Carbon\Carbon::parse($patient->DOB)->format('Y-m-d') : '') }}"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Gender</label>
                                <select class="form-select" name="gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="male"
                                        {{ old('gender', $patient->gender) === 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female"
                                        {{ old('gender', $patient->gender) === 'female' ? 'selected' : '' }}>Female
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="tel" name="phone" class="form-control"
                                    value="{{ old('phone', $patient->phone) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control"
                                    value="{{ old('email', $patient->email) }}" required>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-primary float-end mb-4" onclick="nextStep(1)">
                        Next <i class="fas fa-arrow-right"></i>
                    </button>
                </div>


                <div class="form-step" id="step2">
                    <div class="form-section">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Medical History</label>
                                <textarea class="form-control" name="medical_history" rows="3">{{ old('medical_history', $patient->medical_history) }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Allergies</label>
                                <textarea class="form-control" name="allergies" rows="3">{{ old('allergies', $patient->allergies) }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Emergency Contact Name</label>
                                <input type="text" name="Emergency_contact_name" class="form-control"
                                    value="{{ old('Emergency_contact_name', $patient->Emergency_contact_name) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Emergency Contact Phone</label>
                                <input type="tel" name="Emergency_contact_phone" class="form-control"
                                    value="{{ old('Emergency_contact_phone', $patient->Emergency_contact_phone) }}">
                            </div>
                        </div>
                    </div>
                    <div class="form-navigation">
                        <button type="button" class="btn btn-outline-secondary btn-nav" onclick="prevStep(2)">
                            <i class="fas fa-arrow-left"></i> Previous
                        </button>
                        <button type="button" class="btn btn-primary btn-nav" onclick="nextStep(2)">
                            Next <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>


                <div class="form-step" id="step3">
                    <div class="form-section">

                        <div class="current-appointment">
                            <div class="fw-semibold mb-1">Current Appointment</div>
                            {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }} at
                            {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('h:i A') }}
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="scheduled"
                                        {{ $appointment->status === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                    <option value="completed"
                                        {{ $appointment->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="rescheduled"
                                        {{ $appointment->status === 'rescheduled' ? 'selected' : '' }}>Rescheduled</option>
                                    <option value="canceled" {{ $appointment->status === 'canceled' ? 'selected' : '' }}>
                                        Canceled</option>
                                    @if ($appointment->status === 'walk_in')
                                        <option value="walk_in" selected>Walk-in</option>
                                    @endif
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Service</label>
                                <select class="form-select" name="service_id" id="service_select" required>
                                    <option value="" disabled>Select Service</option>
                                    @foreach ($services as $service)
                                        @if ($service->is_active)
                                            <option value="{{ $service->id }}" data-duration="{{ $service->duration }}"
                                                {{ $appointment->service_id == $service->id ? 'selected' : '' }}>
                                                {{ $service->service_name }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Duration (minutes)</label>
                                <input type="text" class="form-control" id="duration_input" name="duration"
                                    value="{{ old('duration', $appointment->duration) }}" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">New Appointment Date</label>
                                <input type="date" class="form-control" id="appointment_date" name="appointment_date"
                                    value="{{ old('appointment_date', \Carbon\Carbon::parse($appointment->appointment_date)->format('Y-m-d')) }}">
                            </div>

                            <div class="col-12 time-slot-container" id="timeSlotContainer">
                                <label class="form-label fw-semibold">Available Time Slots</label>
                                <div class="time-slots" id="timeSlots">
                                    {{-- Time slots will appear here --}}
                                </div>
                                <input type="hidden" name="appointment_time" id="appointment_time"
                                    value="{{ old('appointment_time', \Carbon\Carbon::parse($appointment->appointment_date)->format('H:i')) }}">
                                <input type="hidden" name="appointment_date_time" id="appointment_date_time"
                                    value="{{ old('appointment_date_time', $appointment->appointment_date) }}">
                            </div>

                            <div class="col-12">
                                <div class="alert alert-info mt-2" id="availabilityMessage" style="display: none;">
                                    Please select a service and date to see available time slots.
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Dentist</label>
                                <select class="form-select" name="staff_id" id="staff_select" required>
                                    <option value="">Select service, date and time first</option>
                                </select>
                            </div>


                            <div class="col-12">
                                <label class="form-label">Additional Notes</label>
                                <textarea class="form-control" name="notes" rows="2">{{ old('notes', $appointment->notes) }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="form-navigation">
                        <button type="button" class="btn btn-outline-secondary btn-nav" onclick="prevStep(3)">
                            <i class="fas fa-arrow-left"></i> Previous
                        </button>
                        <button type="submit" class="btn btn-success btn-nav" id="updateAppointmentBtn">
                            <i class="fas fa-check"></i> Update Appointment
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            let form = document.getElementById('editAppointmentForm');
            let steps = document.querySelectorAll('.form-step');
            let progressBar = document.querySelector('.progress-bar');
            let indicators = document.querySelectorAll('.step-indicator');


            window.nextStep = function(currentStep) {
                let currentStepElement = document.getElementById(`step${currentStep}`);
                let inputs = currentStepElement.querySelectorAll(
                    'input[required], select[required], textarea[required]');

                let isValid = true;
                inputs.forEach(input => {
                    if (!input.value.trim()) {
                        isValid = false;
                        input.classList.add('is-invalid');
                    } else {
                        input.classList.remove('is-invalid');
                    }
                });

                if (!isValid) {
                    alert('Please fill in all required fields.');
                    return;
                }

                currentStepElement.classList.remove('active');
                document.getElementById(`step${currentStep + 1}`).classList.add('active');
                updateProgress(currentStep + 1);
            };


            window.prevStep = function(currentStep) {
                document.getElementById(`step${currentStep}`).classList.remove('active');
                document.getElementById(`step${currentStep - 1}`).classList.add('active');
                updateProgress(currentStep - 1);
            };


            function updateProgress(stepNumber) {
                let progress = ((stepNumber - 1) / (steps.length - 1)) * 100;
                progressBar.style.width = `${progress}%`;

                indicators.forEach((indicator, index) => {
                    if (index + 1 < stepNumber) {
                        indicator.classList.add('completed');
                        indicator.classList.remove('active');
                    } else if (index + 1 === stepNumber) {
                        indicator.classList.add('active');
                        indicator.classList.remove('completed');
                    } else {
                        indicator.classList.remove('active', 'completed');
                    }
                });
            }


            let serviceSelect = document.getElementById('service_select');
            let durationInput = document.getElementById('duration_input');
            let appointmentDateInput = document.getElementById('appointment_date');
            let timeSlotContainer = document.getElementById('timeSlotContainer');
            let timeSlots = document.getElementById('timeSlots');
            let appointmentTimeInput = document.getElementById('appointment_time');
            let appointmentDateTimeInput = document.getElementById('appointment_date_time');
            let staffSelect = document.getElementById('staff_select');
            let availabilityMessage = document.getElementById('availabilityMessage');

            // Set minimum date to today
            let today = new Date();
            let yyyy = today.getFullYear();
            let mm = String(today.getMonth() + 1).padStart(2, '0');
            let dd = String(today.getDate()).padStart(2, '0');
            let formattedToday = `${yyyy}-${mm}-${dd}`;
            appointmentDateInput.min = formattedToday;


            if (serviceSelect.selectedIndex > -1) {
                let selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
                durationInput.value = selectedOption.dataset.duration || '30';
            }


            if (serviceSelect.value && appointmentDateInput.value) {
                generateTimeSlots();
            }


            function generateTimeSlots() {
                let serviceId = serviceSelect.value;
                let selectedDate = appointmentDateInput.value;
                let duration = parseInt(serviceSelect.options[serviceSelect.selectedIndex].dataset.duration || 30);
                let currentAppointmentId = {{ $appointment->id }};


                if (!serviceId || !selectedDate) {
                    timeSlotContainer.classList.remove('active');
                    availabilityMessage.style.display = 'block';
                    availabilityMessage.textContent =
                        'Please select a service and date to see available time slots.';
                    staffSelect.innerHTML = '<option value="">Select service, date and time first</option>';
                    staffSelect.disabled = true;
                    return;
                }

                timeSlotContainer.classList.remove('active');
                availabilityMessage.style.display = 'block';
                availabilityMessage.textContent = 'Loading available time slots...';
                timeSlots.innerHTML = '';


                $.ajax({
                    url: `/dashboard/appointments/get-available-slots`,
                    method: 'GET',
                    data: {
                        service_id: serviceId,
                        date: selectedDate,
                        duration: duration,
                        current_appointment_id: currentAppointmentId
                    },
                    success: function(response) {
                        // console.log('Time slots:', response);

                        if (response.success && response.slots && response.slots.length > 0) {
                            timeSlotContainer.classList.add('active');
                            availabilityMessage.style.display = 'none';

                            // to clear old slots
                            timeSlots.innerHTML = '';


                            let currentTime = appointmentTimeInput.value;
                            // console.log(currentTime);

                            response.slots.forEach(slot => {
                                let timeSlot = document.createElement('div');
                                timeSlot.classList.add('time-slot');

                                if (slot.available) {
                                    timeSlot.textContent = slot.time;
                                    timeSlot.dataset.time = slot.time;
                                    timeSlot.dataset.dateTime = `${selectedDate}T${slot.time}`;

                                    // Pre-select the current appointment time if it matches
                                    if (slot.time === currentTime) {
                                        timeSlot.classList.add('selected');
                                    }

                                    timeSlot.addEventListener('click', function() {
                                        document.querySelectorAll('.time-slot').forEach(
                                            s => s.classList.remove('selected'));

                                        this.classList.add('selected');

                                        appointmentTimeInput.value = this.dataset.time;
                                        appointmentDateTimeInput.value = this.dataset
                                            .dateTime;

                                        updateAvailableDentists(serviceId, this.dataset
                                            .dateTime);
                                    });
                                } else {
                                    timeSlot.textContent = slot.time;
                                    timeSlot.classList.add('disabled');
                                    timeSlot.title = 'Not available';
                                }

                                timeSlots.appendChild(timeSlot);
                            });

                            // If the current time is selected, update the available dentists
                            if (currentTime) {
                                let dateTime = `${selectedDate}T${currentTime}`;
                                updateAvailableDentists(serviceId, dateTime);
                            }
                        } else {
                            timeSlotContainer.classList.remove('active');
                            availabilityMessage.style.display = 'block';
                            availabilityMessage.textContent = response.message ||
                                'No available time slots for the selected date.';
                            staffSelect.innerHTML = '<option value="">No time slots available</option>';
                            staffSelect.disabled = true;
                        }
                    },
                    error: function(error) {
                        console.error('Error loading time slots:', error);
                        timeSlotContainer.classList.remove('active');
                        availabilityMessage.style.display = 'block';
                        availabilityMessage.textContent = 'Error loading time slots. Please try again.';
                    }
                });
            }

            function updateAvailableDentists(serviceId, dateTime) {
                staffSelect.disabled = true;
                staffSelect.innerHTML = '<option value="">Loading...</option>';
                let currentAppointmentId = {{ $appointment->id }};
                let currentStaffId = {{ $appointment->staff_id }};

                $.ajax({
                    url: `/dashboard/appointments/get-available-dentists`,
                    method: 'GET',
                    data: {
                        service_id: serviceId,
                        appointment_date: dateTime,
                        current_appointment_id: currentAppointmentId
                    },
                    success: function(data) {
                        console.log('Dentists response:', data);

                        if (data.success && data.dentists) {
                            let activeDentists = data.dentists.filter(dentist => dentist.is_active);
                            console.log('Active dentists:', activeDentists);

                            if (activeDentists.length > 0) {
                                staffSelect.innerHTML = '<option value="">Select Dentist</option>';

                                activeDentists.forEach(dentist => {
                                    let selected = (dentist.id == currentStaffId) ? 'selected' :
                                        '';
                                    staffSelect.innerHTML +=
                                        `<option value="${dentist.id}" ${selected}>${dentist.user ? dentist.user.name : 'Unknown'}</option>`;
                                });

                                staffSelect.disabled = false;
                            } else {
                                staffSelect.innerHTML =
                                    '<option value="">No dentists available</option>';
                                staffSelect.disabled = true;
                            }
                        } else {
                            staffSelect.innerHTML = '<option value="">No dentists available</option>';
                            staffSelect.disabled = true;
                        }
                    },
                    error: function(error) {
                        console.error('AJAX Error:', error);
                        staffSelect.innerHTML = '<option value="">Error loading dentists</option>';
                        staffSelect.disabled = true;
                    }
                });
            }

            serviceSelect.addEventListener('change', function() {
                let selectedOption = this.options[this.selectedIndex];
                durationInput.value = selectedOption.dataset.duration || '30';

                if (appointmentDateInput.value) {
                    generateTimeSlots();
                } else {
                    timeSlotContainer.classList.remove('active');
                    availabilityMessage.style.display = 'block';
                    availabilityMessage.textContent = 'Please select a date to see available time slots.';
                }
            });

            appointmentDateInput.addEventListener('change', function() {
                if (serviceSelect.value) {
                    generateTimeSlots();
                } else {
                    timeSlotContainer.classList.remove('active');
                    availabilityMessage.style.display = 'block';
                    availabilityMessage.textContent =
                        'Please select a service to see available time slots.';
                }
            });

            form.addEventListener('submit', function(e) {

                let originalDate =
                    "{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('Y-m-d') }}";
                let selectedDate = appointmentDateInput.value;
                let dateHasChanged = originalDate !== selectedDate;

                if (statusSelect.value !== 'canceled' && dateHasChanged && !appointmentTimeInput.value) {
                    e.preventDefault();
                    alert('Please select an appointment time slot for the new date.');
                    return false;
                }
            });
        });
    </script>
@endpush
