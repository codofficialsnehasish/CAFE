    <!-- jQuery library js -->
    <script src="{{ asset('assets/dashboard-assets/js/lib/jquery-3.7.1.min.js') }}"></script>
    <!-- Bootstrap js -->
    <script src="{{ asset('assets/dashboard-assets/js/lib/bootstrap.bundle.min.js') }}"></script>
    <!-- Apex Chart js -->
    <script src="{{ asset('assets/dashboard-assets/js/lib/apexcharts.min.js') }}"></script>
    <!-- Data Table js -->
    <script src="{{ asset('assets/dashboard-assets/js/lib/dataTables.min.js') }}"></script>
    <!-- Iconify Font js -->
    <script src="{{ asset('assets/dashboard-assets/js/lib/iconify-icon.min.js') }}"></script>
    <!-- jQuery UI js -->
    <script src="{{ asset('assets/dashboard-assets/js/lib/jquery-ui.min.js') }}"></script>
    <!-- Vector Map js -->
    <script src="{{ asset('assets/dashboard-assets/js/lib/jquery-jvectormap-2.0.5.min.js') }}"></script>
    <script src="{{ asset('assets/dashboard-assets/js/lib/jquery-jvectormap-world-mill-en.js') }}"></script>
    <!-- Popup js -->
    <script src="{{ asset('assets/dashboard-assets/js/lib/magnifc-popup.min.js') }}"></script>
    <!-- Slick Slider js -->
    <script src="{{ asset('assets/dashboard-assets/js/lib/slick.min.js') }}"></script>
    <!-- prism js -->
    <script src="{{ asset('assets/dashboard-assets/js/lib/prism.js') }}"></script>
    <!-- file upload js -->
    <script src="{{ asset('assets/dashboard-assets/js/lib/file-upload.js') }}"></script>
    <!-- audioplayer -->
    <script src="{{ asset('assets/dashboard-assets/js/lib/audioplayer.js') }}"></script>

    <!-- main js -->
    <script src="{{ asset('assets/dashboard-assets/js/app.js') }}"></script>
    
    <!--tinymce js-->
    <script src="{{ asset('assets/dashboard-assets/js/tinymce/tinymce.min.js') }}"></script>

    <!-- init js -->
    <script src="{{ asset('assets/dashboard-assets/js/form-editor.init.js') }}"></script>

    <script src="{{ asset('assets/dashboard-assets/js/lib/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/dashboard-assets/js/pages/form-advanced.init.js') }}"></script>

    <!-- toast message -->
    <script src="{{ asset('assets/dashboard-assets/js/lib/toast/toastr.js') }}"></script>
    <script src="{{ asset('assets/dashboard-assets/js/pages/toastr.init.js') }}"></script>
    <!-- toast message -->
    @include('layouts._massages')

    @yield('script')