<?php
	session_start();
	include("../settings/connect_datebase.php");
	require '../vendor/autoload.php';
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;
	
	$login = $_POST['login'];
	
	// ищем пользователя
	$query_user = $mysqli->query("SELECT * FROM `users` WHERE `login`='".$login."';");
	 
	$id = -1;
	if($user_read = $query_user->fetch_row()) {
		// создаём новый пароль
		$id = $user_read[0];
	}
	
	function PasswordGeneration() {
		// Матрица символов: латинские строчные, заглавные, цифры и специальные символы
		$chars = "qazxswedcvfrtgbnhyujmkiolp1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP!@#$%^&*()";
		$length = 10; // можно задать длину >8
		$password = "";
		$max = strlen($chars) - 1;
		for ($i = 0; $i < $length; $i++) {
			$password .= $chars[rand(0, $max)];
		}
		// Проверяем, удовлетворяет ли пароль критериям:
		if (!preg_match('/[A-Z]/', $password) || !preg_match('/\d/', $password) || !preg_match('/[^a-zA-Z0-9]/', $password)) {
			// Если не удовлетворяет, генерируем новый
			return PasswordGeneration();
		}
		return $password;
	}
	
	if($id != -1) {
		// Генерируем новый пароль
		$new_password = PasswordGeneration();
		
		// Зашифровываем новый пароль с использованием password_hash()
		$encrypted = password_hash($new_password, PASSWORD_DEFAULT);
		
		// Обновляем пароль в базе данных с зашифрованным значением
		$mysqli->query("UPDATE `users` SET `password`='".$encrypted."' WHERE `login` = '".$login."'");
		
		// Обновляем дату изменения пароля
		$mysqli->query("REPLACE INTO password_expiration (user_id, last_change) VALUES ('".$id."', NOW())");
		
		// Отсылаем письмо с новым паролем
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
			$mail->Subject = 'Новый пароль';
			$mail->Body    = 'Ваш пароль был изменён. Новый пароль: <b>' . $new_password . '</b>';
			$mail->AltBody = 'Ваш пароль был изменён. Новый пароль: ' . $new_password;
			
			$mail->send();
		} catch (Exception $e) {
			// Если отправка письма не удалась, можно выполнить дополнительное действие
		}
	}
	
	echo $id;
?>