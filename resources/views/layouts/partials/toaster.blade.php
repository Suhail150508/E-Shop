<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ensure toastr is loaded
        if (typeof toastr === 'undefined') {
            console.error('Toastr not loaded');
            return;
        }

        @if(Session::has('success'))
            toastr.success("{{ Session::get('success') }}", "Success");
        @endif

        @if(Session::has('status'))
            toastr.success("{{ Session::get('status') }}", "Success");
        @endif

        @if(Session::has('error'))
            toastr.error("{{ Session::get('error') }}", "Error");
        @endif

        @if(Session::has('info'))
            toastr.info("{{ Session::get('info') }}", "Info");
        @endif

        @if(Session::has('warning'))
            toastr.warning("{{ Session::get('warning') }}", "Warning");
        @endif

        // Handle Validation Errors
        @if($errors->any())
            @foreach($errors->all() as $error)
                toastr.error("{{ $error }}", "Validation Error");
            @endforeach
        @endif
    });
</script>