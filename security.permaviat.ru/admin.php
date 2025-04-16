<?php
	ini_set('session.cookie_domain', '.permaviat.ru');
	session_start();
	
	// Проверяем наличие JWT-токена в сессии
	if (!isset($_SESSION['token'])) {
		header("Location: login.php");
		exit;
	}
	
	$token = $_SESSION['token'];
	$tokenParts = explode('.', $token);
	if (count($tokenParts) !== 3) {
		header("Location: login.php");
		exit;
	}
	
	// Декодируем полезную нагрузку (payload) из токена
	$payloadDecoded = base64_decode($tokenParts[1]);
	$payload = json_decode($payloadDecoded);
	if (!$payload) {
		header("Location: login.php");
		exit;
	}
	
	// Проверяем, что роль (roll) пользователя равна 1 (администратор)
	// Если роль не равна 1 или поле отсутствует, перенаправляем на login.php
	if (!isset($payload->roll) || $payload->roll != 1) {
		header("Location: login.php");
		exit;
	}
?>
<!DOCTYPE HTML>
<html>
	<head> 
		<script src="https://code.jquery.com/jquery-1.8.3.js"></script>
		<meta charset="utf-8">
		<title> Admin панель </title>
		
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
				<input type="button" class="button" value="Выйти" onclick="logout()"/>
				
				<div class="name">Административная панель</div>
			
				Административная панель служит для создания, редактирования и удаления записей на сайте.
			
				<div class="footer">
					© КГАПОУ "Авиатехникум", 2020
					<a href=#>Конфиденциальность</a>
					<a href=#>Условия</a>
				</div>
			</div>
		</div>
		
		<script>
			function logout() {
				$.ajax({
					url         : 'ajax/logout.php',
					type        : 'POST', // важно!
					data        : null,
					cache       : false,
					dataType    : 'html',
					processData : false,
					contentType : false, 
					success: function (_data) {
						location.reload();
					},
					error: function( ){
						console.log('Системная ошибка!');
					}
				});
			}
		</script>
	</body>
</html>