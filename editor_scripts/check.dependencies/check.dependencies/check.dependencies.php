<?php namespace Metayota; include_once('../metayota/library.php');   
    if (!$serverDB) {
        errorMsg('You are not logged into a server!');
    }

    $resourceName = $data['resource'];
    $resource = $serverDB->getByName('resource',$resourceName);
    $implementation = json_decode($resource['implementation']);
    $dependenciesList = !empty($resource['dependencies']) ? json_decode($resource['dependencies']) : [];
    $allTagRows = $serverDB->getAllByQuery("SELECT name FROM resource WHERE type='tag'");
    $allTags = array_column($allTagRows, 'name');
   
    
    $dependencies = [];
    foreach($dependenciesList as $dependency) {
        $dependencies[] = $dependency->name;
    }
    $tagsToAddToDependencies = [];
    $allValidatorRows = $serverDB->getAllByQuery("SELECT name FROM resource WHERE type='validator'");
    $allValidators = array_column($allValidatorRows, 'name');

    $parametersRow = $resource['parameters'];
    if (!empty($parametersRow)) {
        $parameters = json_decode($parametersRow,true);
        foreach($parameters as $parameter) {
            if (!empty($parameter['validators'])) {
                foreach($parameter['validators'] as $validator) {
                    
                    if (!empty($validator['name'])) {
                        $validatorName = $validator['name'];
                        if (!in_array($validatorName, $dependencies)) {
                            if (in_array($validatorName,$allValidators)) {
                                $tagsToAddToDependencies[] = $validatorName;
                            }
                        }
                    }
                }
            }
        }
    }
    
    if (isset($implementation->html)) {
        $html = $implementation->html;
    } else if (isset($implementation->{'html.php'})) {
        $html = $implementation->{'html.php'};
    }
    if (!empty($html)) {
        
        $doc = new \DOMDocument();
        @$doc->loadHTML($html);

        $tags = array();
        foreach (@$doc->getElementsByTagName('*') as $tag) {
            $tagName = $tag->tagName;
            if (!in_array($tagName, $tags)) {
                $tags[] = $tagName;
            }
        }

        

        foreach($tags as $tag) {
            if (!in_array($tag, $dependencies)) {
                if (in_array($tag,$allTags)) {
                    $tagsToAddToDependencies[] = $tag;
                }
            }
         //   if ($tag)
        }
        $tagsToAddToDependencies = array_unique($tagsToAddToDependencies);
        echo json_encode($tagsToAddToDependencies);
    }

?>