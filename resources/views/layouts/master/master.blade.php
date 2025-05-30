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
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400&family=Montserrat:wght@400&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('front/css/style.css') }}">

    <style>
        /* Variables for consistent theming */
        :root {
            --primary-color: #124063;
            --primary-hover: #238F89;
            --secondary-color: #c2c7c7;
            --accent-color: #567da8;
            --text-muted: #718096;
            --border-color: #E2E8F0;
            --light-bg: #F8FAFC;
            --input-focus: rgba(42, 167, 160, 0.25);

            /* Bootstrap overrides using your colors */
            --bs-primary: var(--primary-color);
            /* #122e44 */
            --bs-primary-rgb: 18, 46, 68;
            --bs-primary-bg-subtle: #f8fafc;
            /* Light version of your primary */

            /* Link colors */
            --bs-link-color: var(--primary-color);
            --bs-link-hover-color: var(--primary-hover);

            /* Focus styles */
            --bs-focus-ring-color: var(--input-focus);
        }

        /* Outline button customization */
        .btn-outline-primary {
            --bs-btn-color: var(--primary-color);
            --bs-btn-border-color: var(--primary-color);
            --bs-btn-hover-color: #fff;
            --bs-btn-hover-bg: var(--primary-color);
            --bs-btn-hover-border-color: var(--primary-color);
            --bs-btn-focus-shadow-rgb: var(--bs-primary-rgb);
            --bs-btn-active-color: #fff;
            --bs-btn-active-bg: var(--primary-color);
            --bs-btn-active-border-color: var(--primary-color);
            --bs-btn-disabled-color: var(--primary-color);
            --bs-btn-disabled-border-color: var(--primary-color);
        }

        /* Solid primary button */
        .btn-primary {
            --bs-btn-bg: var(--primary-color);
            --bs-btn-border-color: var(--primary-color);
            --bs-btn-hover-bg: var(--primary-hover);
            --bs-btn-hover-border-color: var(--primary-hover);
            --bs-btn-active-bg: var(--primary-hover);
            --bs-btn-active-border-color: var(--primary-hover);
        }

        /* Optional: Apply your secondary color to Bootstrap's secondary */
        .btn-secondary {
            --bs-btn-bg: var(--secondary-color);
            --bs-btn-border-color: var(--secondary-color);
            --bs-btn-color: var(--text-dark);
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
        }

        small {
            font-weight: 300;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem var(--input-focus);
        }

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
    <div class="sidebar-overlay" onclick="closeSidebar()"></div>

    <div class="container-fluid">
        <div class="row">
            @auth
                <div class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                    <div class="position-sticky pt-3" style="top: 0; height: 100vh; overflow-y: auto">
                        <div class="text-center mb-4 ">
                            <div class="d-flex align-items-center ">
                                <img src="{{ asset('front/assets/icons/final_logo.png') }}" class="m-0 p-0"
                                    style="filter: brightness(0) invert(1);" height="40">
                                <a class="text-light text-decoration-none" href="{{ route('dashboard') }}"
                                    style="font-family: 'Playfair Display', serif; font-weight: 600;">
                                    <h2>OralOasis</h2>
                                </a>
                            </div>
                            <hr>


                            <div style="display: inline-block; vertical-align: middle;">
                                @if (Auth::user()->profile->profile_photo)
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

                            <hr>

                        </div>
                        @include('layouts.parts.sidebar')
                    </div>
                </div>
            @endauth


            <div class="col-md-9 col-lg-10">
                <!-- Navbar -->
                <nav class="navbar navbar-expand-lg navbar-light mb-4">
                    <div class="container-fluid">
                        {{-- <button class="navbar-toggler border-0 " type="button" onclick="toggleSidebar()">
                            <span class="navbar-toggler-icon"></span>
                        </button> --}}
                        <ul class="navbar-nav ms-auto">

                            <x-notifications.notifications-dropdown maxNotifications="3" />

                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button"
                                    data-bs-toggle="dropdown">
                                    <i class="fas fa-user-circle fa-lg"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end p-1">
                                    <li><a class="dropdown-item small"
                                            href="{{ route('dashboard.profile.edit') }}">Profile</a></li>
                                    <hr class="dropdown-divider">
                                    <li>
                                        <a class="dropdown-item nav-link small" href="{{ route('password.change') }}">
                                            change password</a>
                                    </li>
                                    <hr class="dropdown-divider">
                                    <li>
                                        <form action="{{ route('logout') }}" method="post">
                                            @csrf
                                            <button class="btn dropdown-item small" type="submit">Logout</button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>


                <div class="content">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('front/js/script.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    @stack('js')
    <script>
        let flash_msg = document.querySelector('#flash-msg');
        window.setTimeout(() => {
            flash_msg.remove();
        }, 3000);
    </script>

</body>

</html>
