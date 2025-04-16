<?php
	ini_set('session.cookie_domain', '.permaviat.ru');
	session_start();
	include("./settings/connect_datebase.php");
	
	if (isset($_SESSION['token'])) {
		$token = $_SESSION['token'];
		$tokenParts = explode('.', $token);
		if (count($tokenParts) === 3) {
			// Декодируем полезную нагрузку (payload)
			$payloadDecoded = base64_decode($tokenParts[1]);
			$payload = json_decode($payloadDecoded);
			if ($payload) {
				// Извлекаем идентификатор пользователя и роль
				$userId = $payload->UserId;   // ID пользователя из токена
				$userRoll = $payload->roll;     // Роль пользователя из токена 
				
				// Редирект в зависимости от роли
				if ($userRoll == 0) {
					header("Location: user.php");
					exit;
				} else if ($userRoll == 1) {
					header("Location: admin.php");
					exit;
				}
			}
		}
	}
?>
<html>
	<head> 
		<meta charset="utf-8">
		<title> Авторизация </title>
		
		<script src="https://code.jquery.com/jquery-1.8.3.js"></script>
		<link rel="stylesheet" href="style.css">
	</head>
	<body>
		<div class="top-menu">
			<a href=#><img src = "img/logo1.png"/></a>
			<div class="name">
				<a href="index.php">
					<div class="subname">БЗОПАСНОСТЬ  ВЕБ-ПРИЛОЖЕНИЙ</div>
					Пермский авиационный техникум им. А. Д. Швецова
				</a>
			</div>
		</div>
		<div class="space"> </div>
		<div class="main">
			<div class="content">
				<div class = "login">
					<div class="name">Авторизация</div>
				
					<div class = "sub-name">Логин:</div>
					<input name="_login" type="text" placeholder="" onkeypress="return PressToEnter(event)"/>
					<div class = "sub-name">Пароль:</div>
					<input name="_password" type="password" placeholder="" onkeypress="return PressToEnter(event)"/>
					
					<a href="regin.php">Регистрация</a>
					<br><a href="recovery.php">Забыли пароль?</a>
					<input type="button" class="button" value="Войти" onclick="LogIn()"/>
					<img src = "img/loading.gif" class="loading"/>
				</div>
				
				<div class="footer">
					© КГАПОУ "Авиатехникум", 2020
					<a href=#>Конфиденциальность</a>
					<a href=#>Условия</a>
				</div>
			</div>
		</div>
		
		<script>
			function LogIn() {
				var loading = document.getElementsByClassName("loading")[0];
				var button = document.getElementsByClassName("button")[0];
				
				var _login = document.getElementsByName("_login")[0].value;
				var _password = document.getElementsByName("_password")[0].value;
				loading.style.display = "block";
				button.className = "button_diactive";
				
				var data = new FormData();
				data.append("login", _login);
				data.append("password", _password);
				
				// AJAX запрос
				$.ajax({
					url         : 'ajax/login_user.php',
					type        : 'POST', // важно!
					data        : data,
					cache       : false,
					dataType    : 'html',
					// отключаем обработку передаваемых данных, пусть передаются как есть
					processData : false,
					// отключаем установку заголовка типа запроса. Так jQuery скажет серверу что это строковой запрос
					contentType : false, 
					// функция успешного ответа сервера
					success: function (_data) {
						console.log("Авторизация прошла успешно, id: " +_data);
						if(_data == "") {
							loading.style.display = "none";
							button.className = "button";
							alert("Логин или пароль не верный.");
						} else {
							localStorage.setItem("token", _data);
							location.reload();
							loading.style.display = "none";
							button.className = "button";
						}
					},
					// функция ошибки
					error: function( ){
						console.log('Системная ошибка!');
						loading.style.display = "none";
						button.className = "button";
					}
				});
			}
			
			function PressToEnter(e) {
				if (e.keyCode == 13) {
					var _login = document.getElementsByName("_login")[0].value;
					var _password = document.getElementsByName("_password")[0].value;
					
					if(_password != "") {
						if(_login != "") {
							LogIn();
						}
					}
				}
			}
			
		</script>
	</body>
</html>