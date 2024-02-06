<?php
namespace Metayota;
function recursive_access(&$array, $keys, $softTranslate) {
    if (empty($keys)) {
        if (is_array($array)) {
            array_walk_recursive($array, function(&$item) use ($softTranslate) {
                $item = $softTranslate($item);
            });
        } else {
            $array = $softTranslate($array);
        }
        return;
    }
    
    $key = array_shift($keys);
    
    if (isset($array[$key])) {
        recursive_access($array[$key], $keys, $softTranslate);
    } else {
        foreach ($array as &$item) {
            if (is_array($item)) {
                recursive_access($item, array_merge([$key], $keys), $softTranslate);
            }
        }
    }
}


function translateParameters(&$parameters) {
    global $db;
    if (empty($parameters)) {
        return null;
    }
    foreach($parameters as &$parameter) {
        $parameterType = $db->getByName('parametertype',$parameter['type']);
        $parameter['title'] = softTranslate($parameter['title']);
        if (!empty($parameter['documentation'])) {
            $parameter['documentation'] = softTranslate($parameter['documentation']);
        }
        if (!empty($parameter['validators'])) {
            foreach($parameter['validators'] as &$validator) {
                if (!empty($validator['options']) && !empty($validator['options']['title'])) {
                    $validator['options']['title'] = softTranslate($validator['options']['title']);
                }
            }
        }
        if (!empty($parameterType['translate_settings'])) {
            $translateSettings = json_decode($parameterType['translate_settings'],true);
            foreach($translateSettings as $translateSetting) {
                if (!str_contains($translateSetting,":")) {
                    if (isset($parameter[$translateSetting])) {
                        $parameter[$translateSetting] = softTranslate($parameter[$translateSetting]);
                    }
                } else {
                    recursive_access($parameter, explode(':', $translateSetting), '\Metayota\softTranslate');
                }
            }
        }
        
        
    }
}

function getBrowserJSON($resourceName, $db)
{
    
    
    $resource = getResourceByName($resourceName, $db);
    if ($resource === false) {
        return false;
    }
    $result = array();
    $result['id'] = $resource['id'];
    if (empty($resource['parameters'])) {
        $result['parameters'] = array();
    } else {
        $result['parameters'] = json_decode($resource['parameters'],true);
        translateParameters($result['parameters']);
    }
    if (!empty($resource['implementation'])) {
        $implementation = json_decode($resource['implementation']);
    }
    $result['implementation'] = array();
    $resourceFolder = '../'.$resource['name'].'/';
    $filenameHTML = $resourceFolder.$resource['name'].'.html';
    if (file_exists($filenameHTML)) {
        $result['implementation']['html'] = file_get_contents($filenameHTML);//$implementation->html;
    }
    $filenameCSS = $resourceFolder.$resource['name'].'.css';
    if (file_exists($filenameCSS)) {
        $result['implementation']['css'] = file_get_contents($filenameCSS);
    }
    $filenameJavaScript = $resourceFolder.$resource['name'].'.javascript';
    if (file_exists($filenameJavaScript)) {
        $result['implementation']['javascript'] = file_get_contents($filenameJavaScript);
    }
    if (isset($implementation->{'html.php'})) {
        $result['implementation']['html'] = callAction($resourceName, 'html', $_GET);
    }
    if (isset($implementation->{'data.php'})) {
        $result['data'] = json_decode(callAction($resourceName, 'data',$_GET));
    } else {
        if (!empty($resource['data'])) {
            $result['data'] = json_decode($resource['data']);
        }
    }
    $result['name'] = $resource['name'];
    $result['title'] = softTranslate($resource['title']);
    if (!empty($resource['configuration'])) {
        $result['configuration'] = json_decode($resource['configuration']);
    }
    if (!empty($resource['allowed_subelements'])) {
        $result['allowed_subelements'] = json_decode($resource['allowed_subelements']);
    }
    $result['type'] = $resource['type'];
    if (!empty($resource['dependencies'])) {
        $result['dependencies'] = json_decode($resource['dependencies']);
    }
    $result['extends_resource'] = $resource['extends_resource'];
    return $result;
}
function mergeBrowserJSON($baseName, $resource, $db)
{
    $baseResource = getBrowserJSON($baseName, $db);
    if (!empty($baseResource['extends_resource'])) {
        $baseResource = mergeBrowserJSON($baseResource['extends_resource'], $baseResource, $db);
    }
    $result = array();
    $result['id'] = $resource['id'];
    $result['parameters'] = mergeJSONArray($baseResource, $resource, 'parameters');
    $result['implementation'] = mergeJSONArray($baseResource, $resource, 'implementation');
    $result['name'] = $resource['name'];
    $result['title'] = $resource['title'];
    $result['configuration'] = mergeJSONArray($baseResource, $resource, 'configuration');
    if (isset($resource['data'])) {
        $result['data'] = $resource['data'];
    } else if (isset($baseResource['data'])) {
        $result['data'] = $baseResource['data'];
    }
    $result['allowed_subelements'] = mergeJSONArray($baseResource, $resource, 'allowed_subelements');
    $result['type'] = $resource['type'];
    $result['dependencies'] = mergeJSONArray($baseResource, $resource, 'dependencies');
    $result['extends_resource'] = $resource['extends_resource'];
    return $result;
}

?>