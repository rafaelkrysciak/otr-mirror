@if(!(Auth::user() && Auth::user()->isPremium()))
    <div class="text-center well adcontainer">
        <script type='text/javascript'><!--//<![CDATA[
        var m3_u = (location.protocol=='https:'?'https://r.hq-mirror.de/delivery/ajs.php':'http://r.hq-mirror.de/delivery/ajs.php');
        var m3_r = Math.floor(Math.random()*99999999999);
        if (!document.MAX_used) document.MAX_used = ',';
        document.write ("<scr"+"ipt type='text/javascript' src='"+m3_u);
        document.write ("?zoneid=1&amp;block=1&amp;blockcampaign=1");
        document.write ('&amp;cb=' + m3_r);
        if (document.MAX_used != ',') document.write ("&amp;exclude=" + document.MAX_used);
        document.write (document.charset ? '&amp;charset='+document.charset : (document.characterSet ? '&amp;charset='+document.characterSet : ''));
        document.write ("&amp;loc=" + escape(window.location));
        if (document.referrer) document.write ("&amp;referer=" + escape(document.referrer));
        if (document.context) document.write ("&context=" + escape(document.context));
        if (document.mmm_fo) document.write ("&amp;mmm_fo=1");
        document.write ("'><\/scr"+"ipt>");
        //]]>--></script><noscript><a href='http://r.hq-mirror.de/delivery/ck.php?n=af3aa95a&amp;cb=INSERT_RANDOM_NUMBER_HERE' target='_blank'><img src='http://r.hq-mirror.de/delivery/avw.php?zoneid=1&amp;cb=INSERT_RANDOM_NUMBER_HERE&amp;n=af3aa95a' border='0' alt='' /></a></noscript>
    </div>
@endif