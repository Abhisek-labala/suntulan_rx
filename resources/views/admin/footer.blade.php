<!-- jQuery (only one version - load first) -->
<script src="{{ asset('js/bootstrap/jquery-3.7.1.min.js') }}?v={{ rand() }}" type="text/javascript"></script>

<!-- jQuery UI for Datepicker -->
<link rel="stylesheet" href="{{ asset('css/ui/1.13.1/themes/base/jquery-ui.css') }}?v={{ rand() }}">
<script src="{{ asset('js/ui/1.13.2/jquery-ui.js') }}?v={{ rand() }}"></script>

<!-- Bootstrap JS -->
<script src="{{ asset('js/bootstrap/bootstrap.bundle.min.js') }}?v={{ rand() }}" type="text/javascript"></script>

<!-- Select2 -->
<script src="{{ asset('js/npm/select2@4.1.0-rc.0/dist/js/select2.min.js') }}?v={{ rand() }}"></script>

<!-- Chart.js & Patternomaly -->
<script src="{{ asset('js/npm/chart.js') }}?v={{ rand() }}"></script>
<script src="{{ asset('js/npm/patternomaly.js') }}?v={{ rand() }}"></script>
<script src="{{ asset('js/npm/sweetalert2@11.js') }}?v={{ rand() }}"></script>

<!-- Moment.js for date formatting -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

<!-- DataTables core + extensions -->
<script src="{{ asset('js/jquery.dataTables.min.js') }}?v={{ rand() }}"></script>
<script src="{{ asset('js/dataTables.responsive.min.js') }}?v={{ rand() }}"></script>
<script src="{{ asset('js/dataTables.buttons.min.js') }}?v={{ rand() }}"></script>
<script src="{{ asset('js/jszip.min.js') }}?v={{ rand() }}"></script>
<script src="{{ asset('js/pdfmake.min.js') }}?v={{ rand() }}"></script>
<script src="{{ asset('js/vfs_fonts.js') }}?v={{ rand() }}"></script>
<script src="{{ asset('js/buttons.html5.min.js') }}?v={{ rand() }}"></script>
<script src="{{ asset('js/buttons.print.min.js') }}?v={{ rand() }}"></script>

<!-- Custom script -->
<script src="{{ asset('js/bootstrap/script.js') }}?v={{ rand() }}"></script>

<!-- Toastr -->
<script src="{{ asset('js/toastr.min.js') }}?v={{ rand() }}" type="text/javascript"></script>

<!-- Initialize components -->
<script>
    $(document).ready(function () {


        // Datepicker
        $("#datepicker").datepicker({
            dateFormat: 'yy-mm-dd'
        });
    });
</script>

@include('partials.toastr_flash')
