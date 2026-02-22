
<script src="/assets/libs/jquery/jquery.min.js"></script>
<script src="/assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- App js -->
<script src="/assets/libs/metismenu/metisMenu.min.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
    $(document).ready(function () {
        @if(session()->has('message'))
        notify("{{ session()->get('message') }}", "{{ session()->has('color') }}");
        @endif

        @if(session()->has('errors'))
        @foreach(json_decode(session()->get('errors')) as $key => $value)
        notify("{{ $value[0] }}", "error");
        @endforeach
        @endif

    });

    function notify(message, type) {
        console.log(message, type)
        toastr.options = {
            closeButton: true,
            progressBar: true,
            preventDuplicates: true,
            positionClass: "toast-top-right",
            timeOut: 3000, // Set the duration for the toast notification
        };

        if (type == 'success') {
            toastr.success(message, "Success");
        } else if (type == 'error') {
            toastr.error(message, "Error");
            toastr.options.timeOut = 7000;
        } else if (type == 'warning') {
            toastr.warning(message, "Warning");
        } else {
            toastr.info(message, type);
        }
    }

</script>

@yield('script')

<script src="/assets/js/app.js"></script>
