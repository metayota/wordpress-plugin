<?php namespace Metayota; include_once('../metayota/library.php');   include_once('../translation-service/translation-service.php');  

if(strstr(strtolower($_SERVER['HTTP_USER_AGENT']), "googlebot") || empty($_SESSION['loggedInUser'])) {

    
    $static = $db->get('static_content',array('path'=>$_SERVER['REQUEST_URI']));
    if (!empty($static)) {
        
        die($static['html']);
    }
}

echo '<!doctype html>'."\n";
echo '<html lang="en">'."\n";
echo '<head>'."\n";
echo '    <base href="/" target="_blank">'."\n";
echo '    <meta charset="utf-8">'."\n";
echo '    <meta http-equiv="X-UA-Compatible" content="IE=edge">'."\n";
echo '    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">'."\n";
echo '    <title>Metayota</title>'."\n";
if (getConfig('wordpress')) {
    echo '    <link rel="stylesheet" type="text/css" href="/wp-content/plugins/metayota/editor_scripts/index/index.css">'."\n";
    echo '    <script type="text/javascript" src="/wp-content/plugins/metayota/scripts/framework/framework.javascript"></script>'."\n";

} else {
    echo '    <link rel="stylesheet" type="text/css" href="/resource/index.css">'."\n";
    echo '    <script type="text/javascript" src="/resource/framework.javascript"></script>'."\n";
}

echo '    <script type="text/javascript">'."\n";
if (getConfig('wordpress')) {
    echo "    Resource.wp = true\n";
    echo "    Resource.scriptsPath = '/wp-content/plugins/metayota/scripts/'\n";
}
if (isset($GLOBALS['language'])) {
    echo "        Resource.appendToAllRequests({'language':'".$GLOBALS['language']."'})\n";
} else {
    echo "        Resource.appendToAllRequests({'language':'en'})\n";
}
echo "        Tag.clear('".$_GET['tag']."')\n";
echo "        Tag.registerAndLoad('".$_GET['tag']."')\n";
echo "        let event = function(e) { alert('test') };\n";
echo "        let params = {}\n";
if (getConfig('wordpress') && !empty($_GET['params'])) {
    echo '        params = '.stripslashes($_GET['params'])."\n";
} else if (!empty($_GET['params'])) {
    echo '        params = '.$_GET['params']."\n";
}

echo '    </script>'."\n";
echo '</head>'."\n";
echo '<body>'."\n";
echo '<div class="tab tab-content">'."\n";
echo '    <script type="text/javascript">'."\n";
echo "         let expr = new Expression('window.parent.postMessage(event,\'https://www.metayota.com\')', { jscontext: true });\n";
echo "         let exprs = {event:expr};\n";
echo "         Resource.loaded('".$_GET['tag']."').then(result=> {\n";
echo "              let tag = TagModel.renderTag('".$_GET['tag']."', params, null, { }, exprs)\n";
echo '              document.body.appendChild(tag.node);'."\n";
echo '        });'."\n";
echo '    </script>'."\n";
echo '</div>'."\n";
echo '</body>'."\n";
echo '</html>'."\n";
?>