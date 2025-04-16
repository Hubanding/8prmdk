<?php
	session_start();
	include("../settings/connect_datebase.php");
	require '../vendor/autoload.php';
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;
	
	$login = $_POST['login'];
	$password = $_POST['password'];

	if (
		strlen($password) <= 8 ||
		!preg_match('/[a-zA-Z]/', $password) ||
		!preg_match('/[^a-zA-Z0-9]/', $password) ||
		!preg_match('/\d/', $password) ||
		!preg_match('/[A-Z]/', $password)
	) {
		echo -2;
		exit;
	}
	
	// ищем пользователя
	$query_user = $mysqli->query("SELECT * FROM `users` WHERE `login`='".$login."'");
	$id = -1;
	
	if($user_read = $query_user->fetch_row()) {
		echo $id;
	} else {
		$encrypted = password_hash($password, PASSWORD_DEFAULT);
		$mysqli->query("INSERT INTO `users`(`login`, `password`, `roll`) VALUES ('".$login."', '".$encrypted."', 0)");
		
		$query_user = $mysqli->query("SELECT * FROM `users` WHERE `login`='".$login."'");
		$user_new = $query_user->fetch_row();
		if(password_verify($password, $user_new[2])) {
			$id = $user_new[0];
		} else {
			$id = -1;
		}
			
		if ($id != -1) {
            $_SESSION['pending_user'] = $id;
            $code = rand(100000, 999999);
            $_SESSION['auth_code'] = $code;
            
            
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.yandex.ru';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'ivanlekonts@yandex.ru'; 
                $mail->Password   = '86594273d';
                $mail->SMTPSecure = 'ssl';  
                $mail->Port       = 465;
                
                $mail->CharSet = 'UTF-8';
                $mail->Encoding = 'base64';
                
                $mail->setFrom('ivanlekonts@yandex.ru', 'Админ');
                $mail->addAddress($login);
                
                $mail->isHTML(true);
                $mail->Subject = 'Код авторизации для регистрации';
                $mail->Body    = 'Ваш код авторизации: <b>' . $code . '</b>';
                $mail->AltBody = 'Ваш код авторизации: ' . $code;
                
                $mail->send();

				$mysqli->query("INSERT INTO password_expiration (user_id, last_change) VALUES ('".$id."', NOW())");
                
                echo "code_required";
            } catch (Exception $e) {
                echo "";
            }
        } else {
            echo "";
        }
    }
?>