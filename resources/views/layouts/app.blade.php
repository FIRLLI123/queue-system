<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Antrian Order') - Queue Dashboard</title>

    <!-- SB Admin 2 Stylesheets -->
    <link href="{{ asset('sbadmin2/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="{{ asset('sbadmin2/css/sb-admin-2.min.css') }}" rel="stylesheet">
    
    <!-- Custom CSS for Premium Experience -->
    <link rel="stylesheet" href="{{ asset('css/custom-dashboard.css') }}">

    @stack('styles')
</head>
<body id="page-top">
    <div id="wrapper">
        
        <!-- SIDEBAR SB ADMIN 2 -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ url('/dashboard') }}">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-headset"></i>
                </div>
                <div class="sidebar-brand-text mx-3">CEC Queue</div>
            </a>

            <hr class="sidebar-divider my-0">

            <!-- Menu 1: Antrian Order (Untuk CC & ADMIN) -->
            <li class="nav-item {{ Request::is('dashboard') ? 'active' : '' }}">
                <a class="nav-link" href="{{ url('/dashboard') }}">
                    <i class="fas fa-fw fa-list-ol"></i>
                    <span>Antrian Order</span>
                </a>
            </li>

            <!-- Menu Admin (Hanya untuk ADMIN) -->
            @if(auth()->user()->isAdmin())
                <hr class="sidebar-divider">
                <div class="sidebar-heading">
                    Manajemen Admin
                </div>

                <!-- Menu 2: Kelola User CC -->
                <li class="nav-item {{ Request::is('admin/users*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ url('/admin/users') }}">
                        <i class="fas fa-fw fa-users"></i>
                        <span>Kelola User</span>
                    </a>
                </li>

                <!-- Menu 3: Kelola Tipe Order -->
                <li class="nav-item {{ Request::is('admin/order-types*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ url('/admin/order-types') }}">
                        <i class="fas fa-fw fa-tags"></i>
                        <span>Kelola Tipe Order</span>
                    </a>
                </li>

                <!-- Menu 3.5: Kelola Titipan Order -->
                <li class="nav-item {{ Request::is('admin/titipan-orders*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ url('/admin/titipan-orders') }}">
                        <i class="fas fa-fw fa-clipboard-list"></i>
                        <span>Kelola Titipan Order</span>
                    </a>
                </li>

                <!-- Menu 3.6: Kelola Kebutuhan Titipan -->
                <li class="nav-item {{ Request::is('admin/titipan-requirements*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ url('/admin/titipan-requirements') }}">
                        <i class="fas fa-fw fa-tasks"></i>
                        <span>Kebutuhan Titipan</span>
                    </a>
                </li>

                <!-- Menu 4: Screen Monitoring -->
                <li class="nav-item {{ Request::is('admin/screen*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ url('/admin/screen') }}">
                        <i class="fas fa-fw fa-desktop"></i>
                        <span>Screen Monitoring</span>
                    </a>
                </li>

                <!-- Menu 5: Laporan Order -->
                <li class="nav-item {{ Request::is('admin/report*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ url('/admin/report') }}">
                        <i class="fas fa-fw fa-chart-bar"></i>
                        <span>Laporan Order</span>
                    </a>
                </li>
            @endif

            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>
        <!-- END OF SIDEBAR -->

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                
                <!-- TOPBAR NAVBAR -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow" style="height: 60px;">
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <ul class="navbar-nav ml-auto align-items-center">
                        
                        <!-- Connection Status Dot (Section 9.1 & 9.2 PRD) -->
                        <li class="nav-item mx-2 d-flex align-items-center">
                            <span id="connection-status-dot" class="online-indicator-dot pulse-green mr-2" title="Koneksi aktif"></span>
                            <span class="fs-8 text-secondary d-none d-sm-inline" id="connection-status-text">Online</span>
                        </li>

                        <!-- Sound Toggle Button -->
                        <li class="nav-item mx-2 d-flex align-items-center">
                            <button id="global-sound-toggle-btn" class="btn btn-sm btn-light border-0 shadow-sm rounded-circle d-flex align-items-center justify-content-center" title="Matikan Suara" style="width: 32px; height: 32px; padding: 0; background: #f8fafc; transition: all 0.2s ease;">
                                <i class="fas fa-volume-up text-success" id="global-sound-icon" style="font-size: 14px;"></i>
                            </button>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- User Profile Dropdown -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <div class="d-flex flex-column align-items-end text-right mr-2">
                                    <span class="text-gray-600 small font-weight-bold">{{ auth()->user()->name }}</span>
                                    <span class="text-gray-500 font-weight-normal" style="font-size: 10px; text-transform: uppercase;">{{ auth()->user()->role === 'CC' ? 'CEC' : auth()->user()->role }}</span>
                                </div>
                                <span class="img-profile rounded-circle bg-primary d-inline-flex align-items-center justify-content-center text-white" style="width: 32px; height: 32px; font-weight: 700;">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </span>
                            </a>
                            
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="{{ url('/change-password') }}">
                                    <i class="fas fa-key fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Ganti Password
                                </a>
                                <div class="dropdown-divider"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </li>
                    </ul>
                </nav>
                <!-- END OF TOPBAR -->

                <!-- CONTENT AREA -->
                <div class="container-fluid">
                    @yield('content')
                </div>
                
            </div>

            <footer class="sticky-footer bg-white py-3 d-none">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Queue Dashboard &copy; 2026 | Developed by <a href="https://firlli.vercel.app" target="_blank" rel="noopener noreferrer" class="font-weight-bold text-primary">Firlli</a></span>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- SB Admin 2 Vendor Scripts -->
    <script src="{{ asset('sbadmin2/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('sbadmin2/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('sbadmin2/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('sbadmin2/js/sb-admin-2.min.js') }}"></script>
    
    <!-- Custom Modular JS for Queue Animations & Realtime Polling -->
    <script src="{{ asset('js/toast.js') }}"></script>
    <script src="{{ asset('js/polling.js') }}"></script>
    <script src="{{ asset('js/queue-animation.js') }}?v={{ filemtime(public_path('js/queue-animation.js')) }}"></script>
    
    <script>
        $(document).ready(function() {
            const soundBtn = document.getElementById('global-sound-toggle-btn');
            const soundIcon = document.getElementById('global-sound-icon');
            
            if (soundBtn && soundIcon) {
                // Initialize sound state
                let enabled = localStorage.getItem('dashboard-sound-enabled') !== 'false';
                updateSoundUI(enabled);
                
                soundBtn.addEventListener('click', function() {
                    enabled = localStorage.getItem('dashboard-sound-enabled') !== 'false';
                    const newEnabled = !enabled;
                    localStorage.setItem('dashboard-sound-enabled', newEnabled ? 'true' : 'false');
                    updateSoundUI(newEnabled);
                    
                    // Show a toast notifying the user
                    if (window.showToast) {
                        window.showToast(newEnabled ? 'Suara notifikasi diaktifkan' : 'Suara notifikasi dinonaktifkan', newEnabled ? 'success' : 'warning');
                    }
                });

                // Add slight hover scale effect via JS
                soundBtn.addEventListener('mouseenter', () => {
                    soundBtn.style.transform = 'scale(1.1)';
                });
                soundBtn.addEventListener('mouseleave', () => {
                    soundBtn.style.transform = 'scale(1)';
                });
            }
            
            function updateSoundUI(enabled) {
                if (enabled) {
                    soundIcon.className = 'fas fa-volume-up text-success';
                    soundBtn.title = 'Matikan Suara';
                    soundBtn.setAttribute('aria-label', 'Matikan Suara');
                } else {
                    soundIcon.className = 'fas fa-volume-mute text-danger';
                    soundBtn.title = 'Aktifkan Suara';
                    soundBtn.setAttribute('aria-label', 'Aktifkan Suara');
                }
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>
