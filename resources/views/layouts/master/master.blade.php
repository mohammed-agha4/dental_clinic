<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.veryicon.com/path-to-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('front/css/style.css') }}">
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400&family=Montserrat:wght@400&display=swap"
        rel="stylesheet">

    <style>
        /* Variables for consistent theming */
        :root {
    /* New color scheme */
    --primary-color: #122e44;  /* Professional teal */
    --primary-hover: #238F89;  /* Slightly darker teal */
    --secondary-color: #E0F2F1;  /* Very light teal */
    --accent-color: #567da8;  /* Warm orange for actions */
    --text-dark: #2D3748;  /* Dark gray-blue */
    --text-muted: #718096;  /* Medium gray */
    --border-color: #E2E8F0;  /* Light gray */
    --light-bg: #F8FAFC;  /* Very light gray */
    --input-focus: rgba(42, 167, 160, 0.25);  /* Teal with transparency */

    /* Spacing and other variables remain the same */
    --spacing-sm: 0.75rem;
    --spacing-md: 1.5rem;
    --spacing-lg: 2.5rem;
    --border-radius: 0.375rem;
    --box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}


        body {
            font-family: 'Montserrat', sans-serif;
        }

        th {
            color: rgba(0, 0, 0, 0.485) !important
        }

        h1,
        h2,
        h3 {
            font-weight: 700;
            /* Bold for headings */
        }

        p {
            font-weight: 400;
            /* Regular for body text */
        }

        small {
            font-weight: 300;
            /* Light for subtle text */
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
                        <div class="text-center mb-4 ">
                            <div class="d-flex align-items-center ">
                                <img src="{{ asset('front/assets/icons/final_logo.png') }}" class="m-0 p-0"
                                    style="filter: brightness(0) invert(1);" height="40" alt="">
                                <a class="text-light text-decoration-none" href="{{ route('dashboard') }}"
                                    style="font-family: 'Playfair Display', serif; font-weight: 600;">
                                    <h2>OralOasis</h2>
                                </a>
                            </div>
                            <hr>


                            @auth
                                <div style="display: inline-block; vertical-align: middle;">
                                    @if (Auth::user()->profile && Auth::user()->profile->profile_photo)
                                        <img src="{{ asset('storage/' . Auth::user()->profile->profile_photo) }}"
                                            style="height: 40px; width: 40px; border-radius: 50%; object-fit: cover;">
                                    @else
                                        @php
                                            $name = urlencode(Auth::user()->name);
                                            $avatarUrl = "https://ui-avatars.com/api/?name={$name}&background=random&color=fff&size=40";
                                        @endphp
                                        <img src="{{ $avatarUrl }}" style="height: 30px; width: 30px; border-radius: 50%;">
                                    @endif
                                </div>
                                <a class="text-decoration-none text-white" href="{{ route('dashboard.profile.edit') }}">
                                    {{ ucfirst(Auth::User()->name) }}
                                </a>
                            @endauth


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

                        <ul class="navbar-nav ms-auto">



                            <x-notifications.notifications-dropdown maxNotifications="3" />



                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button"
                                    data-bs-toggle="dropdown">
                                    <i class="fas fa-user-circle"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item"
                                            href="{{ route('dashboard.profile.edit') }}">Profile</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <a class="nav-link {{ request()->routeIs('password.change') ? 'active' : '' }}"
                                            href="{{ route('password.change') }}">
                                            </i>change password
                                        </a>
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
                    <div id="displayed-content" class="mt-3">
                        {{-- code here --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('front/js/script.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('js')
    <script>
        let flash_msg = document.querySelector('#flash-msg');
        window.setTimeout(() => {
            flash_msg.remove();
        }, 3000);
    </script>



    {{-- <script>
        let appointmentDateInput = document.querySelectorAll('.date');
        let today = new Date();
        let yyyy = today.getFullYear();
        let mm = String(today.getMonth() + 1).padStart(2, '0');
        let dd = String(today.getDate()).padStart(2, '0');
        let formattedToday = `${yyyy}-${mm}-${dd}`;

        appointmentDateInput.forEach(element => {
            element.min = formattedToday;
        });



        if (appointmentDateInput) {
            let today = new Date();
            let yyyy = today.getFullYear();
            let mm = String(today.getMonth() + 1).padStart(2, '0');
            let dd = String(today.getDate()).padStart(2, '0');
            let formattedToday = `${yyyy}-${mm}-${dd}`;
            appointmentDateInput.min = formattedToday;
        }
    </script> --}}
</body>

</html>
