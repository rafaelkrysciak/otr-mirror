
if (window.location.hostname != 'www.'+'hq-'+'mirror'+'.de') {
     window.location = 'http://'+'www.'+'hq-'+'mirror'+'.de/';
}

// carousel
carouselTimer = setInterval(function(){$('.carousel .controls .next').click();},2500);
$('.carousel').on('mouseover', function() {clearInterval(carouselTimer)});
$('.carousel').on('mouseout', function() {carouselTimer=setInterval(function(){$('.carousel .controls .next').click()},2500)});


// Go Top
/*Add class when scroll down*/
$(window).scroll(function(event){
    var scroll = $(window).scrollTop();
    if (scroll >= 550) {
        $(".go-top").addClass("show");
    } else {
        $(".go-top").removeClass("show");
    }
});

/*Animation anchor*/
$('a.go-top').click(function(){
    $('html, body').animate({
        scrollTop: $( $(this).attr('href') ).offset().top
    }, 1000);
});

/*
 * Delete handler
 */
(function() {

    var laravel = {
        initialize: function() {
            this.registerEvents();
        },

        registerEvents: function() {
            $('body').on('click', 'a[data-method]', this.handleMethod);
        },

        handleMethod: function(e) {
            var link = $(this);
            var httpMethod = link.data('method').toUpperCase();
            var handler = link.data('handler').toLowerCase();
            var form;

            // If the data-method attribute is not PUT or DELETE,
            // then we don't know what to do. Just ignore.
            if ( $.inArray(httpMethod, ['PUT', 'DELETE']) === - 1 ) {
                return;
            }

            // Allow user to optionally provide data-confirm="Are you sure?"
            if ( link.data('confirm') ) {
                if ( ! laravel.verifyConfirm(link) ) {
                    return false;
                }
            }

            if(handler == 'ajax') {
                laravel.ajaxDelete(link);
            } else {
                form = laravel.createForm(link);
                form.submit();
            }

            e.preventDefault();
        },

        verifyConfirm: function(link) {
            return confirm(link.data('confirm'));
        },

        ajaxDelete: function(link) {
            var request = $.ajax({
                method: "POST",
                url: link.attr('href'),
                data: {
                    _token: window.csrfToken,
                    _method: link.data('method')
                }
            });
            request.done(function( data ) {
                link.trigger( "done", arguments );
                //if(data.status != 'OK') {
                   // alert( "Request failed: " + (data.message || 'Unknown Error'));
                //}
            });
            request.fail(function( jqXHR, textStatus ) {
                link.trigger( "fail", arguments );
                //alert( "Request failed: " + textStatus );
            });
        },

        createForm: function(link) {
            var form =
                $('<form>', {
                    'method': 'POST',
                    'action': link.attr('href')
                });

            var token =
                $('<input>', {
                    'name': '_token',
                    'type': 'hidden',
                    'value': window.csrfToken
                });

            var hiddenInput =
                $('<input>', {
                    'name': '_method',
                    'type': 'hidden',
                    'value': link.data('method')
                });

            return form.append(token, hiddenInput)
                .appendTo('body');
        }
    };

    laravel.initialize();

})();