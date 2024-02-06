<?php namespace Metayota; include_once('../metayota/library.php');   include_once('../google.ads/google.ads.php');  
    if (session_status() != PHP_SESSION_ACTIVE) {
        session_start();
    }
$_SESSION['spider'] = true;
$content = $db->getByQuery("SELECT path FROM static_content ORDER BY last_index ASC");

echo json_encode($content);

?>