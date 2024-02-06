<?php namespace Metayota; include_once('../metayota/library.php');   include_once('../translation-service/translation-service.php');  
    if (session_status() != PHP_SESSION_ACTIVE) {
        session_start();
    }

    $username =$data['username'];
    if (!empty($username)) {
        $res = $db->fetchQuery("SELECT * FROM user WHERE (username=:username OR email=:username) AND status='active'",array('username' => $username));
    } 
    

    if (password_verify($data['password'],$res['password_hash'])) {
        $result = $res;
    } else {
        $salt = $res['password_salt'];
        $hash = md5($salt.$data['password'].$salt);
        $result = $db->fetchQuery("select * from user where username=:username and password_hash=:hash and status='waiting'",array('username'=>$res['username'],'hash'=>$hash));
        if (!empty($result)) {
            errorMsg("Please check your email and click on the link to verify your email address!");
        }
        $data['remember_me'] = true;
        $result = $db->fetchQuery("select * from user where username=:username and password_hash=:hash and status='active'",array('username'=>$res['username'],'hash'=>$hash));
    }
	

    $user_id = $result['id'];
    
    if (!empty($user_id) && $user_id != null) {
        if (!empty($data['remember_me'])) {
            $token = substr(bin2hex(random_bytes(64)),0,64);
            $lastLogin = date("Y-m-d H:i:s");
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
            $db->insert('remember_me',array('user_id'=>$user_id,'token'=>$token,'last_login'=>$lastLogin,'user_agent'=>$userAgent));
            $expires30days = time() + 3600 * 24  * 30;
            setcookie ('token', $token, $expires30days, '/', '', false, true );
        }
        $_SESSION['loggedInUser'] = $result;
        echo json_encode($_SESSION['loggedInUser']);
    } else {

        $result = $db->fetchQuery("select * from user where username=:username and password_hash=:hash",array('username'=>$res['username'],'hash'=>$hash));
        if (empty($result['id'])) {
            errorMsg("Your login data is not correct.");
        } else {
            errorMsg("Your login data is corrent. Please check your email and click on the link to verify your email address!");
        }
    }
?>