<?php
namespace Metayota;
require_once('../../conf/db-config.php');
require_once('../../conf/cache-config.php');
require_once('../../conf/main-config.php');
require_once('../metayota/lib.php');
require_once('../metayota/lib_db.php');
require_once('../metayota/lib_access.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $checkReferer = true;
    $requestURI = $_SERVER['REQUEST_URI'];
    foreach ($GLOBALS['no_referer_check'] as $noRefererCheck) {
        if (str_starts_with($requestURI, $noRefererCheck)) {
            $checkReferer = false;
        }
    }
    if ($checkReferer) {
        if (isset($_SERVER['HTTP_REFERER'])) {
            $referer = $_SERVER['HTTP_REFERER'];
            $host = $_SERVER['HTTP_HOST'];
            $isAllowed = false;
            foreach ($GLOBALS['allowed_referers'] as $allowedReferer) {
                if (str_starts_with($referer, $allowedReferer)) {
                    $isAllowed = true;
                }
            }
            if (!$isAllowed) {
                error_log("Metayota Security Check Failed");
                die('METAYOTA SECURITY CHECK FAILED / PLEASE CONTACT THE SERVER ADMINISTRATOR / ERROR CODE M2910');
            }
        } else {
            die('METAYOTA SECURITY CHECK FAILED / HEADER MISSING');
        }
    }
}

function customErrorHandler($severity, $message, $file, $line)
{


            $pattern = '#.*/(?P<resource>[^/]+)/(?P<tab>[^/]+)\.(?P<technology>[^/]+)$#';
            if (preg_match($pattern, $file, $matches)) {
                $executionContext = [];
                $executionContext['resource'] = $matches['resource'];
                $executionContext['tab'] = $matches['tab'];
                $executionContext['message'] = $message;
                $executionContext['line'] = $line;
                
                $executionContext['request_uri'] = substr($_SERVER['REQUEST_URI'],0,500);
                $technology = $matches['technology'];

                if (in_array($technology, ['html', 'css', 'javascript', 'php']) && $executionContext['resource'] == $executionContext['tab']) {
                    $executionContext['tab'] = $technology;
                } else {
                    $executionContext['tab'] = $matches['tab'];
                }

               // print_r($executionContext); // For debugging purposes
            }

            global $db;
            $db->query("INSERT INTO error SET message=:message, line=:line, resource=:resource,tab=:tab,request_uri=:request_uri ON DUPLICATE KEY UPDATE count=count+1, time=CURRENT_TIME()",$executionContext);

}


// Inside library.php
function shutdownHandler() {
    $error = error_get_last();
    if ($error !== NULL) {
        customErrorHandler(null, $error['message'], $error['file'], $error['line']);
    }
}
 
register_shutdown_function('\Metayota\shutdownHandler');

// Set the custom error handler  
set_error_handler("\Metayota\customErrorHandler");



header('Last-Modified: ' . gmdate("D, d M Y H:i:s", time()));

$db = new PDORepository();
global $loggedInUser;
$user_id = isset($loggedInUser['id']) ? $loggedInUser['id'] : NULL;
global $user_id;

if (getConfig('wordpress')) {
    $serverDB = $db;
}
if (getConfig('main_server')) {
    $serverID = isset($_SESSION['server']) ? $_SESSION['server'] : NULL;
    if (empty($serverID)) {
        //if (str_ends_with($_SERVER['SERVER_NAME'], '.metayota.com')) {
            if (isset($_COOKIE['server_id'])) {
                $server = $db->get('server',['user_id'=>$user_id,'id'=>$_COOKIE['server_id']]);
                if (!empty($server)) {
                    $serverID = $_COOKIE['server_id'];
                    if (session_status() != PHP_SESSION_ACTIVE) {
                        session_start();
                    }
                    $_SESSION['server'] = $serverID;
                    session_write_close();
                }
            }
       // }
    }

    if (!empty($serverID)) {
        $server = $db->fetchQuery('SELECT * FROM server WHERE id=:serverid', array('serverid' => $serverID)); // user check?
        if (!empty($server)) {
            $serverDB = new PDORepository($server);

        } else {
            $hasTask = $db->get('task', array('server_id' => $serverID, 'assigned_to_user' => $user_id, 'task_state' => 6));
            if (!empty($hasTask)) {
                $server = $db->fetchQuery('SELECT * FROM server WHERE id=:serverid', array('serverid' => $serverID));
                $serverDB = new PDORepository($server);
            } else {
                $_SESSION['server'] = NULL;
            }
        }
    }
}
 
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $data = [];
} else {
    if (empty($_POST)) {
        $REQUEST_BODY = file_get_contents('php://input');
        $data = json_decode($REQUEST_BODY, true);
    } else {
        $data = $_POST;
    }
} 
preg_match('!^.*/(.*?)/[^/]+$!', $_SERVER['SCRIPT_NAME'], $matches);
if (isset($matches[1])) {
    $resource = $matches[1];
    requireAccess('call', $resource); 
}

$get = $_GET;
?>