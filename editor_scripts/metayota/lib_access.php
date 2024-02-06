<?php
namespace Metayota;
if (isset($_COOKIE['PHPSESSID'])) {//2
if(session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
}
require_once('lib_db.php');
$db = new PDORepository();
$loggedInUser = isset($_SESSION['loggedInUser']) ? $_SESSION['loggedInUser'] : null;
if ($loggedInUser == null) {//1
if (!empty($_COOKIE['token'])) {
    $rememberMe = $db->get('remember_me', array('token' => $_COOKIE['token']));
    if (!empty($rememberMe)) {
        $loggedInUser = $db->get('user', array('id' => $rememberMe['user_id']));
        if (!empty($loggedInUser)) {
            if (!isset($_COOKIE['PHPSESSID'])) {
                session_start();
            }
            $_SESSION['loggedInUser'] = $loggedInUser;
        }
    }
}
/*
if ($loggedInUser == null) {
    $result = $db->get('SHOW TABLES LIKE "user"');
    if (empty($result)) {
        $GLOBALS['loggedInUser'] = array('id' => 1, 'username'=>'infinity','usergroup_id'=>3);
    }
}*/
}

if (getConfig('wordpress')) {
    if ( is_user_logged_in() ) {
        $current_user = wp_get_current_user();
        $loggedInUser = [];
        $loggedInUser['is_admin'] = in_array( 'administrator', (array) $current_user->roles );
        $loggedInUser['username'] = $current_user->user_login;
        $loggedInUser['firstname'] = $current_user->user_firstname;
        $loggedInUser['usergroup_id'] = [];
        foreach ( $current_user->roles as $role_slug ) {
            $usergroup = $db->getByName('usergroup', $role_slug);
            $loggedInUser['usergroup_id'][] = $usergroup['id'];
        }
        $_SESSION['loggedInUser'] = $loggedInUser;
    }
} else {
    if (!empty($_SESSION['loggedInUser'])) {
        $_SESSION['loggedInUser']['is_admin'] = $_SESSION['loggedInUser']['usergroup_id'] == 3;
        $loggedInUser = $_SESSION['loggedInUser'];
    }
}

session_write_close();

$GLOBALS['loggedInUser'] = $loggedInUser;

function hasAccess($right, $resource = null, $field = null)
{
    global $db;
    if (isset($GLOBALS['loggedInUser'])) {
        $usergroups = $GLOBALS['loggedInUser']['usergroup_id'];
        if (!is_array($usergroups)) {
            $usergroups = [$usergroups];
        }
        if (isset($GLOBALS['loggedInUser']['is_admin']) && $GLOBALS['loggedInUser']['is_admin'] == true) {
            return true;
        }
    } else {
        $usergroups = [1];
    }
    
    foreach($usergroups as $usergroup_id) {
        $where = array('access_right' => $right, 'usergroup_id' => $usergroup_id);
        if ($resource != null) {
            $where['resource_name']  = $resource;
        }
        if ($field != null) {
            $where['resource_field'] = $field;
        }
        
        $result = $db->get('access', $where);
        if (!empty($result)) {
            return true;
        }
    }
    return false;
}

function requireAccess($right, $resource = null, $field = null)
{
    if (!hasAccess($right, $resource, $field)) {
        header('HTTP/1.0 403 Forbidden');
        
        $msg = ("You have no access to this page. You need the following rights: $right, resoure_name:$resource, field:$field. ");
        echo json_encode(array("error" => $msg));
        die();
    }
}
?>