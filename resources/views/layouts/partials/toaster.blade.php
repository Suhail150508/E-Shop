<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ensure toastr is loaded
        if (typeof toastr === 'undefined') {
            return;
        }

        @if(Session::has('success'))
            toastr.success({!! json_encode(Session::get('success')) !!}, {!! json_encode(__('common.success')) !!});
        @endif

        @if(Session::has('status'))
            toastr.success({!! json_encode(Session::get('status')) !!}, {!! json_encode(__('common.success')) !!});
        @endif

        @if(Session::has('error'))
            toastr.error({!! json_encode(Session::get('error')) !!}, {!! json_encode(__('common.error')) !!});
        @endif

        @if(Session::has('info'))
            toastr.info({!! json_encode(Session::get('info')) !!}, {!! json_encode(__('common.info')) !!});
        @endif

        @if(Session::has('warning'))
            toastr.warning({!! json_encode(Session::get('warning')) !!}, {!! json_encode(__('common.warning')) !!});
        @endif

        // Handle Validation Errors
        @if(isset($errors) && $errors->any())
            @foreach($errors->all() as $error)
                toastr.error({!! json_encode($error) !!}, {!! json_encode(__('common.validation_error')) !!});
            @endforeach
        @endif
    });
</script>