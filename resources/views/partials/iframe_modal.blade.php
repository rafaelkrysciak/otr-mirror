<div id="iframeModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title">&nbsp;</h4>
            </div>
            <div class="modal-body">
                <iframe src="" width="99.6%" height="950" frameborder="0"></iframe>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<script>
    $('#iframeModal').on('show.bs.modal', function (event) {
        var src, link = $(event.relatedTarget);
        if(link.data('src')) {
            src = link.data('src');
        } else if(link.attr('href')) {
            src = link.attr('href');
        } else {
            return false;
        }

        if(src.search(/\?/) > 0) {
            src = src + '&nomenu=1';
        } else {
            src = src + '?nomenu=1';
        }

        var modal = $(this);
        modal.find('iframe').attr('src', src);
        rescale();
    });
    function rescale(){
        var size = {width: $(window).width() , height: $(window).height() };
        /*CALCULATE SIZE*/
        var offset = 20, offsetBody = 150, modal = $('#iframeModal');

        modal.css('height', size.height - offset );
        modal.find('iframe').css('height', size.height - (offset + offsetBody));
        modal.css('top', 0);
    }

    $(window).bind("resize", rescale);
</script>