<?php
	session_start();
	include("./settings/connect_datebase.php");

	if (isset($_GET['error']) && $_GET['error'] === 'duplicate_session') {
        echo "<script>alert('Ваша сессия завершена, потому что вы авторизовались с другого места');</script>";
    }
	
	if (isset($_SESSION['user'])) {
		if($_SESSION['user'] != -1) {
			
			$user_query = $mysqli->query("SELECT * FROM `users` WHERE `id` = ".$_SESSION['user']);
			while($user_read = $user_query->fetch_row()) {
				if($user_read[3] == 0) header("Location: user.php");
				else if($user_read[3] == 1) header("Location: admin.php");
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
				
					<div class = "sub-name">Почта:</div>
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
				url: 'ajax/login_user.php',
				type: 'POST', // важно!
				data: data,
				cache: false,
				dataType: 'html',
				processData: false,
				contentType: false,
				success: function(_data) {
					console.log("Ответ сервера: " + _data);
					var trimmedData = $.trim(_data);
					if (trimmedData === "expired") {
						alert("Ваш пароль истёк. Пожалуйста, восстановите пароль.");
						window.location.href = "recovery.php?expired=1";
					} else if (trimmedData === "code_required") {
						var userCode = prompt("На вашу почту отправлен код авторизации. Введите полученный код:");
						if (userCode !== null && userCode !== "") {
							var data2 = new FormData();
							data2.append("code", userCode);
							$.ajax({
								url: 'ajax/verify_code.php',
								type: 'POST',
								data: data2,
								cache: false,
								dataType: 'html',
								processData: false,
								contentType: false,
								success: function(resp) {
									if ($.trim(resp) === "success") {
										localStorage.setItem("token", "success");
										location.reload();
									} else {
										alert("Неверный код авторизации.");
										loading.style.display = "none";
										button.className = "button";
									}
								},
								error: function() {
									alert("Ошибка при проверке кода.");
									loading.style.display = "none";
									button.className = "button";
								}
							});
						} else {
							alert("Вы не ввели код авторизации.");
							loading.style.display = "none";
							button.className = "button";
						}
					} else if (trimmedData === "") {
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
				error: function() {
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
				if (_login !== "" && _password !== "") {
					LogIn();
				}
			}
		}
		</script>
	</body>
</html>