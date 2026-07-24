<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Student Management System')</title>

    <!-- Google Fonts & Bootstrap 5 -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- DataTables Bootstrap 5 CSS -->
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <style>
        :root {
            --sidebar-width: 260px;
            --sidebar-bg: #1e293b;
            --sidebar-dark: #0f172a;
            --sidebar-hover: #334155;
            --sidebar-active-border: #3b82f6;
            --topbar-height: 60px;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f6f9;
            color: #333;
            margin: 0;
            padding: 0;
        }

        /* ========== WRAPPER ========== */
        .wrapper {
            display: flex;
            width: 100%;
            align-items: stretch;
            min-height: 100vh;
        }

        /* ========== SIDEBAR ========== */
        #sidebar {
            min-width: var(--sidebar-width);
            max-width: var(--sidebar-width);
            background: var(--sidebar-bg);
            color: #fff;
            transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
            display: flex;
            flex-direction: column;
            z-index: 1050;
            position: relative;
        }

        #sidebar .sidebar-header {
            padding: 18px 20px;
            background: var(--sidebar-dark);
            font-weight: 700;
            font-size: 1.15rem;
            letter-spacing: 0.5px;
            flex-shrink: 0;
        }

        #sidebar ul.components {
            padding: 12px 0;
            flex: 1;
            overflow-y: auto;
        }

        #sidebar ul li a {
            padding: 11px 20px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            color: #94a3b8;
            text-decoration: none;
            transition: all 0.2s;
            border-left: 4px solid transparent;
        }

        #sidebar ul li a i {
            margin-right: 11px;
            font-size: 1.05rem;
            flex-shrink: 0;
        }

        #sidebar ul li a:hover,
        #sidebar ul li.active > a {
            color: #fff;
            background: var(--sidebar-hover);
            border-left-color: var(--sidebar-active-border);
        }

        /* ========== CONTENT ========== */
        #content {
            width: 100%;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        /* ========== TOP NAVBAR ========== */
        .top-navbar {
            background: #fff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.06);
            padding: 0 20px;
            height: var(--topbar-height);
            display: flex;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1040;
            flex-shrink: 0;
        }

        .sidebar-toggle-btn {
            background: none;
            border: none;
            color: #475569;
            font-size: 1.35rem;
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 6px;
            transition: background 0.2s;
            display: none;  /* hidden on desktop */
        }
        .sidebar-toggle-btn:hover { background: #f1f5f9; }

        /* ========== SIDEBAR OVERLAY (mobile) ========== */
        #sidebarOverlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1045;
        }
        #sidebarOverlay.active { display: block; }

        /* ========== CARDS ========== */
        .card-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03);
            background: #fff;
        }

        .badge-role {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 4px 9px;
            border-radius: 20px;
        }

        /* ========== MAIN CONTENT ========== */
        main.main-content {
            padding: 1.5rem;
            flex: 1;
        }

        /* ========== FOOTER ========== */
        footer.site-footer {
            background: #fff;
            text-align: center;
            padding: 12px;
            border-top: 1px solid #e2e8f0;
            font-size: 0.8rem;
            color: #94a3b8;
            flex-shrink: 0;
        }

        /* ========== TABLE RESPONSIVE ========== */
        .table-responsive { border-radius: 8px; overflow: hidden; }

        /* ========== RESPONSIVE: MOBILE / TABLET ========== */
        @media (max-width: 991.98px) {
            /* Sidebar slides off-screen on mobile */
            #sidebar {
                position: fixed;
                top: 0;
                left: 0;
                height: 100vh;
                transform: translateX(-100%);
                z-index: 1050;
            }

            #sidebar.open {
                transform: translateX(0);
                box-shadow: 4px 0 20px rgba(0,0,0,0.3);
            }

            /* Content takes full width on mobile */
            #content {
                width: 100%;
                min-width: 0;
            }

            /* Show hamburger toggle button */
            .sidebar-toggle-btn {
                display: inline-flex;
                align-items: center;
            }

            main.main-content {
                padding: 1rem;
            }
        }

        @media (max-width: 575.98px) {
            .top-navbar {
                padding: 0 12px;
            }
            main.main-content {
                padding: 0.75rem;
            }
            .card-custom {
                border-radius: 8px;
            }
            /* Make action buttons stack on very small screens */
            .dt-action-btns .btn {
                padding: 3px 6px;
                font-size: 0.75rem;
            }
        }

        /* ========== DATATABLE RESPONSIVE FIXES ========== */
        table.dataTable {
            width: 100% !important;
        }
        div.dataTables_wrapper div.dataTables_length,
        div.dataTables_wrapper div.dataTables_filter {
            margin-bottom: 0.5rem;
        }
        @media (max-width: 575.98px) {
            div.dataTables_wrapper div.dataTables_length,
            div.dataTables_wrapper div.dataTables_filter,
            div.dataTables_wrapper div.dataTables_info,
            div.dataTables_wrapper div.dataTables_paginate {
                text-align: center;
                float: none;
                width: 100%;
            }
        }

        /* ========== ACCESS URL BANNER ========== */
        .access-url-input { font-size: 0.8rem; }

        /* ========== PAGE TITLE TRUNCATION ========== */
        .page-title-text {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 200px;
        }
        @media (min-width: 576px) {
            .page-title-text { max-width: 340px; }
        }
        @media (min-width: 992px) {
            .page-title-text { max-width: none; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Sidebar Overlay (mobile) -->
    <div id="sidebarOverlay"></div>

    <div class="wrapper">
        <!-- ===== SIDEBAR ===== -->
        <nav id="sidebar">
            <div class="sidebar-header d-flex align-items-center gap-2">
                <i class="bi bi-mortarboard-fill text-primary fs-4"></i>
                <span>EduManager</span>
            </div>

            <ul class="list-unstyled components">
                @auth
                    @if(auth()->user()->isAdmin())
                        <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <a href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
                        </li>
                        <li class="{{ request()->routeIs('admin.teachers.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.teachers.index') }}"><i class="bi bi-person-badge"></i> Teachers</a>
                        </li>
                        <li class="{{ request()->routeIs('admin.classes.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.classes.index') }}"><i class="bi bi-building"></i> Classes &amp; Sections</a>
                        </li>
                        <li class="{{ request()->routeIs('admin.subjects.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.subjects.index') }}"><i class="bi bi-book"></i> Subjects</a>
                        </li>
                    @endif

                    @if(auth()->user()->isAdmin() || auth()->user()->isTeacher())
                        @if(auth()->user()->isTeacher())
                            <li class="{{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}">
                                <a href="{{ route('teacher.dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
                            </li>
                        @endif
                        <li class="{{ request()->routeIs('students.*') ? 'active' : '' }}">
                            <a href="{{ route('students.index') }}"><i class="bi bi-people"></i> Students</a>
                        </li>
                        <li class="{{ request()->routeIs('attendance.*') ? 'active' : '' }}">
                            <a href="{{ route('attendance.index') }}"><i class="bi bi-calendar-check"></i> Attendance</a>
                        </li>
                        <li class="{{ request()->routeIs('marks.*') ? 'active' : '' }}">
                            <a href="{{ route('marks.index') }}"><i class="bi bi-journal-bookmark"></i> Marks Entry</a>
                        </li>
                        <li class="{{ request()->routeIs('fees.*') ? 'active' : '' }}">
                            <a href="{{ route('fees.index') }}"><i class="bi bi-cash-stack"></i> Fee Collection</a>
                        </li>
                        <li class="{{ request()->routeIs('timetables.*') ? 'active' : '' }}">
                            <a href="{{ route('timetables.index') }}"><i class="bi bi-calendar3"></i> Class Timetable</a>
                        </li>
                    @endif

                    @if(auth()->user()->isStudent())
                        <li class="{{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                            <a href="{{ route('student.dashboard') }}"><i class="bi bi-speedometer2"></i> Student Portal</a>
                        </li>
                    @endif
                @endauth
            </ul>
        </nav>

        <!-- ===== PAGE CONTENT ===== -->
        <div id="content">
            <!-- Top Navbar -->
            <nav class="top-navbar">
                <!-- Hamburger toggle (visible only on mobile) -->
                <button class="sidebar-toggle-btn me-2" id="sidebarToggle" title="Toggle sidebar" aria-label="Toggle sidebar">
                    <i class="bi bi-list"></i>
                </button>

                <span class="fw-semibold text-secondary page-title-text">@yield('page-title', 'Dashboard')</span>

                <div class="ms-auto d-flex align-items-center gap-2 gap-sm-3">
                    @auth
                        <div class="text-end d-none d-sm-block">
                            <span class="d-block fw-semibold text-dark" style="font-size:0.9rem;">{{ auth()->user()->name }}</span>
                            <span class="badge bg-primary badge-role">{{ auth()->user()->role }}</span>
                        </div>
                        <div class="d-sm-none">
                            <span class="badge bg-primary badge-role">{{ auth()->user()->role }}</span>
                        </div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-circle p-2" title="Logout">
                                <i class="bi bi-box-arrow-right"></i>
                            </button>
                        </form>
                    @endauth
                </div>
            </nav>

            <!-- Main Body Container -->
            <main class="main-content">
                {{-- Portal Access Link Banner --}}
                @if(session('access_url'))
                    <div class="alert alert-success alert-dismissible fade show card-custom border-start border-4 border-success p-3 mb-4 shadow-sm" role="alert">
                        <div class="d-flex align-items-start gap-3 flex-wrap">
                            <div class="bg-success text-white rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width:42px;height:42px;">
                                <i class="bi bi-envelope-check-fill fs-5"></i>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <h6 class="fw-bold mb-1 text-dark">
                                    <i class="bi bi-key-fill text-success me-1"></i> Student Portal Access Link Generated!
                                </h6>
                                <p class="mb-2 text-secondary small">
                                    Target Student Inbox: <strong>{{ session('user_email') }}</strong> ({{ session('user_name') }}).
                                    @if(session('email_sent'))
                                        <span class="badge bg-success ms-1"><i class="bi bi-check-circle-fill"></i> Real Email Delivered to Student Inbox</span>
                                    @elseif(session('email_error'))
                                        <span class="badge bg-warning text-dark ms-1" title="{{ session('email_error') }}"><i class="bi bi-exclamation-triangle-fill"></i> SMTP Credentials Required in .env to send network emails</span>
                                    @endif
                                </p>
                                <div class="d-flex flex-wrap gap-2 align-items-center">
                                    <input type="text" class="form-control form-control-sm bg-white access-url-input flex-grow-1" value="{{ session('access_url') }}" id="portalAccessUrlInput" readonly style="min-width:0;">
                                    <button class="btn btn-outline-success btn-sm flex-shrink-0" type="button" onclick="copyPortalUrl()">
                                        <i class="bi bi-clipboard me-1"></i><span class="d-none d-sm-inline">Copy</span>
                                    </button>
                                    <a href="{{ session('access_url') }}" target="_blank" class="btn btn-success btn-sm flex-shrink-0">
                                        <i class="bi bi-box-arrow-up-right me-1"></i><span class="d-none d-sm-inline">Open Portal</span>
                                    </a>
                                </div>
                            </div>
                            <button type="button" class="btn-close flex-shrink-0" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                    <script>
                        function copyPortalUrl() {
                            const input = document.getElementById('portalAccessUrlInput');
                            input.select();
                            navigator.clipboard.writeText(input.value).then(function() {
                                alert('Portal Access Link copied to clipboard!');
                            }).catch(function() {
                                // Fallback for older browsers
                                document.execCommand('copy');
                                alert('Portal Access Link copied!');
                            });
                        }
                    </script>
                @endif

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show card-custom mb-4" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show card-custom mb-4" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="site-footer">
                &copy; {{ date('Y') }} Student Management System. Built with Laravel &amp; Bootstrap 5.
            </footer>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <script>
        // CSRF for AJAX
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        // ===== Responsive Sidebar Toggle =====
        const sidebar    = document.getElementById('sidebar');
        const overlay    = document.getElementById('sidebarOverlay');
        const toggleBtn  = document.getElementById('sidebarToggle');

        function openSidebar() {
            sidebar.classList.add('open');
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        toggleBtn.addEventListener('click', function() {
            sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
        });

        overlay.addEventListener('click', closeSidebar);

        // Close sidebar when a nav link is clicked on mobile
        sidebar.querySelectorAll('a').forEach(function(link) {
            link.addEventListener('click', function() {
                if (window.innerWidth < 992) closeSidebar();
            });
        });

        // Close sidebar on resize to desktop
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 992) closeSidebar();
        });
    </script>
    @stack('scripts')
</body>
</html>
