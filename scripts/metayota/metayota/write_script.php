<?php
namespace Metayota;
include_once('../metayota/library.php');
$resourcename = isset($data['resourcename']) ? $data['resourcename'] : $_GET['resourcename'];
$resource = $db->getByName('resource',$resourcename);

if (empty($resource)) {
    $parentFolder = realpath('..');
    $resourceFolder = realpath($parentFolder . '/' . $resourcename);
    
    if ($resourceFolder && dirname($resourceFolder) === $parentFolder) {
        deleteDirectory($resourceFolder);
    }
}

if (isset($resource['implementation']) && $resource['implementation'] != '{}') {
    $implementations = json_decode($resource['implementation']);
} else {
    $implementations = null;
}

if (!empty($implementations)) {
    if (getConfig('wordpress')) {
        $folder = '../../scripts/'.$resource['name'].'/';
    } else {
        $folder = '../'.$resource['name'].'/';
    }
    if (!is_dir($folder)) {
        mkdir($folder);
    }
    if (!empty($resource['dependencies'])) {
        $dependencies = json_decode($resource['dependencies']);
    }
    
    $includeStr = "namespace Metayota; include_once('../metayota/library.php'); ";
    if (!empty($dependencies) && $resource['name'] != 'metayota') {
        foreach($dependencies as $dependency) {
            if ($dependency->type == 'php-library') {
                $dependencyFile = '../'.$dependency->name.'/'.$dependency->name.'.php';
                $includeStr.= "  include_once('$dependencyFile'); ";
            }
        }
        
    }
    
    foreach($implementations as $technology => $implementation) {
        if (str_ends_with($technology,'php') && $technology != 'php') {
            $fileName = $folder.$technology;
        } else {
            if ($technology == 'html' || $technology == 'css' || $technology == 'javascript' || $technology == 'php') {
                $fileName = $folder.$resource['name'].'.'.$technology;
            } else {
                $fileName = $folder.$technology;
            }
        }
        
        if ($resource['type'] == 'php-library' && $resource['name'] != 'metayota') {
            if (!str_starts_with($implementation, '<?php')) {
                $implementation = "<?php\n".$implementation."\n?>";
            }
        }
        
        if (!empty($includeStr)  && $resource['name'] != 'metayota') {
            if (str_ends_with($technology,'php')) {
                if (str_starts_with($implementation,'<?php')) {
                    $implementation = substr($implementation, 5);
                    $implementation = "<?php ".$includeStr." ".$implementation;
                }  else {
                    $implementation = "<?php ".$includeStr." ?>".$implementation;
                }
            }
        }
        
        
        if (file_put_contents($fileName, $implementation) === false) {
            //  echo 'file could not be written';
        }
    }
}



function deleteDirectory($dirPath) {
    if (!is_dir($dirPath)) {
        throw new InvalidArgumentException("$dirPath must be a directory");
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
        $dirPath .= '/';
    }
    $files = scandir($dirPath);
    foreach ($files as $file) {
        if ($file == '.' || $file == '..') {
            continue;
        }
        $filePath = $dirPath . $file;
        if (is_dir($filePath)) {
            deleteDirectory($filePath);
        } else {
            unlink($filePath);
        }
    }
    rmdir($dirPath);
}
?>