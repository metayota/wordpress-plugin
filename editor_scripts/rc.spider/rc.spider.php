<?php namespace Metayota; include_once('../metayota/library.php');   include_once('../google.ads/google.ads.php');  
    $html = str_replace("document.body.appendChild(Tag.render('rc.web.app').node);","",$data['html']);
    $html = str_replace("document.body.appendChild(Tag.render('rc.web.app').node);","",$html);
    $html = str_replace('<script type="text/javascript" src="/resource/framework.javascript"></script>',"",$html);
    

    $adCode = '<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-6016816069265677"
     crossorigin="anonymous"></script>
<!-- wide -->
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-6016816069265677"
     data-ad-slot="8620636872"
     data-ad-format="auto"
     data-full-width-responsive="true"></ins>
<script>
     (adsbygoogle = window.adsbygoogle || []).push({});
</script>';
    
    $html = str_replace("<!-- ***ADVERTISEMENT*** -->",$adCode,$html);
 //   $html = str_replace("<!-- ***FOOTER-AD*** -->",' <script async="async" data-cfasync="false" src="//arsnivyr.com/1?z=5678950"></script>',$html);

    $db->update('static_content',array('path'=>$data['url'],'content'=>$html, 'last_index'=>date('Y-m-d H:i:s')),array('path'=>$data['url'])); 
    success();
?>