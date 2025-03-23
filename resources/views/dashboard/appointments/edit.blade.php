@extends('layouts.master.master')

@section('title', 'Edit Appointment')

@section('content')
    <form action="{{ route('dashboard.appointments.update', $appointment->id) }}" method="post">
        @method('put')
        @csrf

        <div class="row mb-3">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">Patient:</label>
                    <select name="patient_id" id="patientSelect" class="form-select @error('patient_id') is-invalid @enderror">
                        <option selected disabled>--select--</option>
                        @foreach ($patients as $patient)
                            <option value="{{ $patient->id }}" @selected(old('patient_id', $appointment->patient_id) == $patient->id)>{{ $patient->fname }} {{ $patient->lname }}</option>
                        @endforeach
                    </select>
                    @error('patient_id')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">Service:</label>
                    <select name="service_id" id="serviceSelect" class="form-select @error('service_id') is-invalid @enderror">
                        <option selected disabled>--select--</option>
                        @foreach ($services as $service)
                            <option value="{{ $service->id }}" data-duration="{{ $service->duration }}" @selected(old('service_id', $appointment->service_id) == $service->id)>{{ $service->service_name }}</option>
                        @endforeach
                    </select>
                    @error('service_id')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">Appointment Date:</label>
                    <input type="date" id="appointmentDateInput" class="form-control date"
                        value="{{ old('appointment_date', \Carbon\Carbon::parse($appointment->appointment_date)->format('Y-m-d')) }}">
                    @error('appointment_date')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Time Slots -->
        <div class="col-12 time-slot-container" id="timeSlotContainer">
            <label class="form-label">Available Time Slots</label>
            <p id="availabilityMessage" style="display: none;">Please select a service and date to see available time slots.</p>
            <div class="time-slots" id="timeSlots">
                <!-- Time slots will be dynamically populated -->
            </div>
            <!-- Hidden input to store the selected time -->
            <input type="hidden" name="appointment_time" id="appointment_time" value="{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('H:i') }}" required>
            <!-- Hidden input to store the combined date and time -->
            <input type="hidden" name="appointment_date_time" id="appointment_date_time"
                value="{{ old('appointment_date_time', $appointment->appointment_date) }}" required>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">Dentist:</label>
                    <select name="staff_id" id="staffSelect" class="form-select @error('staff_id') is-invalid @enderror">
                        <option selected disabled>--select--</option>
                        @foreach ($staff as $staffMember)
                            <option value="{{ $staffMember->id }}" @selected(old('staff_id', $appointment->staff_id) == $staffMember->id)>{{ $staffMember->user->name }}</option>
                        @endforeach
                    </select>
                    @error('staff_id')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <div class="form-group">
                <label class="form-label">Duration (minutes):</label>
                <input type="text" name="duration" class="form-control"
                    value="{{ old('duration', $appointment->duration) }}" required readonly>
            </div>
        </div>

        <div class="form-group w-50 m-2">
            <label>Status:</label>
            <select name="status" class="form-control">
                <option value="scheduled" @selected(old('status', $appointment->status) == 'scheduled')>scheduled</option>
                <option value="rescheduled" @selected(old('status', $appointment->status) == 'rescheduled')>rescheduled</option>
                <option value="completed" @selected(old('status', $appointment->status) == 'completed')>completed</option>
                <option value="canceled" @selected(old('status', $appointment->status) == 'canceled')>canceled</option>
                <option value="walk_in" @selected(old('status', $appointment->status) == 'walk_in')>walk_in</option>
            </select>
            @error('status')
                <small class="text-danger alert-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="col-12">
            <x-form.textarea label='Additional Notes' name='notes' rows="2"
                :value='$appointment->notes' />
        </div>

        <div class="col-12">
            <x-form.textarea label='Cancellation Reason' name='cancellation_reason' rows="2"
                :value='$appointment->cancellation_reason' />
        </div>

        <button type="submit" class="btn btn-primary m-2">Save Changes</button>
    </form>
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const serviceSelect = document.getElementById('serviceSelect');
            const appointmentDateInput = document.getElementById('appointmentDateInput');
            const timeSlotContainer = document.getElementById('timeSlotContainer');
            const timeSlots = document.getElementById('timeSlots');
            const availabilityMessage = document.getElementById('availabilityMessage');
            const appointmentTimeInput = document.getElementById('appointment_time');
            const appointmentDateTimeInput = document.getElementById('appointment_date_time');
            const staffSelect = document.getElementById('staffSelect');
            const currentAppointmentId = '{{ $appointment->id }}';

            // Initialize by loading time slots based on current selected service and date
            if (serviceSelect.value && appointmentDateInput.value) {
                generateTimeSlots();
            }

            // Add event listeners for changes
            serviceSelect.addEventListener('change', generateTimeSlots);
            appointmentDateInput.addEventListener('change', generateTimeSlots);

            function generateTimeSlots() {
                let serviceId = serviceSelect.value;
                let selectedDate = appointmentDateInput.value;
                let duration = parseInt(serviceSelect.options[serviceSelect.selectedIndex].dataset.duration || 30);

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
                        duration: duration,
                        current_appointment_id: currentAppointmentId // Pass current appointment ID to exclude it from the check
                    },
                    success: function(response) {
                        console.log("Response from server:", response);

                        if (response.success && response.slots && response.slots.length > 0) {
                            timeSlotContainer.classList.add('active');
                            availabilityMessage.style.display = 'none';

                            // Clear previous slots
                            timeSlots.innerHTML = '';

                            // Get current selected time
                            const currentTime = appointmentTimeInput.value;

                            // Create time slots based on response
                            response.slots.forEach(slot => {
                                let timeSlot = document.createElement('div');
                                timeSlot.classList.add('time-slot');

                                if (slot.available || slot.time === currentTime) {
                                    timeSlot.textContent = slot.time;
                                    timeSlot.dataset.time = slot.time;
                                    timeSlot.dataset.dateTime = `${selectedDate}T${slot.time}`;

                                    // Pre-select current time slot
                                    if (slot.time === currentTime) {
                                        timeSlot.classList.add('selected');
                                    }

                                    timeSlot.addEventListener('click', function() {
                                        // Remove selection from all slots
                                        document.querySelectorAll('.time-slot').forEach(s => s.classList.remove('selected'));
                                        // Add selection to this slot
                                        this.classList.add('selected');
                                        // Update hidden inputs with selected time
                                        appointmentTimeInput.value = this.dataset.time;
                                        appointmentDateTimeInput.value = this.dataset.dateTime;
                                        // Update available dentists
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
                            staffSelect.innerHTML = '<option value="">No time slots available</option>';
                            staffSelect.disabled = true;
                        }
                    },
                    error: function(error) {
                        console.error('Error loading time slots:', error);
                        console.error('Error details:', error.responseText);
                        timeSlotContainer.classList.remove('active');
                        availabilityMessage.style.display = 'block';
                        availabilityMessage.textContent = 'Error loading time slots. Please try again.';
                    }
                });
            }

            function updateAvailableDentists(serviceId, dateTime) {
                $.ajax({
                    url: `/dashboard/appointments/get-available-staff`,
                    method: 'GET',
                    data: {
                        service_id: serviceId,
                        date_time: dateTime,
                        current_appointment_id: currentAppointmentId
                    },
                    success: function(response) {
                        if (response.success && response.staff) {
                            staffSelect.innerHTML = '';

                            if (response.staff.length > 0) {
                                staffSelect.disabled = false;

                                response.staff.forEach(staff => {
                                    let option = document.createElement('option');
                                    option.value = staff.id;
                                    option.textContent = staff.name;

                                    // Pre-select the current staff if it matches
                                    if (staff.id == '{{ $appointment->staff_id }}') {
                                        option.selected = true;
                                    }

                                    staffSelect.appendChild(option);
                                });
                            } else {
                                staffSelect.innerHTML = '<option value="">No staff available for this time</option>';
                                staffSelect.disabled = true;
                            }
                        } else {
                            staffSelect.innerHTML = '<option value="">No staff available</option>';
                            staffSelect.disabled = true;
                        }
                    },
                    error: function(error) {
                        console.error('Error loading available staff:', error);
                        staffSelect.innerHTML = '<option value="">Error loading staff</option>';
                        staffSelect.disabled = true;
                    }
                });
            }
        });
    </script>
@endpush
