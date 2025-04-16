<?php
	session_start();
	include("../settings/connect_datebase.php");

	require '../vendor/autoload.php';
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;
	 
	$login = $_POST['login'];
	$password = $_POST['password'];
	
	// ищем пользователя
	$query_user = $mysqli->query("SELECT * FROM `users` WHERE `login` = '".$login."'");
	$id = -1;

	if ($user_read = $query_user->fetch_row()) {
	    if (password_verify($password, $user_read[2])) {
	        $id = $user_read[0];
	    }
	}
	
	if($id != -1) {

		$query_exp = $mysqli->query("SELECT last_change FROM password_expiration WHERE user_id = '".$id."'");
			if($row = $query_exp->fetch_assoc()){
				$last_change = strtotime($row['last_change']);
				if(time() - $last_change >= 86400){ // 86400 секунд = 1 день
					echo "expired";
					exit;
				}
			}

		$code = rand(100000, 999999);
	    $_SESSION['auth_code'] = $code;
	    $_SESSION['pending_user'] = $id;
	    
	    $mail = new PHPMailer(true);
	    try {
	        $mail->isSMTP();
	        $mail->Host       = 'smtp.yandex.ru';
	        $mail->SMTPAuth   = true;
	        $mail->Username   = 'didhfjsiaplxxkak@yandex.ru'; 
	        $mail->Password   = 'iqdxbbzyzzblorrh';
	        $mail->SMTPSecure = 'ssl';  
	        $mail->Port       = 465;

			$mail->CharSet = 'UTF-8';
			$mail->Encoding = 'base64';
	        
	        $mail->setFrom('didhfjsiaplxxkak@yandex.ru', 'Админ');
	        $mail->addAddress($login);
	        
	        $mail->isHTML(true);
	        $mail->Subject = 'Код авторизации';
	        $mail->Body    = 'Ваш код авторизации: <b>' . $code . '</b>';
	        $mail->AltBody = 'Ваш код авторизации: ' . $code;
	        
	        $mail->send();
	        
	        
	        echo "code_required";
	    } catch (Exception $e) {
	        echo "";
	    }
	} else {
	    echo "";
	}
	
	
?>