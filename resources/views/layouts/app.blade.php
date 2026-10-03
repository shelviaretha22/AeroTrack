<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>AeroTrack</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #F3F6F5;
        }

        .sidebar {
            width: 240px;
            height: 100vh;
            background: #365F65;
            position: fixed;
            left: 0;
            top: 0;
            color: white;
        }

        .sidebar-logo {
            padding: 25px 20px;
            font-size: 25px;
            font-weight: bold;
            border-bottom: 1px solid rgba(255,255,255,0.15);
        }

        .sidebar-menu {
            padding: 20px 12px;
        }

        .sidebar-menu a {
            display: block;
            padding: 12px 15px;
            margin-bottom: 5px;
            color: white;
            text-decoration: none;
            border-radius: 7px;
        }

        .sidebar-menu a:hover {
            background: #4F8189;
        }

        .main-content {
            margin-left: 240px;
            min-height: 100vh;
        }

        .topbar {
            height: 70px;
            background: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            border-bottom: 1px solid #D9E5E3;
        }

        .page-content {
            padding: 30px;
        }
    </style>
</head>

<body>

    <!-- SIDEBAR -->
    <aside class="sidebar">

        <div class="sidebar-logo">
            AeroTrack
        </div>

        <div class="sidebar-menu">

            <a href="/dashboard">
                Dashboard
            </a>

            <a href="#">
                Tenant
            </a>

            <a href="#">
                Lokasi
            </a>

            <a href="#">
                Kerja Sama
            </a>

            <a href="#">
                Kontrak
            </a>

            <a href="#">
                Aktivasi
            </a>

            <a href="#">
                Pendapatan
            </a>

            <a href="#">
                Monitoring
            </a>

            <a href="#">
                Laporan
            </a>

        </div>

    </aside>


    <!-- MAIN CONTENT -->
    <main class="main-content">

        <header class="topbar">

            <div>
                <strong>@yield('page-title', 'Dashboard')</strong>
            </div>

            <div>
                Commercial
            </div>

        </header>


        <div class="page-content">

            @yield('content')

        </div>

    </main>

</body>

</html>