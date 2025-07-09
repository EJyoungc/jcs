<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">

    <link rel="stylesheet" href="{{ asset('dist/css/style.css') }}">

    <!-- Scripts -->
    <link rel="stylesheet" href="{{ asset('dist/webfont/tabler-icons.min.css') }}">
    {{-- @vite(['dist/webfont/tabler-icons.min.css', 'dist/css/style.css', 'plugins/fontawesome-free/css/all.min.css']) --}}
    @livewireStyles
    <title>{{ $title ?? 'Page Title' }}</title>
</head>

<body class="hold-transition sidebar-mini">
    <!-- Site wrapper -->
    <div class="wrapper">
        <!-- Navbar -->

        @livewire('navigation.top-live')
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        @livewire('navigation.left-live')

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            {{ $slot }}
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <footer class="main-footer">
            <div class="float-right d-none d-sm-block">
                <b>Version</b> 1.0
            </div>
            <strong>Copyright &copy; 2025 <a href="#">Small holder farmer</a>.</strong> All rights
            reserved.
        </footer>

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->
    @livewireScripts
    <script src="{{ asset('plugins/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <x-livewire-alert::scripts />
    <!-- jQuery -->
    <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('dist/js/adminlte.min.js') }}"></script>
    <!-- AdminLTE for demo purposes -->
    {{-- <script src="{{ asset('dist/js/demo.js')}}"></script> --}}
    <script src="{{ asset('plugins/chart.js/Chart.min.js') }}"></script>
    @stack('scripts')

    <script>
        // document.addEventListener('livewire:init', () => {
        Livewire.on('modal-open', (data) => {
            // Handle the event here
            var modalbackdrop = document.createElement('div');
            modalbackdrop.classList.add("modal-backdrop", "fade", "show");
            document.body.appendChild(modalbackdrop);

        });
        Livewire.on('modal-cancel', (data) => {
            // Handle the event here
            var modalbackdrop = document.querySelector('.modal-backdrop');
            if (modalbackdrop) {
                modalbackdrop.parentNode.removeChild(modalbackdrop);
            }

        });
        // });
    </script>
</body>

</html>
