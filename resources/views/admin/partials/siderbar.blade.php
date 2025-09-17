<style>
    .menu a {
        text-decoration: none;
        /* Remove default underline */
    }

    /* Optional: Remove text decoration on hover or active state */
    .menu a:hover,
    .menu a:focus,
    .menu a:active {
        text-decoration: none;
        /* Ensure underline doesn't appear on hover or focus */
    }
</style>




<div class="sidebar-wrapper sidebar-theme" style="width: 268px;">
    <nav id="sidebar">
        <div class="navbar-nav">
            <div class="nav-logo">
                <div class="nav-item theme-logo" style="width: 100%; text-align: center;">
                    <a href="{{ route('dashboard') }}">
                        <img src="{{ asset('images/logo.jpeg') }}" alt="logo" style="max-width: 90%; height: auto;">
                    </a>
                </div>
            </div>
            <!-- <div class="nav-item sidebar-toggle">
                <div class="btn-toggle sidebarCollapse">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevrons-left">
                        <polyline points="11 17 6 12 11 7"></polyline>
                        <polyline points="18 17 13 12 18 7"></polyline>
                    </svg>
                </div>
            </div> -->
        </div>
        <div class="profile-info">
            <div class="user-info">
                <div class="profile-img">
                    @if (Auth::user()->image)
                    <img src="{{ asset(Auth::user()->image) }}" alt="avatar">
                    @else
                    <img src="{{ asset('images/dummy.jpg') }}" alt="No Image">
                    @endif
                </div>
                <div class="profile-content">
                    @if(auth()->check())
                    <h6 class="">{{ auth()->user()->name }}</h6>
                    @endif
                    <p class="">
                        @if (auth()->user()->role == 0)
                        HR
                        @else
                        {{ auth()->user()->role }}
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <div class="shadow-bottom"></div>
        <ul class="list-unstyled menu-categories">
            <li class="menu">
                <a href="{{ route('dashboard') }}" aria-expanded="false" class="dropdown-toggle">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                        <span>Dashboard</span>
                    </div>
                </a>
            </li>



            <li class="menu">
                <a href="{{ route('employee.index') }}" aria-expanded="false" class="dropdown-toggle">
                    <div class="">
                        <!-- Replace the existing SVG with a custom SVG -->
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <!-- Conditional logic for displaying "Employee" or "Profile" -->
                        <span>
                            @if (in_array(auth()->user()->role, ['admin', 'HR', 'Accountant']))
                            Employee
                            @else
                            Profile
                            @endif
                        </span>
                    </div>
                </a>
            </li>

            <li class="menu">
                <a href="{{ route('document.index') }}" aria-expanded="false" class="dropdown-toggle">
                    <div class="">
                        <!-- Replace the existing SVG with a document SVG icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z"></path>
                            <line x1="6" y1="10" x2="14" y2="10"></line>
                            <line x1="6" y1="14" x2="14" y2="14"></line>
                            <line x1="6" y1="18" x2="14" y2="18"></line>
                        </svg>
                        <span>Employee Document</span>
                    </div>
                </a>
            </li>


            <!-- <li class="menu">
              <a href="{{ route('accouncementdocument.index') }}" aria-expanded="false" class="dropdown-toggle">
                    <div class="">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bell">
                            <path d="M18 8a6 6 0 1 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>
                        <span>Request Document</span>
                    </div>
                </a>
            </li> -->

            <li class="menu">
                <a href="{{route('leave.index')}}" aria-expanded="false" class="dropdown-toggle">
                    <div class="">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-watch">
                            <circle cx="12" cy="12" r="7"></circle>
                            <polyline points="12 9 12 12 13.5 13.5"></polyline>
                            <path d="M16.24 7.76l1.42-1.42M7.76 7.76L6.34 6.34M6.34 17.66l1.42-1.42M17.66 17.66l-1.42-1.42"></path>
                            <path d="M12 2v2M12 20v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42"></path>
                        </svg>
                        <span>Leave Status</span>
                    </div>
                </a>
            </li>

            <li class="menu">
                <a href="{{route('attendance.index')}}" aria-expanded="false" class="dropdown-toggle">
                    <div class="">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-calendar-check">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                            <polyline points="9 16 11 18 15 14"></polyline>
                        </svg>
                        <span>Attendance</span>
                    </div>
                </a>
            </li>

            <li class="menu">
                <a href="{{route('payroll.index')}}" aria-expanded="false" class="dropdown-toggle">
                    <div class="">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign">
                            <line x1="12" y1="1" x2="12" y2="23"></line>
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                        </svg>
                        <span>PayRoll</span>
                    </div>
                </a>
            </li>

            <!-- @if(auth()->user()->role == 'admin' || auth()->user()->role == 'Accountant' || auth()->user()->role == 'HR')
            <li class="menu">
                <a href="{{route('payslip.index')}}" aria-expanded="false" class="dropdown-toggle">
                    <div class="">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z"></path>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                        </svg>
                        <span>PaySlip</span>
                    </div>
                </a>
            </li>
            @endif -->
            @if(auth()->user()->role == 'admin' || auth()->user()->role == 'Accountant' || auth()->user()->role == 'HR')
            <li class="menu">
                <a href="{{route('payslipupload.index')}}" aria-expanded="false" class="dropdown-toggle">
                    <div class="">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-upload">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="17 8 12 3 7 8"></polyline>
                            <line x1="12" y1="3" x2="12" y2="15"></line>
                        </svg>
                        <span>PaySlip Upload</span>
                    </div>
                </a>
            </li>
            @endif

            <li class="menu">
                <a href="{{ route('shift.index') }}" aria-expanded="false" class="dropdown-toggle">
                    <div class="">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-clock">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="6" x2="12" y2="12"></line>
                            <line x1="12" y1="12" x2="16" y2="10"></line>
                        </svg>
                        <span>Shift</span>
                    </div>
                </a>
            </li>

            <li class="menu">
                <a href="{{route('rota.index')}}" aria-expanded="false" class="dropdown-toggle">
                    <div class="">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-calendar">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        <span>Rota</span>
                    </div>
                </a>
            </li>


            @if(auth()->user()->role == 'admin' || auth()->user()->role == 'Accountant' || auth()->user()->role == 'HR')
            <li class="menu">
                <a href="{{ route('expenses.index') }}" aria-expanded="false" class="dropdown-toggle">
                    <div class="">
                        <!-- Wallet SVG Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-wallet">
                            <path d="M20 15V8a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v7"></path>
                            <rect x="1" y="8" width="22" height="14" rx="2" ry="2"></rect>
                            <line x1="6" y1="15" x2="6" y2="15"></line>
                        </svg>
                        <span>Expenses</span>
                    </div>
                </a>
            </li>

            @endif

            <li class="menu">
                <a href="{{ route('announcements.index') }}" aria-expanded="false" class="dropdown-toggle">
                    <div class="">
                        <!-- Announcement SVG Icon (Bell) -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bell">
                            <path d="M18 8A6 6 0 0 0 6 8v5H3v2h1v2h14v-2h1v-2h-3V8z"></path>
                            <path d="M13 21h-2a2 2 0 0 1-2-2h6a2 2 0 0 1-2 2z"></path>
                        </svg>
                        <span>Announcement</span>
                    </div>
                </a>
            </li>



            <li class="menu">
                <a href="{{route('profile.update')}}" aria-expanded="false" class="dropdown-toggle">
                    <div class="">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-sliders">
                            <line x1="4" y1="21" x2="4" y2="14"></line>
                            <line x1="4" y1="10" x2="4" y2="3"></line>
                            <line x1="12" y1="21" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12" y2="3"></line>
                            <line x1="20" y1="21" x2="20" y2="16"></line>
                            <line x1="20" y1="12" x2="20" y2="3"></line>
                            <line x1="1" y1="14" x2="7" y2="14"></line>
                            <line x1="9" y1="8" x2="15" y2="8"></line>
                            <line x1="17" y1="16" x2="23" y2="16"></line>
                        </svg>
                        <span>Setting</span>
                    </div>
                </a>
            </li>

            @if(auth()->user()->role == 'admin' || auth()->user()->role == 'Accountant' || auth()->user()->role == 'HR')

            <li class="menu">
                <a href="{{ route('roles.index') }}" aria-expanded="false" class="dropdown-toggle">
                    <div class="">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users">
                            <path d="M17 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M9 21v-2a4 4 0 0 1 3-3.87"></path>
                            <path d="M9 5a4 4 0 1 0 0 8"></path>
                            <path d="M17 5a4 4 0 1 0 0 8"></path>
                        </svg>
                        <span>Roles Permissions</span>
                    </div>
                </a>
            </li>

            @endif



            <li class="menu">
                <a href="{{route('contacts')}}" aria-expanded="false" class="dropdown-toggle">
                    <div class="">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-map-pin">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                        <span>Contacts</span>
                    </div>
                </a>
            </li>


            <li class="menu">
                <a href="{{ route('logout') }}" aria-expanded="false" class="dropdown-toggle">
                    <div class="">
                        <!-- Logout SVG Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-log-out">
                            <path d="M10 17l5-5-5-5M15 12H3" />
                        </svg>
                        <span>Logout</span>
                    </div>
                </a>
            </li>






            <!-- <li class="menu">
                <a href="#invoice" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                    <div class="">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign">
                            <line x1="12" y1="1" x2="12" y2="23"></line>
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                        </svg>
                        <span>Logout</span>
                    </div>
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </div>
                </a>
                <ul class="collapse submenu list-unstyled" id="invoice" data-bs-parent="#accordionExample">
                    <li>
                        <a href="./app-invoice-list.html"> List </a>
                    </li>
                    <li>
                        <a href="./app-invoice-preview.html"> Preview </a>
                    </li>
                    <li>
                        <a href="./app-invoice-add.html"> Add </a>
                    </li>
                    <li>
                        <a href="./app-invoice-edit.html"> Edit </a>
                    </li>
                </ul>
            </li> -->



        </ul>

    </nav>

</div>