<script>
    $(document).ready(function() {
        $('.accept-link').on('click', function(e) {
            e.preventDefault();
            let id = $(this).data('link-id');

            console.log(id);

            $.ajax({
                url: '{{ url('relationships/accept') }}/' + id,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
            }).done(function(res) {}).fail(function(jqXHR, textStatus, errorThrown) {
                alert("AJAX call failed: " + textStatus + ", " + errorThrown);
            });
        });

        $('.reject-link').on('click', function(e) {
            e.preventDefault();
            let id = $(this).data('link-id');

            console.log(id);
            console.log('{{ url('relationships/reject') }}/' + id);

            $.ajax({
                url: '{{ url('relationships/reject') }}/' + id,
                method: 'POST',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
            }).done(function(res) {}).fail(function(jqXHR, textStatus, errorThrown) {
                alert("AJAX call failed: " + textStatus + ", " + errorThrown);
            });
        });
    });
</script>
