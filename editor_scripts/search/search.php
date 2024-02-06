<?php namespace Metayota; include_once('../metayota/library.php');  
    $searchStr = isset($data['search']) ? $data['search'] : $_POST['search'];
    
    $search = '%'.(mb_strtolower($searchStr)).'%';
    //
    //$search = str_replace('"', '%', $searchStr);
	$res = $serverDB->fetchAllQuery("SELECT * FROM resource WHERE lower(implementation) LIKE :search OR lower(parameters) LIKE :search",array('search'=>$search));
    
    if ($res) {
        $tagFound = $res;
        $results = array();
        foreach($tagFound as $foundTag) {
            if (!empty($foundTag['implementation'])) {
                $impl = json_decode($foundTag['implementation']);
                if (!empty($impl)) {
                    $matchingLines = array();
                //  $results = array();

                    foreach($impl as $key => $implementation) {
                        $lines = explode("\n",$implementation);
                        $i = 0;
                        foreach($lines as $line) {
                            if( stripos( $line, $searchStr ) !== false ) {
                                if ( !isset($matchingLines[$key])) {
                                    $matchingLines[$key] = array();
                                }
                                $matchingLines[$key][] = array('code' => $line, 'line' => $i+1);
                            }
                            $i++;
                        }
                        if (isset($matchingLines[$key])) {
                            $results[] = array('name' => $foundTag['name'], 'type' => 'Code', 'language'=>$key,'lines'=>$matchingLines[$key]);
                        }
                    }
                }
            }

            $items = array();
            $parameters = !empty($foundTag['parameters']) ? json_decode($foundTag['parameters']) : null;
            if (!empty($parameters)) {
                foreach($parameters as $parameter) {
                    $paramJson = json_encode($parameter);
                    if (stripos($paramJson,$searchStr) !== false) {
                        $items[] = array('name' => $parameter->name, 'type'=>'Parameter');
                    }
                }
            }
            if (count($items) > 0) {
                $results[] = array('name' => $foundTag['name'], 'type'=>'Parameter', 'parameters'=>$items);
            }
            
          //  $tags[] = array( 'name' => $foundTag['name'],'results' => $results);
        }
        
        echo json_encode($results);
    } else {
        echo json_encode(array());
    }
?>