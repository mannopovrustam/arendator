<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js" integrity="sha512-zP5W8791v1A6FToy+viyoyUUyjCzx+4K8XZCKzW28AnCoepPNIXecxh9mvGuy3Rt78OzEsU+VCvcObwAMvBAww==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<style>
    .jconfirm-box {
    max-width: 500px; /* Set the maximum width as needed */
}
</style>
<script>
    var csrfToken = "{{ csrf_token() }}";

    $(document).keydown(function(event) {
        if (event.ctrlKey && event.key === 'Enter') {
            var selectedText = window.getSelection().toString();
            if (selectedText.length > 0) {
                $.confirm({
                    title: 'Xatolikni jo\'natish!',
                    content: 'Xabarni yubormoqchimisiz!',
                    boxWidth: '50%',
                    buttons: {
                        confirm: function () {
                            $.ajax({
                                method: 'POST',
                                url: '/errors',
                                data: {
                                    '_token': csrfToken,
                                    'url': window.location.href,
                                    'error': selectedText
                                },
                                success: function (res) {
                                    alert(res);
                                }
                            })
                        },
                        cancel: function () {

                        },
                    }
                })
            }
        }
    });
</script>
