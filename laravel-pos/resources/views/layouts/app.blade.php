<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('argon/img/apple-icon.png') }}">
  <link rel="icon" type="image/png" href="{{ asset('argon/img/favicon.png') }}">
  <title>
    {{ config('app.name', 'Laravel') }} - POS
  </title>
  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
  @stack('styles')
  <!-- Nucleo Icons -->
  <link href="{{ asset('argon/css/nucleo-icons.css') }}" rel="stylesheet" />
  <link href="{{ asset('argon/css/nucleo-svg.css') }}" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <link href="{{ asset('argon/css/nucleo-svg.css') }}" rel="stylesheet" />
  <!-- CSS Files -->
  <link id="pagestyle" href="{{ asset('argon/css/argon-dashboard.css?v=2.0.4') }}" rel="stylesheet" />
  <style>
    @media (max-width: 1199.98px) {
      .sidenav {
        position: fixed;
        top: 0;
        left: -300px;
        width: 250px;
        height: 100%;
        background: #fff;
        transition: all 0.3s;
        z-index: 1050;
        box-shadow: 3px 0 10px rgba(0,0,0,0.1);
      }
      .sidenav.show {
        left: 0;
      }
      .main-content {
        margin-left: 0 !important;
      }
      .navbar-toggler {
        display: block !important;
        margin-right: 10px;
      }
    }
  </style>
</head>

<body class="g-sidenav-show   bg-gray-100">
  <div class="min-height-300 bg-primary position-absolute w-100"></div>

  @include('layouts.sidebar')

  <main class="main-content position-relative border-radius-lg ">
    @include('layouts.topnav')
    <div class="container-fluid py-4">
      @include('layouts.partials.alerts')
      {{ $slot }}

      @include('layouts.footer')
    </div>
  </main>

  <!--   Core JS Files   -->
  <script src="{{ asset('argon/js/core/popper.min.js') }}"></script>
  <script src="{{ asset('argon/js/core/bootstrap.min.js') }}"></script>
  <script src="{{ asset('argon/js/plugins/perfect-scrollbar.min.js') }}"></script>
  <script src="{{ asset('argon/js/plugins/smooth-scrollbar.min.js') }}"></script>
  <script>
    // Inisialisasi scrollbar jika diperlukan
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }

    // Fungsi untuk toggle sidebar
    document.addEventListener('DOMContentLoaded', function() {
      const sidebar = document.querySelector('.sidenav');
      const sidebarToggle = document.getElementById('sidebarToggle');

      // Tampilkan tombol toggle di mobile
      if (window.innerWidth < 992) {
        if (sidebarToggle) sidebarToggle.style.display = 'block';
      }

      // Toggle sidebar saat tombol diklik
      if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function(e) {
          e.stopPropagation();
          sidebar.classList.toggle('show');
        });
      }

      // Tutup sidebar saat mengklik di luar
      document.addEventListener('click', function() {
        if (window.innerWidth < 992) {
          sidebar.classList.remove('show');
        }
      });

      // Jangan tutup sidebar saat mengklik di dalam sidebar
      if (sidebar) {
        sidebar.addEventListener('click', function(e) {
          e.stopPropagation();
        });
      }

      // Atur ulang tampilan saat ukuran layar berubah
      function handleResize() {
        if (window.innerWidth >= 992) {
          sidebar.classList.remove('show');
          if (sidebarToggle) sidebarToggle.style.display = 'none';
        } else {
          if (sidebarToggle) sidebarToggle.style.display = 'block';
        }
      }

      window.addEventListener('resize', handleResize);

      // Panggil sekali saat pertama kali dimuat
      handleResize();
    });
  </script>
  <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="{{ asset('argon/js/argon-dashboard.min.js?v=2.0.4') }}"></script>
  @stack('scripts')
</body>

</html>
