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
            display: block !important;
        }

        .time-slots {
            display: grid !important;
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

        #patientSearchResults {
            display: none;
            margin-top: 15px;
        }

        .patient-info {
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
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
                <div class="progress-bar" role="progressbar" style="width: 25%"></div>
            </div>

            <div class="d-flex justify-content-between mb-4">
                <div class="text-center">
                    <div class="step-indicator active mx-auto mb-2">1</div>
                    <span class="d-none d-md-block">Find Patient</span>
                </div>
                <div class="text-center">
                    <div class="step-indicator mx-auto mb-2">2</div>
                    <span class="d-none d-md-block">Personal Info</span>
                </div>
                <div class="text-center">
                    <div class="step-indicator mx-auto mb-2">3</div>
                    <span class="d-none d-md-block">Medical History</span>
                </div>
                <div class="text-center">
                    <div class="step-indicator mx-auto mb-2">4</div>
                    <span class="d-none d-md-block">Appointment</span>
                </div>
            </div>

            <form method="POST" action="{{ route('dashboard.appointments.store') }}" id="appointmentForm">
                @csrf
                <input type="hidden" name="existing_patient_id" id="existingPatientId"
                    value="{{ old('existing_patient_id') }}">

                {{-- search patient --}}
                <div class="form-step active" id="step0">
                    <h4 class="mb-4">Find Patient</h4>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label>Search by Phone:</label>
                            <input type="tel" id="patientSearchPhone" class="form-control"
                                placeholder="Enter patient phone number" value="{{ old('phone') }}">
                        </div>
                        <div class="col-md-6">
                            <label>Search by Email:</label>
                            <input type="email" id="patientSearchEmail" class="form-control"
                                placeholder="Enter patient email" value="{{ old('email') }}">
                        </div>
                        <div class="col-12">
                            <button type="button" class="btn btn-primary" id="searchPatientBtn">
                                <i class="fas fa-search me-2"></i>Search Patient
                            </button>
                            <button type="button" class="btn btn-outline-secondary ms-2" id="newPatientBtn">
                                <i class="fas fa-user-plus me-2"></i>New Patient
                            </button>
                        </div>
                        <div class="col-12" id="patientSearchResults">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Found Patient</h5>
                                    <div id="patientDetails"></div>
                                    <button type="button" class="btn btn-success mt-3" id="useExistingPatient">
                                        <i class="fas fa-check me-2"></i>Use This Patient
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- personal info --}}
                <div class="form-step" id="step1">
                    <h4 class="mb-4">Personal Information</h4>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label>First Name:</label>
                            <input type="text" name="fname" class="form-control fname"
                                value="{{ old('fname', $patients->fname) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label>Last Name:</label>
                            <input type="text" name="lname" class="form-control"
                                value="{{ old('lname', $patients->lname) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label>Date Of Birth:</label>
                            <input type="date" name="DOB" class="form-control"
                                value="{{ old('DOB', $patients->DOB) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Gender</label>
                            <select class="form-select" name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="male" @selected(old('gender', $patients->gender) == 'male')>Male</option>
                                <option value="female" @selected(old('gender', $patients->gender) == 'female')>Female</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Phone:</label>
                            <input type="tel" name="phone"
                                class="form-control @error('phone') is-invalid @enderror"
                                value="{{ old('phone', $patients->phone) }}" required>
                            @error('phone')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label>Email:</label>
                            <input type="email" name="email" class="form-control"
                                value="{{ old('email', $patients->email) }}" required>
                        </div>
                    </div>
                    <div class="mt-4 d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary px-4" onclick="prevStep(1)"><i
                                class="fas fa-arrow-left me-2"></i> Previous</button>
                        <button type="button" class="btn btn-primary px-4" onclick="nextStep(1)">Next <i
                                class="fas fa-arrow-right ms-2"></i></button>
                    </div>
                </div>

                {{-- Medical History --}}
                <div class="form-step" id="step2">
                    <h4 class="mb-4">Medical History</h4>
                    <div class="row g-3">
                        <div class="col-12">
                            <label>Medical History</label>
                            <textarea name="medical_history" class="form-control @error('medical_history') is-invalid @enderror" cols="30"
                                rows="3">{{ old('medical_history', $patients->medical_history ?? '') }}</textarea>
                            @error('medical_history')
                                <small class="text-danger alert-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label>Allergies</label>
                            <textarea name="allergies" class="form-control @error('allergies') is-invalid @enderror" cols="30"
                                rows="3">{{ old('allergies', $patients->allergies ?? '') }}</textarea>
                            @error('allergies')
                                <small class="text-danger alert-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label>Emergency Contact Name:</label>
                            <input type="text" name="Emergency_contact_name" class="form-control"
                                value="{{ old('Emergency_contact_name', $patients->Emergency_contact_name) }}">
                        </div>
                        <div class="col-md-6">
                            <label>Emergency Contact Phone:</label>
                            <input type="tel" name="Emergency_contact_phone" class="form-control"
                                value="{{ old('Emergency_contact_phone', $patients->Emergency_contact_phone) }}">
                        </div>
                    </div>
                    <div class="mt-4 d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary px-4" onclick="prevStep(2)"><i
                                class="fas fa-arrow-left me-2"></i> Previous</button>
                        <button type="button" class="btn btn-primary px-4" onclick="nextStep(2)">Next <i
                                class="fas fa-arrow-right ms-2"></i></button>
                    </div>
                </div>

                {{-- Appointment Details --}}
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

                        <!-- Date/Time Selection -->
                        <div class="col-12 date-picker-container">
                            <label class="form-label">Appointment Date </label>
                            <input type="date" class="form-control" id="appointment_date" name="appointment_date"
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
    {{-- handling the steps and validation script --}}
    <script>
        // ----//----//----//----//----//----// handling the steps and validation   ----//----//----//----//----//


        // give each input and select under the step1 id a custom attribute "data-was-required" with value "true"
        document.querySelectorAll('#step1 input[required], #step1 select[required]').forEach(field => {
            field.setAttribute('data-was-required', 'true');
        });


        // Multi-step form navigation
        let form = document.getElementById('appointmentForm');
        let steps = document.querySelectorAll('.form-step');
        let progressBar = document.querySelector('.progress-bar');
        let indicators = document.querySelectorAll('.step-indicator');
        let currentStep = 0;

        // Initialize form steps by showing the first step(0)
        function initSteps() {
            steps.forEach((step, index) => {
                if (index === 0) {
                    step.classList.add('active');
                } else {
                    step.classList.remove('active');
                }
            });
            updateProgress(0);
        }


        // Go to specific step
        // in html directly
        window.goToStep = function(stepNumber) {
            steps.forEach((step, index) => {
                if (index === stepNumber) {
                    step.classList.add('active');
                } else {
                    step.classList.remove('active');
                }
            });
            currentStep = stepNumber;
            updateProgress(stepNumber);
        };


        // Next step with validation
        window.nextStep = function(currentStepNumber) {
            let currentStepElement = document.getElementById(`step${currentStepNumber}`);
            let inputs = currentStepElement.querySelectorAll(
                // select required inputs, select, textarea without the disabled ones
                'input[required]:not(:disabled), select[required]:not(:disabled), textarea[required]:not(:disabled)'
            );

            // Validate required fields
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

            goToStep(currentStepNumber + 1);
        };

        // Previous step
        window.prevStep = function(currentStepNumber) {
            goToStep(currentStepNumber - 1);
        };


        // Update progress bar and indicators
        function updateProgress(stepNumber) {
            let progress = ((stepNumber) / (steps.length - 1)) * 100; // (4  / 4 steps - 1) * 100
            // console.log(stepNumber);
            // console.log(steps.length);

            progressBar.style.width = `${progress}%`;

            indicators.forEach((indicator, index) => {
                if (index < stepNumber) {
                    indicator.classList.add('completed');
                    indicator.classList.remove('active');
                } else if (index === stepNumber) {
                    indicator.classList.add('active');
                    indicator.classList.remove('completed');
                } else {
                    indicator.classList.remove('active', 'completed');
                }
            });
        }
    </script>



    {{-- handling the search and its functionality --}}
    <script>
        // ------/------/------/------/------/handling the search and its functionality------/------/------/------/------/

        let patientSearchResponse = null;

        // Patient Search Functionality
        document.getElementById('searchPatientBtn').addEventListener('click', function() {
            const phone = document.getElementById('patientSearchPhone').value;
            const email = document.getElementById('patientSearchEmail').value;

            if (!phone && !email) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Missing Information',
                    text: 'Please enter a phone number or email to search',
                    confirmButtonText: 'OK'
                });
                return;
            }

            $.ajax({
                url: '/dashboard/patients/search',
                method: 'GET',
                data: { // Data to send to the server
                    phone,
                    email
                },
                beforeSend: function() {
                    // Show loading state
                    $('#searchPatientBtn').html(
                            '<i class="fas fa-spinner fa-spin me-2"></i>Searching...')
                        .prop( // Disables the button to prevent multiple requests.
                            // prop: "property" and is used to get or set properties of HTML elements, like disabled, checked, selected

                            'disabled', true);
                },
                success: function(response) {
                    patientSearchResponse = response; // Store the response
                    if (response.success && response.patient) {
                        // Set the existing patient ID
                        document.getElementById('existingPatientId').value = response.patient.id;

                        // Show patient details
                        document.getElementById('patientDetails').innerHTML =
                            `
                            <div class="patient-info">
                                <p><strong>Name:</strong> ${response.patient.fname} ${response.patient.lname}</p>
                                <p><strong>Phone:</strong> ${response.patient.phone}</p>
                                <p><strong>Email:</strong> ${response.patient.email || 'Not Recorded'}</p>
                                <p><strong>DOB:</strong> ${response.patient.DOB ? new Date(response.patient.DOB).toLocaleDateString() : 'Not Recorded'}</p>
                                <p><strong>Last Visit:</strong> ${response.last_visit ? new Date(response.last_visit).toLocaleDateString() : 'Never'}</p>
                            </div>
                        `;

                        // Show the results and next button
                        document.getElementById('patientSearchResults').style.display = 'block';
                    } else {
                        Swal.fire({
                            icon: 'info',
                            title: 'Patient Not Found',
                            text: 'No patient found with those details. Please check the information or create a new patient.',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(
                    xhr) { //  XMLHttpRequest (XHR) object (holding the error and the request status)
                    console.error('Search error:', xhr);
                    Swal.fire({
                        icon: 'error',
                        title: 'Search Failed',
                        text: xhr.responseJSON?.message ||
                            'An error occurred while searching for the patient',
                        confirmButtonText: 'OK'
                    });
                },
                complete: function() { // Code to run after request finishes (success or error)
                    // Reset button state
                    $('#searchPatientBtn').html('<i class="fas fa-search me-2"></i>Search Patient')
                        .prop('disabled', false);
                }
            });
        });

        // Use existing patient button
        document.getElementById('useExistingPatient').addEventListener('click', function() {
            const patientId = document.getElementById('existingPatientId').value;

            if (patientId) {
                const patient = patientSearchResponse.patient;

                // Disable all input and textarea fields in Step 1 and Step 2
                document.querySelectorAll('#step1 input, #step1 select, #step2 input, #step2 textarea')
                    .forEach(field => {
                        field.disabled = true;
                    });

                // Remove 'required' attributes (optional, if form validation should be skipped)
                document.querySelectorAll('[required]').forEach(field => {
                    field.removeAttribute('required');
                });

                // Fill the form fields with patient data
                document.querySelector('input[name="fname"]').value = patient.fname || '';
                document.querySelector('input[name="lname"]').value = patient.lname || '';
                document.querySelector('input[name="phone"]').value = patient.phone || '';
                document.querySelector('input[name="email"]').value = patient.email || '';

                if (patient.DOB) {
                    const cleanedDOB = patient.DOB.split('T')[0].replace(/\D+$/,
                        ''); // to seperate the date form the time
                    document.querySelector('input[name="DOB"]').value = cleanedDOB;
                }

                if (patient.gender) {
                    document.querySelector('select[name="gender"]').value = patient.gender;
                }

                // Fill Step 2 fields (medical history, allergies, emergency contacts)
                document.querySelector('textarea[name="medical_history"]').value = patient.medical_history || '';
                document.querySelector('textarea[name="allergies"]').value = patient.allergies || '';
                document.querySelector('input[name="Emergency_contact_name"]').value = patient
                    .Emergency_contact_name || '';
                document.querySelector('input[name="Emergency_contact_phone"]').value = patient
                    .Emergency_contact_phone || '';

                // Proceed to Step 3
                goToStep(3);
            }
        });

        // New patient button
        document.getElementById('newPatientBtn').addEventListener('click', function() {
            // 1. Clear existing patient ID
            document.getElementById('existingPatientId').value = '';

            // 2. Reset Step 1 (Personal Info)
            const step1Fields = document.querySelectorAll('#step1 input, #step1 select, #step1 textarea');
            step1Fields.forEach(field => {
                field.value = ''; // Clear value
                field.disabled = false; // Re-enable if disabled
                field.classList.remove('is-invalid'); // Remove validation errors

                // Restore required status if it was originally required
                if (field.hasAttribute('data-was-required')) {
                    field.setAttribute('required', '');
                    field.removeAttribute('data-was-required');
                }
            });

            // 3. Reset Step 2 (Medical History)
            const step2Fields = document.querySelectorAll('#step2 input, #step2 select, #step2 textarea');
            step2Fields.forEach(field => {
                field.value = ''; // Clear all medical history fields
                field.disabled = false; // Ensure enabled
                field.classList.remove('is-invalid'); // Clear validation
            });

            // 4. Clear search results and inputs
            document.getElementById('patientSearchResults').style.display = 'none';
            document.getElementById('patientSearchPhone').value = '';
            document.getElementById('patientSearchEmail').value = '';

            // 5. Reset appointment step (Step 3) if needed
            document.getElementById('service_select').selectedIndex = 0;
            document.getElementById('duration_input').value = '';
            document.getElementById('appointment_date').value = '';
            document.querySelectorAll('.time-slot.selected').forEach(slot => {
                slot.classList.remove('selected');
            });
            document.getElementById('appointment_time').value = '';
            document.getElementById('staff_select').innerHTML =
                '<option value="">Select service, date and time first</option>';

            // 6. Go to Step 1
            goToStep(1);
        });
    </script>



    {{-- handling the walk ins --}}
    <script>
        const regularBookingBtn = document.getElementById('regularBookingBtn');
        const walkInBtn = document.getElementById('walkInBtn');

        if (regularBookingBtn) {
            regularBookingBtn.addEventListener('click', function() {
                switchForm(false);
            });
        }

        if (walkInBtn) {
            walkInBtn.addEventListener('click', function() {
                switchForm(true);
            });
        }


        // Form switching between regular and walk-in
        function switchForm(isWalkIn) {
            const regularBookingBtn = document.getElementById('regularBookingBtn');
            const walkInBtn = document.getElementById('walkInBtn');
            const regularBookingForm = document.getElementById('regularBookingForm');
            const walkInForm = document.getElementById('walkInForm');
            const bookingDescription = document.getElementById('bookingDescription');


            // element.classList.toggle('active');
            //- If 'active' is already present, it gets removed. , - If 'active' is missing, it gets added.

            // element.classList.toggle('active', true); // Always adds 'active'
            // element.classList.toggle('active', false); // Always removes 'active'
            // - true → Ensures the class is added.
            // - false → Ensures the class is removed


            if (regularBookingBtn) {
                regularBookingBtn.classList.toggle('active', !isWalkIn);
            }
            if (walkInBtn) {
                walkInBtn.classList.toggle('active', isWalkIn);
            }

            if (regularBookingForm) {
                regularBookingForm.style.display = isWalkIn ? 'none' : 'block';
            }
            if (walkInForm) walkInForm.style.display = isWalkIn ? 'block' : 'none';

            if (bookingDescription) {
                bookingDescription.textContent = isWalkIn ?
                    'Create an immediate appointment for a walk-in patient' :
                    'Schedule a new appointment with complete patient information';
            }

            // Reset forms when switching
            if (isWalkIn) {
                // Reset regular booking form if the walk in is choosen
                if (document.getElementById('appointmentForm')) {
                    document.getElementById('appointmentForm').reset();
                    initSteps();
                }
            } else {
                // Reset walk-in form if the regular is choosen
                if (document.getElementById('walkInAppointmentForm')) {
                    document.getElementById('walkInAppointmentForm').reset();
                }
            }
        }
    </script>


    {{-- handling the slots and dentists appearing and submitting the form --}}
    <script>
        // Time slot selection functionality
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
        if (appointmentDateInput) {
            let today = new Date();
            let yyyy = today.getFullYear();
            let mm = String(today.getMonth() + 1).padStart(2,
                '0'); //today.getMonth(); retrieves the months by index,so january = 0
            let dd = String(today.getDate()).padStart(2, '0');
            let formattedToday = `${yyyy}-${mm}-${dd}`;
            appointmentDateInput.min =
                formattedToday; // the minimum value of the appointment_date input is todays appointment
            console.log(formattedToday);
        }

        // Generate time slots based on service duration
        function generateTimeSlots() {
            let serviceId = serviceSelect.value;
            let selectedDate = appointmentDateInput.value;
            let duration = parseInt(serviceSelect.options[serviceSelect.selectedIndex].dataset.duration ||
                30); //selectedIndex returns the index of the currently selected option.


            if (!serviceId || !selectedDate) {
                timeSlotContainer.classList.remove('active');
                availabilityMessage.style.display = 'block';
                availabilityMessage.textContent = 'Please select a service and date to see available time slots.';
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
                url: `/dashboard/appointments/get-available-slots`,
                method: 'GET',
                data: {
                    service_id: serviceId,
                    date: selectedDate,
                    duration: duration
                },
                success: function(response) {
                    // console.log(response);
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
                                    document.querySelectorAll('.time-slot')
                                        .forEach(s => s.classList.remove('selected'));
                                    // Add selection to this slot
                                    this.classList.add('selected');
                                    // Update hidden inputs with selected time
                                    appointmentTimeInput.value = this.dataset.time;
                                    appointmentDateTimeInput.value = this.dataset.dateTime;
                                    // Update available dentists for this time slot
                                    updateAvailableDentists(serviceId, this.dataset.dateTime);
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
                        availabilityMessage.textContent = 'No available time slots for the selected date.';
                        staffSelect.innerHTML = '<option value=""> No time slots available</option>';
                        staffSelect.disabled = true;
                    }
                },
                error: function(error) {
                    console.error('Error loading time slots:', error);
                    timeSlotContainer.classList.remove('active');
                    availabilityMessage.style.display = 'block';
                    availabilityMessage.textContent = 'Error loading time slots.';
                }
            });
        }

        // Update available dentists
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
                    if (data.success && data.dentists) {
                        // console.log(data.dentists); // object
                        // console.log(data.success); // true or false

                        let activeDentists = data.dentists.filter(dentist => dentist
                            .is_active); // retrieve dentists with active status

                        if (activeDentists.length > 0) {
                            staffSelect.innerHTML = '<option value="">Select Dentist</option>';
                            activeDentists.forEach(dentist => {
                                staffSelect.innerHTML +=
                                    `<option value="${dentist.id}">${dentist.user ? dentist.user.name : 'Unknown'}</option>`;
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
                    staffSelect.innerHTML = '<option value="">Error loading dentists</option>';
                    staffSelect.disabled = true;
                }
            });
        }

        // Event listeners for the appointment booking form
        if (serviceSelect) {
            serviceSelect.addEventListener('change', function() {
                let selectedOption = this.options[this.selectedIndex];
                if (durationInput) {
                    durationInput.value = selectedOption.dataset.duration ||
                        ''; // store in the duration field the value in the data-duration in the service select
                }
                if (appointmentTimeInput) {
                    appointmentTimeInput.value = '';
                }
                if (appointmentDateTimeInput) {
                    appointmentDateTimeInput.value = '';
                }

                if (appointmentDateInput && appointmentDateInput.value) {
                    generateTimeSlots();
                } else if (timeSlotContainer) {
                    timeSlotContainer.classList.remove('active');
                    if (availabilityMessage) {
                        availabilityMessage.style.display = 'block';
                        availabilityMessage.textContent = 'Please select a date to see available time slots.';
                    }
                }
            });
        }

        if (appointmentDateInput) {
            appointmentDateInput.addEventListener('change', function() {
                if (appointmentTimeInput) {
                    appointmentTimeInput.value = '';
                }
                if (appointmentDateTimeInput) {
                    appointmentDateTimeInput.value = '';
                }

                if (serviceSelect && serviceSelect.value) {
                    generateTimeSlots();
                } else if (timeSlotContainer) {
                    timeSlotContainer.classList.remove('active');
                    if (availabilityMessage) {
                        availabilityMessage.style.display = 'block';
                        availabilityMessage.textContent =
                            'Please select a service to see available time slots.';
                    }
                }
            });
        }



        // Form submission validation
        if (form) {
            // In your form submission event listener
            form.addEventListener('submit', function(e) {
                // If using existing patient, remove required attributes from step 1 fields
                // if (document.getElementById('existingPatientId').value) {
                //     document.querySelectorAll('#step1 input[required], #step1 select[required]')
                //         .forEach(field => {
                //             field.removeAttribute('required');
                //             field.removeAttribute('aria-required');
                //         });
                // }

                // Show loading state
                let submitBtn = document.getElementById('bookAppointmentBtn');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML =
                        '<span class="spinner-border spinner-border-sm me-2"></span>Booking...';
                }
            });
        }
    </script>



    {{-- walk in for handling --}}

    <script>
        // Walk-in form handling
        let walkInServiceSelect = document.getElementById('walkInServiceSelect');
        let walkInStaffSelect = document.getElementById('walkInStaffSelect');
        let serviceDurationInput = document.getElementById('serviceDuration');
        // let servicePriceInput = document.getElementById('servicePrice');

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
                // let price = selectedOption.getAttribute('data-price');
                serviceDurationInput.value = duration ? `${duration} minutes` : '';
                // if (servicePriceInput) {
                //     servicePriceInput.value = price ? `${price}` : '';
                // }
            } else {
                serviceDurationInput.value = '';
                // if (servicePriceInput) {
                //     servicePriceInput.value = '';
                // }
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
                url: `/dashboard/appointments/get-available-dentists?service_id=${serviceId}&is_walk_in=true`, // query parameters
                method: 'GET',
                success: function(data) {
                    // console.log('Walk-in API Response:', data);
                    // console.log('All walk-in dentists returned:', data.dentists);

                    if (data.success && data.dentists) {
                        // Filter to ensure only active dentists are shown
                        let activeDentists = data.dentists.filter(dentist => dentist.is_active);
                        // console.log('Filtered active walk-in dentists:', activeDentists);

                        if (activeDentists.length > 0) {
                            walkInStaffSelect.innerHTML =
                                '<option value="">Select Dentist</option>';
                            activeDentists.forEach(dentist => {
                            //     console.log(
                            //         `Adding walk-in dentist: ${dentist.id}, Name: ${dentist.user ? dentist.user.name : 'Unknown'}, Active: ${dentist.is_active}`
                            //     );
                                walkInStaffSelect.innerHTML +=
                                    `<option value="${dentist.id}">${dentist.user ? dentist.user.name : 'Unknown'}</option>`;
                            });
                            walkInStaffSelect.disabled = false;
                        } else {
                            // console.log('no active dentists available');
                            walkInStaffSelect.innerHTML ='<option value="">No dentists available</option>';
                            walkInStaffSelect.disabled = true;
                        }
                    } else {
                        // console.log('No walk-in dentists data in the response or response not successful');
                        walkInStaffSelect.innerHTML = '<option value="">No dentists available</option>';
                        walkInStaffSelect.disabled = true;
                    }
                },
                error: function(error) {
                    console.error('Walk-in AJAX Error:', error);
                    walkInStaffSelect.innerHTML ='<option value="">Error loading dentists</option>';
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
                // if (servicePriceInput) {
                //     servicePriceInput.value = '';
                // }
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
    </script>


    {{-- <script>
        // Walk-in form handling
        let walkInServiceSelect = document.getElementById('walkInServiceSelect');
        let walkInStaffSelect = document.getElementById('walkInStaffSelect');
        let serviceDurationInput = document.getElementById('serviceDuration');
        // let servicePriceInput = document.getElementById('servicePrice');

        if (walkInServiceSelect) {
            walkInServiceSelect.addEventListener('change', function() {
                updateWalkInDentists();
                updateServiceDetails();
            });
        }

        function updateServiceDetails() {
            let selectedOption = selectedOption walkInServiceSelect.options[walkInServiceSelect.selectedIndex];
            if (selectedOption && selectedOption.value) {
                let duration = selectedOption.getAttribute('data-duration');
                // let price = selectedOption.getAttribute('data-price');
                serviceDurationInput.value = duration ? `${duration} minutes` : '';
                // if (servicePriceInput) {
                //     servicePriceInput.value = price ? `${price}` : '';
                // }
            } else {
                serviceDurationInput.value = '';
                if (servicePriceInput) {
                    servicePriceInput.value = '';
                }
            }
        }

        function updateWalkInDentists() {
            let serviceId = walkInServiceSelect.value;
            // window.alert(serviceId);
            if (!serviceId) {
                // window.alert(5);
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
    </script> --}}
@endpush
