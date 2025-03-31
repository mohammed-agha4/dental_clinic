<ul class="nav flex-column" id="sidebarAccordion">
    <!-- Main Navigation -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
            <i class="side-icon fas fa-home me-2"></i>Dashboard
        </a>
    </li>

    <!-- Patient Care Section -->
    <li class="nav-item">
        <a class="nav-link dropdown-toggle {{ request()->is('dashboard/patients*') || request()->is('dashboard/appointments*') || request()->is('dashboard/visits*') ? 'active' : '' }}"
           href="#patientCareSubmenu"
           data-bs-toggle="collapse"
           aria-expanded="{{ request()->is('dashboard/patients*') || request()->is('dashboard/appointments*') || request()->is('dashboard/visits*') ? 'true' : 'false' }}">
            <i class="side-icon fas fa-user-md me-2"></i>Patient Care
        </a>
        <ul class="collapse list-unstyled ps-3 {{ request()->is('dashboard/patients*') || request()->is('dashboard/appointments*') || request()->is('dashboard/visits*') ? 'show' : '' }}"
            id="patientCareSubmenu"
            data-bs-parent="#sidebarAccordion">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard.patients.index') ? 'active' : '' }}"
                    href="{{ route('dashboard.patients.index') }}">
                    <i class="side-icon fas fa-users me-2"></i>Patients
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard.appointments.index') ? 'active' : '' }}"
                    href="{{ route('dashboard.appointments.index') }}">
                    <i class="side-icon fas fa-calendar-alt me-2"></i>Appointments
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard.visits.index') ? 'active' : '' }}"
                    href="{{ route('dashboard.visits.index') }}">
                    <i class="side-icon fas fa-stethoscope me-2"></i>Visits
                </a>
            </li>
        </ul>
    </li>

    <!-- Clinic Management Section -->
    <li class="nav-item">
        <a class="nav-link dropdown-toggle {{ request()->is('dashboard/services*') || request()->is('dashboard/staff*') || request()->is('dashboard/service-staff*') ? 'active' : '' }}"
           href="#clinicSubmenu"
           data-bs-toggle="collapse"
           aria-expanded="{{ request()->is('dashboard/services*') || request()->is('dashboard/staff*') || request()->is('dashboard/service-staff*') ? 'true' : 'false' }}">
            <i class="side-icon fa-solid fa-tooth me-2"></i>Clinic Management
        </a>
        <ul class="collapse list-unstyled ps-3 {{ request()->is('dashboard/services*') || request()->is('dashboard/staff*') || request()->is('dashboard/service-staff*') ? 'show' : '' }}"
            id="clinicSubmenu"
            data-bs-parent="#sidebarAccordion">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard.services.index') ? 'active' : '' }}"
                    href="{{ route('dashboard.services.index') }}">
                    <i class="side-icon fa-solid fa-tooth me-2"></i>Services
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard.staff.index') ? 'active' : '' }}"
                    href="{{ route('dashboard.staff.index') }}">
                    <i class="side-icon fas fa-user-md me-2"></i>Staff
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard.service-staff.index') ? 'active' : '' }}"
                    href="{{ route('dashboard.service-staff.index') }}">
                    <i class="side-icon fas fa-link me-2"></i>Staff Services
                </a>
            </li>
        </ul>
    </li>

    <!-- Inventory Section -->
    <li class="nav-item">
        <a class="nav-link dropdown-toggle {{ request()->is('dashboard/inventory*') ? 'active' : '' }}"
           href="#inventorySubmenu"
           data-bs-toggle="collapse"
           aria-expanded="{{ request()->is('dashboard/inventory*') ? 'true' : 'false' }}">
            <i class="side-icon fas fa-boxes me-2"></i>Inventory
        </a>
        <ul class="collapse list-unstyled ps-3 {{ request()->is('dashboard/inventory*') ? 'show' : '' }}"
            id="inventorySubmenu"
            data-bs-parent="#sidebarAccordion">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard.inventory.suppliers.index') ? 'active' : '' }}"
                    href="{{ route('dashboard.inventory.suppliers.index') }}">
                    <i class="side-icon fas fa-truck me-2"></i>Suppliers
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard.inventory.categories.index') ? 'active' : '' }}"
                    href="{{ route('dashboard.inventory.categories.index') }}">
                    <i class="side-icon fas fa-tags me-2"></i>Categories
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard.inventory.inventory.index') ? 'active' : '' }}"
                    href="{{ route('dashboard.inventory.inventory.index') }}">
                    <i class="side-icon fas fa-boxes me-2"></i>Items
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard.inventory.inventory-transactions.index') ? 'active' : '' }}"
                    href="{{ route('dashboard.inventory.inventory-transactions.index') }}">
                    <i class="side-icon fas fa-exchange-alt me-2"></i>Transactions
                </a>
            </li>
        </ul>
    </li>

    <!-- Finance Section -->
    <li class="nav-item">
        <a class="nav-link dropdown-toggle {{ request()->is('dashboard/payments*') || request()->is('dashboard/expenses*') ? 'active' : '' }}"
           href="#financeSubmenu"
           data-bs-toggle="collapse"
           aria-expanded="{{ request()->is('dashboard/payments*') || request()->is('dashboard/expenses*') ? 'true' : 'false' }}">
            <i class="side-icon fas fa-money-bill-wave me-2"></i>Finance
        </a>
        <ul class="collapse list-unstyled ps-3 {{ request()->is('dashboard/payments*') || request()->is('dashboard/expenses*') ? 'show' : '' }}"
            id="financeSubmenu"
            data-bs-parent="#sidebarAccordion">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard.payments.index') ? 'active' : '' }}"
                    href="{{ route('dashboard.payments.index') }}">
                    <i class="side-icon fas fa-credit-card me-2"></i>Payments
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard.expenses.index') ? 'active' : '' }}"
                    href="{{ route('dashboard.expenses.index') }}">
                    <i class="side-icon fas fa-money-bill-wave me-2"></i>Expenses
                </a>
            </li>
        </ul>
    </li>
</ul>
