<ul class="nav flex-column" id="sidebarAccordion">
    <!-- Main Navigation -->

    @can('dashboard.overview')
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <i class="side-icon fas fa-home me-2"></i>Overview
            </a>
        </li>
    @endcan

    <!-- Patient Care Section -->
    <li class="nav-item">
        <a class="nav-link dropdown-toggle {{ request()->is('dashboard/patients*') || request()->is('dashboard/appointments*') || request()->is('dashboard/visits*') ? 'active' : '' }}"
            href="#patientCareSubmenu" data-bs-toggle="collapse"
            aria-expanded="{{ request()->is('dashboard/patients*') || request()->is('dashboard/appointments*') || request()->is('dashboard/visits*') ? 'true' : 'false' }}">
            <i class="side-icon fas fa-user-md me-2"></i>Patient Care
        </a>
        <ul class="collapse list-unstyled ps-3 {{ request()->is('dashboard/patients*') || request()->is('dashboard/appointments*') || request()->is('dashboard/visits*') ? 'show' : '' }}"
            id="patientCareSubmenu" data-bs-parent="#sidebarAccordion">
            @can('patients.view')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard.patients.index') ? 'active' : '' }}"
                        href="{{ route('dashboard.patients.index') }}">
                        <i class="side-icon fas fa-users me-2"></i>Patients
                    </a>
                </li>
            @endcan
            @can('appointments.view')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard.appointments.index') ? 'active' : '' }}"
                        href="{{ route('dashboard.appointments.index') }}">
                        <i class="side-icon fas fa-calendar-alt me-2"></i>Appointments
                    </a>
                </li>
            @endcan
            @can('visits.view')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard.visits.index') ? 'active' : '' }}"
                        href="{{ route('dashboard.visits.index') }}">
                        <i class="side-icon fas fa-stethoscope me-2"></i>Visits
                    </a>
                </li>
            @endcan
        </ul>
    </li>

    <!-- Clinic Management Section -->
    <li class="nav-item">
        <a class="nav-link dropdown-toggle {{ request()->is('dashboard/services*') || request()->is('dashboard/staff*') || request()->is('dashboard/service-staff*') ? 'active' : '' }}"
            href="#clinicSubmenu" data-bs-toggle="collapse"
            aria-expanded="{{ request()->is('dashboard/services*') || request()->is('dashboard/staff*') || request()->is('dashboard/service-staff*') ? 'true' : 'false' }}">
            <i class="side-icon fa-solid fa-tooth me-2"></i>Clinic Management
        </a>
        <ul class="collapse list-unstyled ps-3 {{ request()->is('dashboard/services*') || request()->is('dashboard/staff*') || request()->is('dashboard/service-staff*') ? 'show' : '' }}"
            id="clinicSubmenu" data-bs-parent="#sidebarAccordion">
            @can('services.view')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard.services.index') ? 'active' : '' }}"
                        href="{{ route('dashboard.services.index') }}">
                        <i class="side-icon fa-solid fa-tooth me-2"></i>Services
                    </a>
                </li>
            @endcan
            @can('staff.view')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard.staff.index') ? 'active' : '' }}"
                        href="{{ route('dashboard.staff.index') }}">
                        <i class="side-icon fas fa-user-md me-2"></i>Staff
                    </a>
                </li>
            @endcan
            @can('service_staff.view')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard.service-staff.index') ? 'active' : '' }}"
                        href="{{ route('dashboard.service-staff.index') }}">
                        <i class="side-icon fas fa-link me-2"></i>Staff Services
                    </a>
                </li>
            @endcan
        </ul>
    </li>

    <!-- Inventory Section -->
    <li class="nav-item">
        <a class="nav-link dropdown-toggle {{ request()->is('dashboard/inventory*') ? 'active' : '' }}"
            href="#inventorySubmenu" data-bs-toggle="collapse"
            aria-expanded="{{ request()->is('dashboard/inventory*') ? 'true' : 'false' }}">
            <i class="side-icon fas fa-boxes me-2"></i>Inventory
        </a>
        <ul class="collapse list-unstyled ps-3 {{ request()->is('dashboard/inventory*') ? 'show' : '' }}"
            id="inventorySubmenu" data-bs-parent="#sidebarAccordion">
            @can('suppliers.view')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard.inventory.suppliers.index') ? 'active' : '' }}"
                        href="{{ route('dashboard.inventory.suppliers.index') }}">
                        <i class="side-icon fas fa-truck me-2"></i>Suppliers
                    </a>
                </li>
            @endcan
            @can('categories.view')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard.inventory.categories.index') ? 'active' : '' }}"
                        href="{{ route('dashboard.inventory.categories.index') }}">
                        <i class="side-icon fas fa-tags me-2"></i>Categories
                    </a>
                </li>
            @endcan
            @can('inventory.view')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard.inventory.inventory.index') ? 'active' : '' }}"
                        href="{{ route('dashboard.inventory.inventory.index') }}">
                        <i class="side-icon fas fa-boxes me-2"></i>Items
                    </a>
                </li>
            @endcan
            @can('transactions.view')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard.inventory.inventory-transactions.index') ? 'active' : '' }}"
                        href="{{ route('dashboard.inventory.inventory-transactions.index') }}">
                        <i class="side-icon fas fa-exchange-alt me-2"></i>Transactions
                    </a>
                </li>
            @endcan
        </ul>
    </li>

    <!-- Finance Section -->
    <li class="nav-item">
        <a class="nav-link dropdown-toggle {{ request()->is('dashboard/payments*') || request()->is('dashboard/expenses*') ? 'active' : '' }}"
            href="#financeSubmenu" data-bs-toggle="collapse"
            aria-expanded="{{ request()->is('dashboard/payments*') || request()->is('dashboard/expenses*') ? 'true' : 'false' }}">
            <i class="side-icon fas fa-money-bill-wave me-2"></i>Finance
        </a>
        <ul class="collapse list-unstyled ps-3 {{ request()->is('dashboard/payments*') || request()->is('dashboard/expenses*') ? 'show' : '' }}"
            id="financeSubmenu" data-bs-parent="#sidebarAccordion">
            @can('payments.view')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard.payments.index') ? 'active' : '' }}"
                        href="{{ route('dashboard.payments.index') }}">
                        <i class="side-icon fas fa-credit-card me-2"></i>Payments
                    </a>
                </li>
            @endcan
            @can('expenses.view')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard.expenses.index') ? 'active' : '' }}"
                        href="{{ route('dashboard.expenses.index') }}">
                        <i class="side-icon fas fa-money-bill-wave me-2"></i>Expenses
                    </a>
                </li>
            @endcan
        </ul>
    </li>

    <!-- Roles Section -->
    @can('roles.view')
        <li class="nav-item">
            <a class="nav-link dropdown-toggle {{ request()->is('dashboard/roles*') || request()->is('dashboard/user-roles*') ? 'active' : '' }}"
                href="#roleSubmenu" data-bs-toggle="collapse"
                aria-expanded="{{ request()->is('dashboard/roles*') || request()->is('dashboard/user-roles*') ? 'true' : 'false' }}">
                <i class="side-icon fas fa-user-shield me-2"></i>Roles & Permissions
            </a>
            <ul class="collapse list-unstyled ps-3 {{ request()->is('dashboard/roles*') || request()->is('dashboard/user-roles*') ? 'show' : '' }}"
                id="roleSubmenu" data-bs-parent="#sidebarAccordion">
                @can('roles.view')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard.roles.index') ? 'active' : '' }}"
                            href="{{ route('dashboard.roles.index') }}">
                            <i class="side-icon fas fa-id-card me-2"></i>Roles
                        </a>
                    </li>
                @endcan
                @can('user_roles.view')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard.user-roles.index') ? 'active' : '' }}"
                            href="{{ route('dashboard.user-roles.index') }}">
                            <i class="side-icon fas fa-users-cog me-2"></i>User Roles
                        </a>
                    </li>
                @endcan

            </ul>
        </li>
    @endcan
</ul>
