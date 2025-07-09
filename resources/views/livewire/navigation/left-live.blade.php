<div>
    <aside class="main-sidebar sidebar-dark-primary bg-purple elevation-4">
        <!-- Brand Logo -->
        <a href="{{ route('dashboard') }}" class="brand-link">
            <span class="brand-text font-weight-light">Procurement System</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar user panel -->
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <img src="{{ Auth::user()->profile_photo_path ? asset('storage/'.Auth::user()->profile_photo_path) : asset('face-0.jpg') }}"
                        width="60" height="60" class="rounded-circle" alt="User Image">
                </div>
                <div class="info">
                    <a href="{{ route('profile') }}" class="d-block text-capitalize">{{ Auth::user()->name }}</a>
                    <span class="badge bg-success ">{{ Auth::user()->email }}</span> <br/>
                    <span class="badge bg-secondary ">{{ Auth::user()->role }}</span>
                </div>
            </div>

            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                           
                        </a>
                    </li>
                    
                    @if(auth()->user()->role == 'Candidate')
                        <li class="nav-item">
                            <a href="{{ route('application-submission') }}" class="nav-link">
                                <i class="nav-icon fas fa-file-upload"></i>
                                <p>Submit Application</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('notifications') }}" class="nav-link">
                                <i class="nav-icon fas fa-bell"></i>
                                <p>Notifications</p>
                            </a>
                        </li>
                    @elseif(in_array(auth()->user()->role, ['District_court_clerk', 'Training_officer']))
                        <li class="nav-item">
                            <a href="{{ route('application-queue') }}" class="nav-link">
                                <i class="nav-icon fas fa-list"></i>
                                <p>Application Queue</p>
                            </a>
                        </li>
                        {{-- <li class="nav-item">
                            <a href="{{ route('application-review') }}" class="nav-link">
                                <i class="nav-icon fas fa-search"></i>
                                <p>Application Review</p>
                            </a>
                        </li> --}}
                        <li class="nav-item">
                            <a href="{{ route('notifications') }}" class="nav-link">
                                <i class="nav-icon fas fa-bell"></i>
                                <p>Notifications</p>
                            </a>
                        </li>
                    
                    @elseif(auth()->user()->role == 'Jtc_member')
                        <li class="nav-item">
                            <a href="{{ route('application-queue') }}" class="nav-link">
                                <i class="nav-icon fas fa-list"></i>
                                <p>Application Queue</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('committee-voting') }}" class="nav-link">
                                <i class="nav-icon fas fa-vote-yea"></i>
                                <p>Committee Voting</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('notifications') }}" class="nav-link">
                                <i class="nav-icon fas fa-bell"></i>
                                <p>Notifications</p>
                            </a>
                        </li>
                    @elseif(auth()->user()->role == 'Chairperson')
                        <li class="nav-item">
                            <a href="{{ route('application-queue') }}" class="nav-link">
                                <i class="nav-icon fas fa-list"></i>
                                <p>Application Queue</p>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a href="{{ route('kpi-dashboard') }}" class="nav-link">
                                <i class="nav-icon fas fa-chart-line"></i>
                                <p>KPI Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('audit-logs') }}" class="nav-link">
                                <i class="nav-icon fas fa-history"></i>
                                <p>Audit Logs</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('notifications') }}" class="nav-link">
                                <i class="nav-icon fas fa-bell"></i>
                                <p>Notifications</p>
                            </a>
                        </li>
                    @elseif(auth()->user()->role == 'System_administrator')
                        <li class="nav-item">
                            <a href="{{ route('admin.users') }}" class="nav-link">
                                <i class="nav-icon fas fa-users"></i>
                                <p>User Management</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('kpi-dashboard') }}" class="nav-link">
                                <i class="nav-icon fas fa-chart-line"></i>
                                <p>KPI Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('audit-logs') }}" class="nav-link">
                                <i class="nav-icon fas fa-history"></i>
                                <p>Audit Logs</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('notifications') }}" class="nav-link">
                                <i class="nav-icon fas fa-bell"></i>
                                <p>Notifications</p>
                            </a>
                        </li>
                    @endif

                    <li class="nav-item">
                        <a href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout').submit();"
                           class="nav-link">
                            <i class="nav-icon fas fa-door-open text-danger"></i>
                            <p class="text-danger">Logout</p>
                        </a>
                        <form id="logout" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>
</div>
