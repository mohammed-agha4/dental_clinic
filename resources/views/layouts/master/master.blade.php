<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('front/css/style.css') }}">

    <style>
        /* Variables for consistent theming */
        :root {
            /* --primary-color: #0d6efd; */
            --primary-hover: #0b5ed7;
            --text-dark: #212529;
            --text-muted: #6c757d;
            --border-color: #dee2e6;
            --light-bg: #f8f9fa;
            --input-focus: rgba(13, 110, 253, 0.25);
            --spacing-sm: 0.75rem;
            --spacing-md: 1.5rem;
            --spacing-lg: 2.5rem;
            --border-radius: 0.375rem;
            --box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        /* Base form styling */
        .patient-form-wrapper {
            padding: var(--spacing-md);
            background-color: #fff;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }

        .patient-form-header {
            margin-bottom: var(--spacing-md);
            padding-bottom: var(--spacing-sm);
            border-bottom: 1px solid var(--border-color);
        }

        .patient-form-header h3 {
            color: var(--text-dark);
            font-weight: 600;
            margin-bottom: 0;
        }

        /* Form group spacing and layout */
        .form-row {
            display: flex;
            gap: var(--spacing-md);
            margin-bottom: var(--spacing-md);
        }

        .form-col {
            flex: 1;
        }

        /* Form controls enhancements */
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem var(--input-focus);
        }

        /* Radio button styling */
        .gender-container {
            background-color: var(--light-bg);
            padding: var(--spacing-md);
            border-radius: var(--border-radius);
            margin-bottom: var(--spacing-md);
        }

        .gender-label {
            display: block;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: var(--spacing-sm);
        }

        .gender-options {
            display: flex;
            gap: var(--spacing-lg);
        }

        /* Text area specific styling */
        .form-textarea {
            min-height: 120px;
            resize: vertical;
        }

        /* Emergency contact section */
        .emergency-contact {
            background-color: var(--light-bg);
            padding: var(--spacing-md);
            border-radius: var(--border-radius);
            margin-top: var(--spacing-md);
            margin-bottom: var(--spacing-md);
        }

        .emergency-title {
            font-weight: 600;
            margin-bottom: var(--spacing-sm);
            color: var(--text-dark);
        }

        /* Submit button styling */
        .submit-button {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 0.5rem 1.5rem;
            font-weight: 500;
            box-shadow: var(--box-shadow);
        }

        .submit-button:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
                gap: var(--spacing-sm);
            }
        }
    </style>


    <style>
        .side-icon {
            height: 18px;
            width: 18px;
            margin-right: 10px !important;
        }

        hr {
            margin: 10px 0 8px 0;
        }
    </style>
    @yield('css')
</head>

<body>
    <!-- Overlay for mobile -->
    <div class="sidebar-overlay" onclick="closeSidebar()"></div>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            @auth

                <div class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                    <div class="position-sticky pt-3" style="top: 0; height: 100vh; overflow-y: auto">
                        <div class="text-center mb-4">
                            <h4>OralOasis</h4>
                            <hr>
                            <img src="https://placehold.co/30x30" alt=""
                                style="height: 30px; width: 30px; border-radius: 50%">
                            <strong class="small">{{ Auth::User()->name }}</strong>
                            <hr>
                        </div>
                        @include('layouts.parts.sidebar')
                    </div>
                </div>
            @endauth

            <!-- Main Content Area with Navbar -->
            <div class="col-md-9 col-lg-10">
                <!-- Navbar -->
                <nav class="navbar navbar-expand-lg navbar-light mb-4">
                    <div class="container-fluid">
                        <button class="navbar-toggler border-0" type="button" onclick="toggleSidebar()">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="d-flex align-items-center">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search...">
                                <button class="btn btn-outline-secondary" type="button">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <ul class="navbar-nav ms-auto">



                            <x-notifications.notifications-dropdown maxNotifications="3" />





                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button"
                                    data-bs-toggle="dropdown">
                                    <i class="fas fa-user-circle"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="#">Profile</a></li>
                                    <li><a class="dropdown-item" href="#">Settings</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <form action="{{ route('logout') }}" method="post">
                                            @csrf
                                            <button class="btn dropdown-item" type="submit">Logout</button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>

                <!-- Main Content Area -->
                <div class="content">
                    @yield('content')
                    <div id="displayed-content" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('front/js/script.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('js')
    {{-- <script>
        let flash_msg = document.querySelector('#flash-msg');
        window.setTimeout(() => {
            flash_msg.remove();
        }, 3000);
    </script> --}}


    <script>
        let appointmentDateInput = document.querySelectorAll('.date');
        let today = new Date();
        let yyyy = today.getFullYear();
        let mm = String(today.getMonth() + 1).padStart(2, '0');
        let dd = String(today.getDate()).padStart(2, '0');
        let formattedToday = `${yyyy}-${mm}-${dd}`;

        appointmentDateInput.forEach(element => {
            element.min = formattedToday;
        });
    </script>
</body>

</html>
