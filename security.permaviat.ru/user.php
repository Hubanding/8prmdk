<?php
	ini_set('session.cookie_domain', '.permaviat.ru');
	session_start();

	include("./settings/connect_datebase.php");

	// 1. Проверяем, есть ли JWT-токен в сессии
	if (!isset($_SESSION['token'])) {
		header("Location: login.php");
		exit;
	}

	// 2. Разбиваем токен на части
	$tokenParts = explode('.', $_SESSION['token']);
	if (count($tokenParts) !== 3) {
		header("Location: login.php");
		exit;
	}

	// 3. Декодируем payload
	$payloadDecoded = base64_decode($tokenParts[1]);
	$payload = json_decode($payloadDecoded);
	if (!$payload) {
		header("Location: login.php");
		exit;
	}

	// 4. Проверяем роль (roll) — должна быть 0, иначе это не «пользователь»
	if (!isset($payload->roll) || $payload->roll != 0) {
		// Если роль не 0, отправляем на login
		header("Location: login.php");
		exit;
	}

	// 5. Извлекаем ID пользователя
	$userId = $payload->UserId;

	$userQuery = $mysqli->query("SELECT * FROM `users` WHERE `id` = $userId");
	$userData  = $userQuery->fetch_assoc();
	if (!$userData) {
		// Если пользователь с таким ID не найден, выходим
		header("Location: login.php");
		exit;
}
?>
<!DOCTYPE HTML>
<html>
	<head> 
		<script src="https://code.jquery.com/jquery-1.8.3.js"></script>
		<meta charset="utf-8">
		<title> Личный кабинет </title>
		
		<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
		<link rel="stylesheet" href="style.css">
	</head>
	<body>
		<div class="top-menu">
			<a href=# class = "singin"><img src = "img/ic-login.png"/></a>
		
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
				<div class="name" style="padding-bottom: 0px;">Личный кабинет</div>
				<div class="description">
					Добро пожаловать: <?php echo htmlspecialchars($userData['login']); ?><br>
					Ваш идентификатор: <?php echo htmlspecialchars($userData['id']); ?>
				</div>
			
				<div class="footer">
					© КГАПОУ "Авиатехникум", 2020
					<a href=#>Конфиденциальность</a>
					<a href=#>Условия</a>
				</div>
			</div>
		</div>
		
		<script>
			var id_statement = -1;
			function DeleteStatementt(id_statement) {
				if(id_statement != -1) {
					
					var data = new FormData();
					data.append("id_statement", id_statement);
					
					// AJAX запрос
					$.ajax({
						url         : 'ajax/delete_statement.php',
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
							console.log(_data);
							location.reload();
						},
						// функция ошибки
						error: function(){
							console.log('Системная ошибка!');
						}
					});
				}
			}
			
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