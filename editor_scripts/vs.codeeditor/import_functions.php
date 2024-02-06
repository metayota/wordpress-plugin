<?php namespace Metayota; include_once('../metayota/library.php');   include_once('../translation-service/translation-service.php');  
$functions = get_defined_functions(true);

foreach($functions['internal'] as $fn) {
    $db->insert('php_function',array('name'=>$fn,'version'=>'8.2'));
}
?>