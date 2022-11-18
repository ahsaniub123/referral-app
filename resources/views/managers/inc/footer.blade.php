<script src="{{ asset('admin/js/tabler.min.js') }}"></script>
<script src="https://code.jquery.com/jquery-1.10.2.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    @if(Session::has('success'))
    toastr.success("{{ Session::get('success') }}") ;
    @endif

    @if(Session::has('error'))
    toastr.error("{{ Session::get('error') }}") ;
    @endif

    @if($errors->any())
    toastr.error("{{ implode('', $errors->all(':message')) }}");
    @endif
</script>

<script>
    $(document).ready(function () {
        // CSV FILE IMPORT
        $('body').on('click','.import_product_button',function () {
            $(`#import-file-input`).trigger('click');
        });

        $('body').on('change','.product-csv-uploader',function () {
            console.log($('#import-form'));
            $('#import-form').submit();
        });

        $('select').each(function () {
            $(this).select2({
                dropdownParent: $(this).parent(),
                width: "100%",
            });
        });
    });
</script>
