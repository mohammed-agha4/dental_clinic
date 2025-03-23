@extends('layouts.master.master')
@section('title', 'Booking Appointment')

@section('css')
    <style>
        .booking-form {
            max-width: 800px;
            margin: 2rem auto;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            border-radius: 15px;
            overflow: hidden;
        }

        .form-step {
            display: none;
        }

        .form-step.active {
            display: block;
        }

        .progress-bar {
            height: 10px;
            transition: width 0.5s ease-in-out;
        }

        .step-indicator {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background-color: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            transition: all 0.3s;
        }

        .step-indicator.active {
            background-color: #0d6efd;
            color: white;
        }

        .step-indicator.completed {
            background-color: #198754;
            color: white;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #0d6efd;
        }

        /* Time slot styling */
        .time-slot-container {
            display: none;
            margin-top: 15px;
        }

        .time-slot-container.active {
            display: block;
        }

        .time-slots {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }

        .time-slot {
            padding: 10px;
            text-align: center;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .time-slot:hover {
            background-color: #f8f9fa;
        }

        .time-slot.selected {
            background-color: #0d6efd;
            color: white;
            border-color: #0d6efd;
        }

        .time-slot.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background-color: #f1f1f1;
        }

        .date-picker-container {
            margin-bottom: 15px;
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
        <div id="flash-msg" class="alert alert-danger alert-dismissible fade show ">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="container">
        <div class="booking-header mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">Appointment Management</h2>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-primary active" id="regularBookingBtn">
                        <i class="fas fa-calendar-alt me-2"></i>Regular Booking
                    </button>
                    <button type="button" class="btn btn-outline-primary" id="walkInBtn">
                        <i class="fas fa-walking me-2"></i>Walk-in
                    </button>
                </div>
            </div>
            <p class="text-muted mt-2 mb-0" id="bookingDescription">
                Schedule a new appointment with complete patient information
            </p>
        </div>

        <div id="regularBookingForm" class="booking-form bg-white p-4">
            <div class="progress mb-4">
                <div class="progress-bar" role="progressbar" style="width: 33%"></div>
            </div>

            <div class="d-flex justify-content-between mb-4">
                <div class="text-center">
                    <div class="step-indicator active mx-auto mb-2">1</div>
                    <span class="d-none d-md-block">Personal Info</span>
                </div>
                <div class="text-center">
                    <div class="step-indicator mx-auto mb-2">2</div>
                    <span class="d-none d-md-block">Medical History</span>
                </div>
                <div class="text-center">
                    <div class="step-indicator mx-auto mb-2">3</div>
                    <span class="d-none d-md-block">Appointment</span>
                </div>
            </div>

            <form method="POST" action="{{ route('dashboard.appointments.store') }}" id="appointmentForm">
                @csrf





                <!-- Step 1: Personal Information -->
                <div class="form-step active" id="step1">
                    <h4 class="mb-4">Personal Information</h4>
                    <div class="row g-3">
                        <div class="col-md-6">

                            <label>First Name:</label>
                            <input type="text" name="fname" class="form-control" value="{{ old('fname', $patients->fname) }}" required >

                        </div>
                        <div class="col-md-6">

                            <label>Last Name:</label>
                            <input type="text" name="lname" class="form-control" value="{{ old('lname', $patients->lname) }}" required >

                        </div>
                        <div class="col-md-6">

                            <label>Date Of Birth:</label>
                            <input type="date" name="DOB" class="form-control" value="{{ old('DOB', $patients->DOB) }}" required >

                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Gender</label>
                            <select class="form-select" name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Phone:</label>
                            <input type="tel" name="phone" class="form-control" value="{{ old('phone', $patients->phone) }}" required >
                        </div>
                        <div class="col-md-6">
                            <label>Email:</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $patients->email) }}" required >
                        </div>
                    </div>
                    <div class="mt-4 text-end">
                        <button type="button" class="btn btn-primary px-4" onclick="nextStep(1)">Next <i
                                class="fas fa-arrow-right ms-2"></i></button>
                    </div>
                </div>


                <!-- Step 2: Medical History -->
                <div class="form-step" id="step2">
                    <h4 class="mb-4">Medical History</h4>
                    <div class="row g-3">
                        <div class="col-12">
                            <x-form.textarea label='Medical History' name='medical_history' :value='$patients->medical_history' />
                        </div>
                        <div class="col-12">
                            <x-form.textarea label='Allergies' name='allergies' :value='$patients->allergies' />
                        </div>
                        <div class="col-md-6">
                            <label>Emergency Contact Name:</label>
                            <input type="text" name="Emergency_contact_name" class="form-control" value="{{ old('Emergency_contact_name', $patients->Emergency_contact_name) }}" >

                        </div>
                        <div class="col-md-6">
                            <label>Emergency Contact Phone:</label>
                            <input type="tel" name="Emergency_contact_phone" class="form-control" value="{{ old('Emergency_contact_phone', $patients->Emergency_contact_phone) }}" >
                        </div>
                    </div>
                    <div class="mt-4 d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary px-4" onclick="prevStep(2)"><i
                                class="fas fa-arrow-left me-2"></i> Previous</button>
                        <button type="button" class="btn btn-primary px-4" onclick="nextStep(2)">Next <i
                                class="fas fa-arrow-right ms-2"></i></button>
                    </div>
                </div>



                <!-- Step 3: Appointment Details -->
                <div class="form-step" id="step3">
                    <h4 class="mb-4">Appointment Details</h4>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Service </label>
                            <select class="form-select" name="service_id" id="service_select" required>
                                <option value="" selected disabled>Select Service</option>
                                @foreach ($services as $service)
                                    @if ($service->is_active)
                                        <option value="{{ $service->id }}" data-duration="{{ $service->duration }}">
                                            {{ $service->service_name }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Duration (minutes)</label>
                            <input type="text" class="form-control" id="duration_input" name="duration" readonly>
                        </div>

                        <!-- New Date/Time Selection -->
                        <div class="col-12 date-picker-container">
                            <label class="form-label">Appointment Date </label>
                            <input type="date" class="form-control " id="appointment_date" name="appointment_date"
                                required>
                        </div>

                        <!-- Time Slots -->
                        <div class="col-12 time-slot-container" id="timeSlotContainer">
                            <label class="form-label">Available Time Slots </label>
                            <div class="time-slots" id="timeSlots">
                                <!-- Time slots will be dynamically populated -->
                            </div>
                            <!-- Hidden input to store the selected time -->
                            <input type="hidden" name="appointment_time" id="appointment_time" required>
                            <!-- Hidden input to store the combined date and time -->
                            <input type="hidden" name="appointment_date_time" id="appointment_date_time" required>
                        </div>

                        <div class="col-md-12">
                            <div class="alert alert-info mt-2" id="availabilityMessage" style="display: none;">
                                Please select a service and date to see available time slots.
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Available Dentist</label>
                            <select class="form-select" name="staff_id" id="staff_select" required disabled>
                                <option value="">Select service, date and time first</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <x-form.textarea label='Cancellation Reason' name='cancellation_reason' rows="2"
                                :value='$patients->cancellation_reason' />
                        </div>

                        <div class="col-12">
                            <x-form.textarea label='Additional Notes' name='notes' rows="2" :value='$patients->notes' />
                        </div>
                    </div>
                    <div class="mt-4 d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary px-4" onclick="prevStep(3)"><i
                                class="fas fa-arrow-left me-2"></i> Previous</button>
                        <button type="submit" class="btn btn-success px-4" id="bookAppointmentBtn">Book Appointment <i
                                class="fas fa-check ms-2"></i></button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Walk-in Form -->
        <div id="walkInForm" class="booking-form bg-white p-4" style="display: none;">
            <form method="POST" action="{{ route('dashboard.appointments.store') }}" id="walkInAppointmentForm">
                @csrf
                <input type="hidden" name="appointment_type" value="walk_in">

                <div class="row g-3">
                    <!-- Patient Information Section -->
                    <div class="col-12">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Patient Information</h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">First Name </label>
                                        <input type="text" class="form-control" name="fname" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Last Name </label>
                                        <input type="text" class="form-control" name="lname" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Phone </label>
                                        <input type="tel" class="form-control" name="phone" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" name="email">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Gender</label>
                                        <select class="form-select" name="gender" required>
                                            <option value="">Select Gender</option>
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Service Selection Section -->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Service Details</h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Service </label>

                                        <select class="form-select" name="service_id" id="walkInServiceSelect" required>
                                            <option value="">Select Service</option>
                                            @foreach ($services as $service)
                                                @if ($service->is_active)
                                                    <!-- Only show active services -->
                                                    <option value="{{ $service->id }}"
                                                        data-duration="{{ $service->duration }}"
                                                        data-price="{{ $service->service_price }}">
                                                        {{ $service->service_name }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>

                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Dentist </label>
                                        <select class="form-select" name="staff_id" id="walkInStaffSelect" required>
                                            <option value="">Select Dentist</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Duration</label>
                                        <input type="text" class="form-control" id="serviceDuration" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Additional Information</h5>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label">Chief Complaint <span
                                                class="text-danger">*</span></label>
                                        <textarea class="form-control" name="cheif_complaint" rows="2" required></textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Additional Notes</label>
                                        <textarea class="form-control" name="notes" rows="2"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="mt-4 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-outline-secondary" id="resetWalkInForm">
                        <i class="fas fa-redo me-2"></i>Reset
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-2"></i>Create Walk-in Appointment
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Multi step form navigation
            // next and previous buttons
            // progress bar, form validation
            let form = document.getElementById('appointmentForm');
            let steps = document.querySelectorAll('.form-step');
            let progressBar = document.querySelector('.progress-bar');
            let indicators = document.querySelectorAll('.step-indicator');

            // next step with validation
            window.nextStep = function(currentStep) {
                let currentStepElement = document.getElementById(`step${currentStep}`);
                let inputs = currentStepElement.querySelectorAll(
                    'input[required], select[required], textarea[required]');


                // validation
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
            // previous button
            window.prevStep = function(currentStep) {
                document.getElementById(`step${currentStep}`).classList.remove('active');
                document.getElementById(`step${currentStep - 1}`).classList.add('active');
                updateProgress(currentStep - 1);
            };
            // progress bar
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








            // Time slot selection
            let serviceSelect = document.getElementById('service_select');
            let durationInput = document.getElementById('duration_input');
            let appointmentDateInput = document.getElementById('appointment_date');
            let timeSlotContainer = document.getElementById('timeSlotContainer');
            let timeSlots = document.getElementById('timeSlots');
            let appointmentTimeInput = document.getElementById('appointment_time');
            let appointmentDateTimeInput = document.getElementById('appointment_date_time');
            let staffSelect = document.getElementById('staff_select');
            let availabilityMessage = document.getElementById('availabilityMessage');
            let bookAppointmentBtn = document.getElementById('bookAppointmentBtn');

            // Set minimum date to today
            let today = new Date();
            let yyyy = today.getFullYear();
            let mm = String(today.getMonth() + 1).padStart(2, '0');
            let dd = String(today.getDate()).padStart(2, '0');
            let formattedToday = `${yyyy}-${mm}-${dd}`;
            appointmentDateInput.min = formattedToday;


            // Generate time slots based on service duration
            function generateTimeSlots() {
                let serviceId = serviceSelect.value;
                let selectedDate = appointmentDateInput.value;
                let duration = parseInt(serviceSelect.options[serviceSelect.selectedIndex].dataset.duration ||
                    30);

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




                // AJAX call to get available time slots
                $.ajax({
                    url: `/dashboard/appointments/get-available-slots`, // Ensure this matches the route
                    method: 'GET',
                    data: {
                        service_id: serviceId,
                        date: selectedDate, // Ensure this is in YYYY-MM-DD format
                        duration: duration
                    },
                    success: function(response) {
                        if (response.success && response.slots && response.slots.length > 0) {
                            timeSlotContainer.classList.add('active');
                            availabilityMessage.style.display = 'none';

                            // Clear previous slots
                            timeSlots.innerHTML = '';

                            // Create time slots based on response
                            response.slots.forEach(slot => {
                                let timeSlot = document.createElement('div');
                                timeSlot.classList.add('time-slot');
                                if (slot.available) {
                                    timeSlot.textContent = slot.time;
                                    timeSlot.dataset.time = slot.time;
                                    timeSlot.dataset.dateTime = `${selectedDate}T${slot.time}`;

                                    timeSlot.addEventListener('click', function() {
                                        // Remove selection from all slots
                                        document.querySelectorAll('.time-slot').forEach(
                                            s => s.classList.remove('selected'));
                                        // Add selection to this slot
                                        this.classList.add('selected');
                                        // Update hidden inputs with selected time
                                        appointmentTimeInput.value = this.dataset.time;
                                        appointmentDateTimeInput.value = this.dataset
                                            .dateTime;
                                        // Update available dentists for this time slot
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
                        } else {
                            timeSlotContainer.classList.remove('active');
                            availabilityMessage.style.display = 'block';
                            availabilityMessage.textContent =
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








            // show the available dentists according to the service
            function updateAvailableDentists(serviceId, dateTime) {
                staffSelect.disabled = true;
                staffSelect.innerHTML = '<option value="">Loading...</option>';

                $.ajax({
                    url: `/dashboard/appointments/get-available-dentists`,
                    method: 'GET',
                    data: {
                        service_id: serviceId,
                        appointment_date: dateTime
                    },
                    success: function(data) {
                        console.log('API Response:', data);
                        console.log('All dentists returned:', data.dentists);

                        if (data.success && data.dentists) {
                            // Filter to ensure only active dentists are shown
                            let activeDentists = data.dentists.filter(dentist => dentist.is_active);
                            console.log('Filtered active dentists:', activeDentists);

                            if (activeDentists.length > 0) {
                                staffSelect.innerHTML = '<option value="">Select Dentist</option>';
                                activeDentists.forEach(dentist => {
                                    console.log(
                                        `Adding dentist: ${dentist.id}, Name: ${dentist.user ? dentist.user.name : 'Unknown'}, Active: ${dentist.is_active}`
                                    );
                                    staffSelect.innerHTML +=
                                        `<option value="${dentist.id}">${dentist.user ? dentist.user.name : 'Unknown'}</option>`;
                                });
                                staffSelect.disabled = false;
                            } else {
                                console.log('No active dentists available after filtering');
                                staffSelect.innerHTML =
                                    '<option value="">No dentists available</option>';
                                staffSelect.disabled = true;
                            }
                        } else {
                            console.log('No dentists data in the response or response not successful');
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



            // Event listeners for the appointment booking form
            serviceSelect.addEventListener('change', function() {
                let selectedOption = this.options[this.selectedIndex];
                durationInput.value = selectedOption.dataset.duration || '';
                appointmentTimeInput.value = '';
                appointmentDateTimeInput.value = '';

                if (appointmentDateInput.value) {
                    generateTimeSlots();
                } else {
                    timeSlotContainer.classList.remove('active');
                    availabilityMessage.style.display = 'block';
                    availabilityMessage.textContent = 'Please select a date to see available time slots.';
                }
            });

            appointmentDateInput.addEventListener('change', function() {
                appointmentTimeInput.value = '';
                appointmentDateTimeInput.value = '';
                if (serviceSelect.value) {
                    generateTimeSlots();
                } else {
                    timeSlotContainer.classList.remove('active');
                    availabilityMessage.style.display = 'block';
                    availabilityMessage.textContent =
                        'Please select a service to see available time slots.';
                }
            });




            // Form submission validation
            form.addEventListener('submit', function(e) {
                if (!appointmentDateTimeInput.value) {
                    e.preventDefault();
                    alert('Please select an appointment time slot.');
                    return false;
                }
            });

            // Form switching
            let regularBookingBtn = document.getElementById('regularBookingBtn');
            let walkInBtn = document.getElementById('walkInBtn');
            let regularBookingForm = document.getElementById('regularBookingForm');
            let walkInForm = document.getElementById('walkInForm');
            let bookingDescription = document.getElementById('bookingDescription');

            function switchForm(isWalkIn) {
                regularBookingBtn.classList.toggle('active', !isWalkIn);
                walkInBtn.classList.toggle('active', isWalkIn);
                regularBookingForm.style.display = isWalkIn ? 'none' : 'block';
                walkInForm.style.display = isWalkIn ? 'block' : 'none';
                bookingDescription.textContent = isWalkIn ? 'Create an immediate appointment for a walk-in patient' : 'Schedule a new appointment with complete patient information';
            }

            regularBookingBtn.addEventListener('click', () => switchForm(false));
            walkInBtn.addEventListener('click', () => switchForm(true));

            // Walk-in form handling
            let walkInServiceSelect = document.getElementById('walkInServiceSelect');
            let walkInStaffSelect = document.getElementById('walkInStaffSelect');
            let serviceDurationInput = document.getElementById('serviceDuration');
            let servicePriceInput = document.getElementById('servicePrice');

            if (walkInServiceSelect) {
                walkInServiceSelect.addEventListener('change', function() {
                    updateWalkInDentists();
                    updateServiceDetails();
                });
            }

            function updateServiceDetails() {
                let selectedOption = walkInServiceSelect.options[walkInServiceSelect.selectedIndex];
                if (selectedOption && selectedOption.value) {
                    let duration = selectedOption.getAttribute('data-duration');
                    let price = selectedOption.getAttribute('data-price');
                    serviceDurationInput.value = duration ? `${duration} minutes` : '';
                    if (servicePriceInput) {
                        servicePriceInput.value = price ? `$${price}` : '';
                    }
                } else {
                    serviceDurationInput.value = '';
                    if (servicePriceInput) {
                        servicePriceInput.value = '';
                    }
                }
            }

            function updateWalkInDentists() {
                let serviceId = walkInServiceSelect.value;

                if (!serviceId) {
                    walkInStaffSelect.innerHTML = '<option value="">Select service first</option>';
                    walkInStaffSelect.disabled = true;
                    return;
                }

                walkInStaffSelect.disabled = true;
                walkInStaffSelect.innerHTML = '<option value="">Loading...</option>';

                $.ajax({
                    url: `/dashboard/appointments/get-available-dentists?service_id=${serviceId}&is_walk_in=true`,
                    method: 'GET',
                    success: function(data) {
                        console.log('Walk-in API Response:', data);
                        console.log('All walk-in dentists returned:', data.dentists);

                        if (data.success && data.dentists) {
                            // Filter to ensure only active dentists are shown
                            let activeDentists = data.dentists.filter(dentist => dentist.is_active);
                            console.log('Filtered active walk-in dentists:', activeDentists);

                            if (activeDentists.length > 0) {
                                walkInStaffSelect.innerHTML =
                                    '<option value="">Select Dentist</option>';
                                activeDentists.forEach(dentist => {
                                    console.log(
                                        `Adding walk-in dentist: ${dentist.id}, Name: ${dentist.user ? dentist.user.name : 'Unknown'}, Active: ${dentist.is_active}`
                                    );
                                    walkInStaffSelect.innerHTML +=
                                        `<option value="${dentist.id}">${dentist.user ? dentist.user.name : 'Unknown'}</option>`;
                                });
                                walkInStaffSelect.disabled = false;
                            } else {
                                console.log('No active dentists available for walk-in after filtering');
                                walkInStaffSelect.innerHTML =
                                    '<option value="">No dentists available</option>';
                                walkInStaffSelect.disabled = true;

                            }
                        } else {
                            console.log(
                                'No walk-in dentists data in the response or response not successful'
                            );
                            walkInStaffSelect.innerHTML =
                                '<option value="">No dentists available</option>';
                            walkInStaffSelect.disabled = true;
                        }
                    },
                    error: function(error) {
                        console.error('Walk-in AJAX Error:', error);
                        walkInStaffSelect.innerHTML =
                            '<option value="">Error loading dentists</option>';
                        walkInStaffSelect.disabled = true;
                    }
                });
            }

            // Reset walk-in form
            let resetWalkInFormBtn = document.getElementById('resetWalkInForm');
            if (resetWalkInFormBtn) {
                resetWalkInFormBtn.addEventListener('click', function() {
                    document.getElementById('walkInAppointmentForm').reset();
                    walkInStaffSelect.innerHTML = '<option value="">Select service first</option>';
                    walkInStaffSelect.disabled = true;
                    serviceDurationInput.value = '';
                    if (servicePriceInput) {
                        servicePriceInput.value = '';
                    }
                });
            }

            // Walk-in form submission
            document.getElementById('walkInAppointmentForm').addEventListener('submit', function(e) {
                e.preventDefault();

                if (!this.checkValidity()) {
                    e.stopPropagation();
                    this.classList.add('was-validated');
                    return;
                }

                let submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';

                this.submit();
            });
        });
    </script>
@endpush
