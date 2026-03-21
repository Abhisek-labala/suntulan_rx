{{-- Toastr Flash Messages + SweetAlert2 Confirm Helper --}}
<script>
$(document).ready(function () {

    // ── Toastr global options ──────────────────────────────────────
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "4000",
        "extendedTimeOut": "1500",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };

    @if(session()->has('success'))
        toastr.success("{{ addslashes(session('success')) }}", "Success");
    @endif

    @if(session()->has('error'))
        toastr.error("{{ addslashes(session('error')) }}", "Error");
    @endif

    @if(session()->has('message'))
        toastr.info("{{ addslashes(session('message')) }}", "Info");
    @endif

    @if(session()->has('info'))
        toastr.info("{{ addslashes(session('info')) }}", "Info");
    @endif

    @if(session()->has('warning'))
        toastr.warning("{{ addslashes(session('warning')) }}", "Warning");
    @endif

    // ── Auto-bind SweetAlert2 to any form that has data-confirm ────
    // Usage: <form data-confirm="Are you sure?"> ... </form>
    $(document).on('submit', 'form[data-confirm]', function (e) {
        e.preventDefault();
        var form    = this;
        var message = $(form).data('confirm') || 'Are you sure?';
        var title   = $(form).data('confirm-title') || 'Please Confirm';
        var icon    = $(form).data('confirm-icon')  || 'warning';

        Swal.fire({
            title: title,
            text: message,
            icon: icon,
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor:  '#6c757d',
            confirmButtonText: $(form).data('confirm-ok') || 'Yes, proceed!',
            cancelButtonText:  'Cancel',
        }).then(function (result) {
            if (result.isConfirmed) {
                // Remove the listener to allow natural submit
                form.removeEventListener('submit', arguments.callee);
                form.submit();
            }
        });
    });

});

// ── Standalone helper for inline onclick usage ─────────────────────
// Usage: onclick="swalConfirm(this, 'Delete this item?')"
function swalConfirm(el, message, title, icon) {
    var form = $(el).closest('form')[0] || el;
    Swal.fire({
        title: title || 'Please Confirm',
        text:  message || 'Are you sure?',
        icon:  icon || 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor:  '#6c757d',
        confirmButtonText: 'Yes, proceed!',
        cancelButtonText:  'Cancel',
    }).then(function (result) {
        if (result.isConfirmed) {
            form.submit();
        }
    });
    return false;
}
</script>

