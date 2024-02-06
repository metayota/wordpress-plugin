<?php namespace Metayota; include_once('../metayota/library.php');   include_once('../sync-functions/sync-functions.php');   include_once('../translation-service/translation-service.php');  
    if (session_status() != PHP_SESSION_ACTIVE) {
        session_start();
    }
    $task_id = $data['task_id'];

    function getTask($task_id) {
        global $db;
        $skillsJoin = " LEFT OUTER JOIN skills ON task.skill_id = skills.id LEFT OUTER JOIN translation t ON t.translation_key = skills.skill_name AND t.language=:language";
        $baseQuery = "SELECT task.*, task_state.name as status, task_state.actions as actions, task_state.progress as progress, t.translation as skill_name FROM `task` LEFT OUTER JOIN task_state ON task_state.id = task.task_state ".$skillsJoin;
        $where = array();

        $where[] = "task.id =:task_id";

        $sqlQuery = $baseQuery.' WHERE '.implode(' AND ',$where);
        return $db->fetchOneWithParameters($sqlQuery, array('task_id'=>$task_id,'language'=>$GLOBALS['language']));
    }

    function getRCSpent($task_id) {
        global $db;
        $result = $db->fetchOneWithParameters('SELECT SUM(compensation) as spent FROM `task_work` WHERE task_id=:task_id',array('task_id'=>$task_id));
        return $result['spent']*1;
    }

    $task = getTask($task_id);

    $action = $data['action'];
    $user = $_SESSION['loggedInUser'];

    if ($action == 'submit_for_verification') {
        $id = $db->getId('task_state','waiting_for_verification');
        $db->update('task',array('task_state'=>$id),array('id'=>$task_id));
    }

    if ($action == 'take_for_verification') {
        $id = $db->getId('task_state','verification');
        $db->update('task',array('task_state'=>$id,'assigned_to_user' => $user_id),array('id'=>$task_id));
    }

    if ($action == 'set_valid') {
        $verificationTaskStateId = $db->getId('task_state','verification');
        if ($task['task_state'] == $verificationTaskStateId) {
            $id = $db->getId('task_state','search_worker');
            $db->update('task',array('task_state'=>$id, 'assigned_to_user' => 0),array('id'=>$task_id));
            $db->insert('task_work',array('action' => 'set_valid', 'worker_user_id' => $user_id,'compensation' => 0.02,'task_id' => $task_id,'task_state_id' => $verificationTaskStateId, 'new_task_state_id' => $id));
        }
    }

    if ($action == 'verification_reject') {
        $verificationTaskStateId = $db->getId('task_state','verification');
        if ($task['task_state'] == $verificationTaskStateId) {
            $id = $db->getId('task_state','draft');
            $db->update('task',array('task_state'=>$id, 'assigned_to_user'=>$task['owner_user_id']),array('id'=>$task_id));
            $db->insert('task_work',array('action' => 'set_invalid', 'comment' => $data['comment'], 'worker_user_id' => $user_id,'compensation' => 1,'task_id' => $task_id,'task_state_id' => $verificationTaskStateId, 'new_task_state_id' => $id));
        }
    }

    if ($action == 'take_task') {
        $id = $db->getId('task_state','work_in_progress');
        $db->update('task',array('task_state'=>$id,'worker_id'=>$user_id,'assigned_to_user'=>$user_id),array('id'=>$task_id));
    }

    if ($action == 'cancel_work') {
        $workInProgressState = $db->getId('task_state','work_in_progress');
        if ($task['task_state'] == $workInProgressState) {
            $id = $db->getId('task_state','search_worker');
            // DELETE
            $db->update('task',array('task_state'=>$id,'assigned_to_user'=>0),array('id'=>$task_id));
            unset($_SESSION['current_task']);
        }
    }

    if ($action == 'cancel_task') {
        if ($task['owner_user_id'] == $user_id) {
            $id = $db->getId('task_state','cancelled');
            $db->update('task',array('task_state'=>$id,'assigned_to_user'=>0,'worker_id'=>0),array('id'=>$task_id));
        }
    }

    if ($action == 'back_to_draft') {
        if ($task['owner_user_id'] == $user_id) {
            $id = $db->getId('task_state','draft');
            $db->update('task',array('task_state'=>$id,'assigned_to_user'=>0,'worker_id'=>0),array('id'=>$task_id));
        }
    }

    if ($action == 'submit_for_review') {
        $workServer = $db->update( 'server',['user_id'=>NULL], ['task_id' => $task_id]);

        $workInProgressState = $db->getId('task_state','work_in_progress');
        if ($task['task_state'] == $workInProgressState) {
            $id = $db->getId('task_state','waiting_for_review');
            $db->update('task',array('task_state'=>$id,'assigned_to_user'=>0),array('id'=>$task_id));
        }
    }

    if ($action == 'take_for_review') {
        $id = $db->getId('task_state','in_review');
        if ($task['worker_id'] == $user_id) {
            msg('msg_cannot_review_own_work');
        }
        $db->update('task',array('task_state'=>$id,'assigned_to_user'=>$user_id),array('id'=>$task_id));
    }

    if ($action == 'reject_work') {
        $id = $db->getId('task_state','work_in_progress');
        $inReviewState = $db->getId('task_state','in_review');
        if ($task['task_state'] != $inReviewState) {
            errorMsg('The task has to be in review to reject it.');
        }
        if ($task['assigned_to_user'] != $user_id) {
            errorMsg('The task is in review by another user.');
        }
        $db->update('task',array('task_state'=>$id,'assigned_to_user'=>$task['worker_id']),array('id'=>$task_id));
        $reviewerCompensation = round($task['max_price'] * 0.05);
        $db->update('server',['user_id'=>$task['worker_id']],['task_id'=>$task_id]);
        $db->insert('task_work',array('action' => 'review_rejected','comment' => $data['comment'], 'rating' => $data['rating'], 'worker_user_id' => $user_id,'compensation' => $reviewerCompensation,'task_id' => $task_id,'task_state_id' => $inReviewState, 'new_task_state_id' => $id));
    }

    if ($action == 'review_successful') {
        $inReviewState = $db->getId('task_state','in_review');
        $waitingForReviewState = $db->getId('task_state','waiting_for_review');
        $workInProgressState = $db->getId('task_state','work_in_progress');
        $id = $db->getId('task_state','success');
        $db->update('task',array('task_state'=>$id, 'assigned_to_user' => $task['owner_user_id']),array('id'=>$task_id));
       // $spent = getRCSpent($task_id);
        $compensation = $task['max_price'];// - $spent - ($task['max_price'] / 3);
        $workerCompensation = round($compensation * 0.45);
        $reviewerCompensation = round($compensation * 0.05);
        $db->insert('task_work',array('action' => 'take_task', 'comment' => $data['comment'], 'rating' => $data['rating'], 'compensation' => $workerCompensation, 'worker_user_id' => $user_id,'task_id' => $data['task_id'],'task_state_id' => $workInProgressState, 'new_task_state_id' => $waitingForReviewState));
        $db->insert('task_work',array('action' => 'review_successful', 'worker_user_id' => $user_id,'compensation' => $reviewerCompensation,'task_id' => $task_id,'task_state_id' => $inReviewState, 'new_task_state_id' => $id));
    }

    if ($action == 'integrate_change') {

        if ($user_id != $task['owner_user_id']) {
            errorMsg('The owner of the webspace and creator of the task must integrate this change!');
        }


        $id = $db->getId('task_state','finished');
        $db->update('task',array('task_state'=>$id, 'assigned_to_user' => 0),array('id'=>$task_id));


        $task = $db->getById('task', $task_id);

        $workServer = $db->get( 'server', array( 'task_id' => $task_id));
        $mainServer = $db->getById('server', $task['server_id']);

        //$mainServerLogin = array('username' => $mainServer['username'], 'password' => $mainServer['password'], 'host' => $mainServer['host'], 'db' => $mainServer['db']);
        $mainServerDB = new PDORepository($mainServer);

        //$workServerLogin = array('username' => $workServer['username'], 'password' => $workServer['password'], 'host' => $workServer['host'], 'db' => $workServer['db']);
        $workServerDB = new PDORepository($workServer);

        $checkChanges = array('title','type','vendor','name','parameters','allowed_subelements','documentation','implementation','dependencies','version','license','data','configuration','visibility');
        $dependencies = getResourceDependencies($task['resource'], $workServerDB);
        $changedResources = array();
        foreach( $dependencies as $dependency ) {
            $resourceName = $dependency['name'];
            $workResource = $workServerDB->getByName('resource', $dependency['name']);
            $mainResource = $mainServerDB->getByName('resource', $dependency['name']);

            foreach($checkChanges as $checkChange) {
                if (empty($mainResource)) {
                    unset($workResource['id']);
                    $mainServerDB->insert('resource', $workResource);
                    break;
                } else if ($workResource[$checkChange] != $mainResource[$checkChange]) {
                    
                    $originalResourceID = $mainResource['id'];
                    unset($mainResource['id']);
                    $mainResource['version_comment'] = 'Before Task #'.$task_id;
                    $mainServerDB->insert('resource_archive',$mainResource); 
                    $mainServerDB->deleteById('resource', $originalResourceID);
                    unset($workResource['id']);
                    $mainServerDB->insert('resource', $workResource);
                    break;
                }
            }
        }
        

        $server = $db->get('server',array('task_id'=>$task_id));
        if (!empty($server)) {
            $db->query("DELETE FROM server WHERE id=:id",array('id'=>$server['id']));
            $account = $server['username'];
            if (substr($account,0,3) != 'web') {
                die('Delete account not possible!');
            }
            $hoster_https = $server['hoster_https'];
            file_get_contents($hoster_https.'call/rc.account.delete/delete_files?api_key=3583LAZRI3HAFK376RZIWA&account='.$account);
            //success();
        }
    }

   // callAction('metayota','export_app');
    
    if ($data['task_id']) {
        $task = getTask($task_id);
        
        echo json_encode($task);    
    }

    
    
?>