@extends('layouts.master.master')
@section('title', 'pass change')

@section('css')
    <style>
        body {
            background-color: #f8f9fa;
        }

        .password-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 30px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .password-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .password-header h2 {
            color: #333;
        }

        .form-group {
            margin-bottom: 20px;
        }
    </style>

@endsection


@section('content')
    <div class="container">
        <div class="password-container">
            <div class="password-header">
                <h2>Change Password</h2>
                <p class="text-muted">Please enter your current and new password</p>
            </div>

            <form id="changePasswordForm">
                <div class="form-group">
                    <label for="currentPassword" class="form-label">Current Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="currentPassword" required>
                        <button class="btn btn-outline-secondary toggle-password" type="button">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="newPassword" class="form-label">New Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="newPassword" required>
                        <button class="btn btn-outline-secondary toggle-password" type="button">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirmPassword" class="form-label">Confirm New Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="confirmPassword" required>
                        <button class="btn btn-outline-secondary toggle-password" type="button">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    <div class="invalid-feedback" id="confirmPasswordFeedback">
                        Passwords do not match.
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Change Password</button>
                    <a href="#" class="btn btn-link">Forgot password?</a>
                </div>
            </form>
        </div>
    </div>


@endsection


@push('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Custom JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle password visibility
            document.querySelectorAll('.toggle-password').forEach(button => {
                button.addEventListener('click', function() {
                    const input = this.parentNode.querySelector('input');
                    const icon = this.querySelector('i');

                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.classList.remove('bi-eye');
                        icon.classList.add('bi-eye-slash');
                    } else {
                        input.type = 'password';
                        icon.classList.remove('bi-eye-slash');
                        icon.classList.add('bi-eye');
                    }
                });
            });

            // Form validation
            const form = document.getElementById('changePasswordForm');
            const newPassword = document.getElementById('newPassword');
            const confirmPassword = document.getElementById('confirmPassword');

            form.addEventListener('submit', function(e) {
                e.preventDefault();

                // Check if passwords match
                if (newPassword.value !== confirmPassword.value) {
                    confirmPassword.classList.add('is-invalid');
                    return;
                } else {
                    confirmPassword.classList.remove('is-invalid');
                }

                // Here you would typically send the data to the server
                alert('Password changed successfully!');
                form.reset();
            });

            // Live confirm password check
            confirmPassword.addEventListener('input', function() {
                if (newPassword.value !== this.value && this.value.length > 0) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });
        });
    </script>
@endpush
