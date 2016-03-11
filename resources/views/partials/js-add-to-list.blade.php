<script>
    $('.add-to-list').click(function (event) {
        $this = $(this);
        var list = $this.data('list');
        var itemId = $this.data('id');
        var added = $this.hasClass('list-active');

        if(list == 'films') {
            addToFilms(itemId, added);
        } else {
            addToList(list, itemId, added);
        }

        return false;
    });

    function addToList(list, itemId, added) {
        $.ajax({
            method: "POST",
            url: added ? "{{url('user-list/remove')}}" : "{{url('user-list/add')}}",
            data: {tv_program_id: itemId, list: list, _token: '{{ csrf_token() }}'}
        }).done(function (msg, status) {
            if (status == 'success' && msg.status == 'OK') {
                if (added) {
                    $this.removeClass('list-active');
                } else {
                    $this.addClass('list-active');
                }
            }
        });
    }

    function addToFilms(filmId, added) {
        var request = $.ajax((added ? "{{url('user-films/remove')}}" : "{{url('user-films/add')}}") + '/' + filmId);
        request.done(function (msg, status) {
            if (status == 'success' && msg.status == 'OK') {
                if (added) {
                    $this.removeClass('list-active');
                } else {
                    $this.addClass('list-active');
                }
            }
        });
    }

</script>