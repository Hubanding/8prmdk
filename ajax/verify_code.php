<?php
	session_start();
	include("../settings/connect_datebase.php");
	
	if(isset($_POST['code'])) {
	    $code = $_POST['code'];
	    if(isset($_SESSION['auth_code']) && isset($_SESSION['pending_user'])) {
	        if($code == $_SESSION['auth_code']) {
				$user_id = $_SESSION['pending_user'];
				$session_token = bin2hex(random_bytes(16));
				$_SESSION['user'] = $user_id;
				$_SESSION['user_token'] = $session_token;
				
				// Обновляем (или вставляем) запись в таблице user_sessions
				$stmt = $mysqli->prepare("REPLACE INTO user_sessions (user_id, session_token, last_update) VALUES (?, ?, NOW())");
				$stmt->bind_param("is", $user_id, $session_token);
				$stmt->execute();
				$stmt->close();
				
				// Удаляем временные переменные
				unset($_SESSION['pending_user']);
				unset($_SESSION['auth_code']);
				
				echo "success";
				exit;
	        } else {
	            echo "";
	            exit;
	        }
	    } else {
	        echo "";
	        exit;
	    }
	} else {
	    echo "";
	}
?>
