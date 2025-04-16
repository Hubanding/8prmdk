<?php
	session_start();
	include("./settings/connect_datebase.php");
	
	if (isset($_SESSION['user'])) {
		if($_SESSION['user'] != -1) {
			$user_query = $mysqli->query("SELECT * FROM `users` WHERE `id` = " . $_SESSION['user']);
			while($user_read = $user_query->fetch_row()) {
				if($user_read[3] == 0) {
					header("Location: index.php");
					exit();
				}
			}
		} else {
			header("Location: login.php");
			exit();
		}
 	} else {
		header("Location: login.php");
		echo "Пользователя не существует";
		exit();
	}

	include("./settings/session.php");
?>
<!DOCTYPE html>
<html>
	<head> 
		<meta charset="utf-8">
		<title>Admin панель</title>
		
		<link rel="stylesheet" href="style.css">
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.css">
		<script src="https://code.jquery.com/jquery-1.8.3.js"></script>
		<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
		<style>
			table { width: 100%; border-collapse: collapse; }
			th, td { padding: 10px; border: 1px solid #ddd; text-align: center; }
			#chartContainer { width: 80%; margin: 20px auto; }
		</style>
	</head>
	<body>
		<div class="top-menu">
			<a href="#"><img src="img/logo1.png" alt="logo" /></a>
			<div class="name">
				<a href="index.php">
					<div class="subname">БЕЗОПАСНОСТЬ ВЕБ-ПРИЛОЖЕНИЙ</div>
					Пермский авиационный техникум им. А. Д. Швецова
					<input type="button" class="button" value="Выйти" onclick="logout()" style="width: 200px; margin-top:-20px;"/>
				</a>
			</div>
		</div>
		<div class="space"></div>
		<div class="main">
			<div class="content">

				
				<div class="name">Журнал событий</div>
				
			
				<table id="logsTable">
					<thead>
						<tr>
							<td style="width: 160px">Дата и время</td>
							<td style="width: 160px">IP пользователя</td>
							<td style="width: 160px">Время в сети</td>
							<td style="width: 160px">Статус</td>
							<td>Событие</td>
						</tr>
					</thead>
					<tbody>
					</tbody>
					
				</table>
				
				
				<div class="footer">
					© КГАПОУ "Авиатехникум", 2020
					<a href="#">Конфиденциальность</a>
					<a href="#">Условия</a>
				</div>
			</div>
		</div>
		
		<script>
			function timeStringToSeconds(timeStr) {
				var parts = timeStr.split(':');
				if(parts.length !== 3) return 0;
				return (+parts[0]) * 3600 + (+parts[1]) * 60 + (+parts[2]);
			}
			
			function GetEvents() {
				$.ajax({
					url         : 'ajax/events/get.php',
					type        : 'POST',
					dataType    : 'json',
					success: function(data) {
						var tbody = $("#logsTable tbody");
						tbody.empty();
						$.each(data, function(i, event) {
							var row = "<tr>" +
								"<td>" + event.Date + "</td>" +
								"<td>" + event.Ip + "</td>" +
								"<td>" + event.TimeOnline + "</td>" +
								"<td>" + event.Status + "</td>" +
								"<td style='text-align:left'>" + event.Event + "</td>" +
								"</tr>";
							tbody.append(row);
						});
						
						if ($.fn.DataTable.isDataTable('#logsTable')) {
							$('#logsTable').DataTable().destroy();
						}
						
						var table = $('#logsTable').DataTable({
							"order": [[ 1, "desc" ]],  
							"paging": false,           
							"dom": 't'
						});
						
						$('#logsTable tfoot th').each(function(index){
							if(index === 0) {
								$(this).html('');
							} 
						});
						
						table.columns().every(function(index){
							if(index === 0) return; 
							var that = this;
							$('input', this.footer()).on('keyup change', function(){
								if (that.search() !== this.value) {
									that.search(this.value).draw();
								}
							});
						});
						
						
					},
					error: function() {
						console.log("Ошибка загрузки данных");
					}
				});
			}
			
			
			
			$(document).ready(function() {
				GetEvents();
			});
			
			function logout() {
				window.location.href = "ajax/logout.php";
			}
			
			
		</script>
	</body>
</html>
