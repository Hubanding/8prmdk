<?php
	ini_set('session.cookie_domain', '.permaviat.ru');
	session_start();
	// Получаем логин и пароль из POST-запроса
	$login    = isset($_POST['login']) ? $_POST['login'] : '';
	$password = isset($_POST['password']) ? $_POST['password'] : '';

	$authUrl = "http://auth.permaviat.ru/index.php";

	// Инициализируем cURL для запроса к серверу авторизации
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $authUrl);
	// Передаем логин и пароль через HTTP Basic Auth
	curl_setopt($ch, CURLOPT_USERPWD, "$login:$password");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, false);
	$response = curl_exec($ch);
	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);

	// Если авторизация успешна, сервер должен вернуть HTTP 200 и JSON с токеном
	if ($httpCode == 200) {
		$data = json_decode($response, true);
		if (isset($data['token']) && !empty($data['token'])) {
			// Сохраняем полученный токен в сессии
			$_SESSION['token'] = $data['token'];
			echo $data['token'];
			exit;
		}
	}

	// Если авторизация не удалась, возвращаем пустую строку
	echo "";
	exit;
?>