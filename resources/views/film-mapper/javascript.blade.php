<script>
    $(".film-select").select2({
        ajax: {
            width: 'style',
            url: '{{ url('film/search-for-select') }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term, // search term
                    page: params.page
                };
            },
            processResults: function (data, params) {
                // parse the results into the format expected by Select2.
                // since we are using custom formatting functions we do not need to
                // alter the remote JSON data
                params.page = params.page || 1;
                return {
                    results: data.items,
                    pagination: {
                        more: (params.page * 30) < data.total_count
                    }
                };
            },
            cache: true
        },
        escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
        minimumInputLength: 3,
        _templateResult: function(repo) {
            if (repo.loading) return repo.text;
            return repo.text;
        },
        _templateSelection: function(repo) {
            return repo.text || repo.text;
        }
    });
</script>
