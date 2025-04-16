<?php
	session_start();
	include("./settings/connect_datebase.php");
	
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
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Регистрация</title>
    <script src="https://code.jquery.com/jquery-1.8.3.js"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="top-menu">
        <a href=#><img src="img/logo1.png" /></a>
        <div class="name">
            <a href="index.php">
                <div class="subname">БЕЗОПАСНОСТЬ ВЕБ-ПРИЛОЖЕНИЙ</div>
                Пермский авиационный техникум им. А. Д. Швецова
            </a>
        </div>
    </div>
    <div class="space"> </div>
    <div class="main">
        <div class="content">
        <div class="login">
            <div class="name">Регистрация</div>

            <div class="sub-name">Почта:</div>
            <input name="_login" type="text" placeholder="" onkeypress="return PressToEnter(event)" />

            <div class="sub-name">Пароль:</div>
            <input name="_password" type="password" placeholder="" onkeypress="return PressToEnter(event)" />

            <div class="sub-name">Повторите пароль:</div>
            <input name="_passwordCopy" type="password" placeholder="" onkeypress="return PressToEnter(event)" />

            <div id="password-errors" style="color: red;"></div>
            <a href="login.php">Вернуться</a>
            <input type="button" class="button" value="Зарегистрироваться" onclick="RegIn()" style="margin-top: 0px;" />
            <img src="img/loading.gif" class="loading" style="margin-top: 0px; display: none;" />
        </div>

            <div class="footer">
                © КГАПОУ "Авиатехникум", 2020
                <a href="#">Конфиденциальность</a>
                <a href="#">Условия</a>
            </div>
        </div>
    </div>

    <script>
        function showAlert(message) {
            alert(message);
        }

        function validatePassword(password) {
            let errors = [];
            if (password.length < 8) errors.push("Пароль должен содержать более 8 символов.");
            if (!/[A-Z]/.test(password)) errors.push("Пароль должен содержать хотя бы одну заглавную букву.");
            if (!/[a-z]/.test(password)) errors.push("Пароль должен содержать хотя бы одну строчную букву.");
            if (!/[0-9]/.test(password)) errors.push("Пароль должен содержать хотя бы одну цифру.");
            if (!/[!@#$%^&*(),.?":{}|<>]/.test(password)) errors.push("Пароль должен содержать хотя бы один специальный символ.");
            return errors;
        }

        function RegIn() {
    var _login = document.getElementsByName("_login")[0].value;
    var _password = document.getElementsByName("_password")[0].value;
    var _passwordCopy = document.getElementsByName("_passwordCopy")[0].value;
    var errorDiv = document.getElementById("password-errors");
    errorDiv.innerHTML = "";

    if (_login === "") {
        showAlert("Введите логин.");
        return;
    }

    if (_password === "") {
        showAlert("Введите пароль.");
        return;
    }

    if (_password !== _passwordCopy) {
        showAlert("Пароли не совпадают.");
        return;
    }

    let passwordErrors = validatePassword(_password);
    if (passwordErrors.length > 0) {
        showAlert(passwordErrors.join("\n"));
        return;
    }

    document.querySelector(".loading").style.display = "block";
    var data = new FormData();
    data.append("login", _login);
    data.append("password", _password);
    
    $.ajax({
        url: 'ajax/regin_user.php',
        type: 'POST',
        data: data,
        cache: false,
        processData: false,
        contentType: false,
        success: function (_data) {
            if (_data == -1) {
                showAlert("Пользователь с таким логином существует.");
            } else if (_data == 0) {
                showAlert("Ошибка регистрации. Попробуйте снова.");
            } else {
                showAlert("Регистрация успешна!");
                location.reload();
            }
            document.querySelector(".loading").style.display = "none";
        },
        error: function() {
            showAlert('Системная ошибка!');
            document.querySelector(".loading").style.display = "none";
        }
    });
}

    </script>
</body>
</html>

