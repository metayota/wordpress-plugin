<?php namespace Metayota; include_once('../metayota/library.php');   include_once('../translation-service/translation-service.php');   
$fns = $db->getAll('php_function');
/*
$functions = get_defined_functions(true);

foreach($functions['internal'] as $fn) {
    $db->insert('php_function',array('name'=>$fn,'version'=>'8.2'));
}
*/

foreach($fns as $fn) {
    $functionName = str_replace('_','-',$fn['name']);
    $documentation = file_get_contents("/var/www/html/php-documentation/function.$functionName.html");
    if(preg_match("/<title>(.+)<\/title>/i", $documentation, $matches)) {
        $title = $matches[1];
        $title = html_entity_decode($title);
        $db->update('php_function',array('description'=>$title,'definition'=>getFunctionSignature($fn['name'])),array('id'=>$fn['id']));
    }
    else
        print "The page doesn't have a title tag";
}

function getFunctionSignature(string $functionName): string {
    $refFunction = new ReflectionFunction($functionName);

    // Prepare function name
    $signature = $refFunction->getName() . "(";

    // Prepare function parameters
    $params = [];
    foreach ($refFunction->getParameters() as $param) {
        $paramStr = "";

        // Get parameter type
        if ($param->hasType()) {
            $paramStr .= $param->getType() . " ";
        }

        // Get parameter name
        $paramStr .= '$' . $param->getName();

        // Check if parameter has a default value
        if ($param->isDefaultValueAvailable()) {
            $paramStr .= " = " . var_export($param->getDefaultValue(), true);
        }

        $params[] = $paramStr;
    }

    // Add parameters to signature
    $signature .= implode(", ", $params);

    // Close function signature
    $signature .= ")";

    // Include return type if available
    if ($refFunction->hasReturnType()) {
        $signature .= ": " . $refFunction->getReturnType();
    }

    return $signature;
}

?>