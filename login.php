<?php 
session_start();
if(!empty($_SESSION['guest'])||!empty($_SESSION['user'])||!empty($_SESSION['robot'])||!empty($_SESSION['admin'])||!empty($_SESSION['superadmin']))
{
	echo "
			<script>
			setTimeout(function(){window.location.href='index.php';},1000);
			</script>";
	exit('請先登出');
}
?>
<!DOCTYPE html>
<html lang="zh-Hant-TW">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta name="description" content="學號或姓名查詢系統">
		<meta name="author" content="張尊堯">
		<title>
		登入系統
		</title>
		<link rel="stylesheet" type="text/css" href="main.css">
	</head>
	<body>
		<form name="login" action="login.php" method="post">
			<p>使用者名稱<input type="text" name="username"></p>
			<p>密碼<input type="password" name="password"></p>
			<p><input type="submit" name="submit" value="登入">
			   <input type="submit" name="guest" value="訪客的身分"></p>
		</form>
		<form name="register" action="register.php" method="post">
			<input type="submit" name="register" value="註冊">
		</form>
		<br>
		<?php
		if(!empty($_POST['guest']))
		{
			$_SESSION['guest']  = true;
			header("refresh:0;url=index.php");
		}

		function timeout()
		{
			echo "
			<script>
			setTimeout(function(){window.location.href='login.php';},1000);
			</script>";
		}

		if(empty($_POST['username']) && empty($_POST['password']))
		{
			exit('請輸入帳號');
		}

		if(!empty($_POST['username'])||!empty($_POST['password']))
		{
			include("userdata.php");

			$username = $_POST['username'];//安全性疑慮
			$password = $_POST['password'];//安全性疑慮
			$sql 	  = "SELECT username, password, level FROM username";
			$result   = $pw->query($sql);

			$_SESSION['superadmin'] = false;
			$_SESSION['admin'] 		= false;
			$_SESSION['robot'] 		= false;
			$_SESSION['user'] 		= false;
			//$_SESSION['guest']	 	= false;

			while($row = $result->fetch_assoc())
			{
				if($username === $row["username"])
				{
					if(password_verify($password, $row["password"]))
					{
						switch ($row["level"])
						{
							case "5":
								$_SESSION['superadmin'] = true;
								break;
							case "4":
								$_SESSION['admin'] = true;
								break;							
							case "3":
								$_SESSION['robot'] = true;
								break;
							case "2":
								$_SESSION['user']  = true;
								break;
						}
						$pw->close();
						$OK = true;
						header("refresh:0;url=index.php");
						break;
					}
				}
			}
			if(empty($OK)) 
			{
				timeout();
				echo "帳號或密碼錯誤!";
			}
		}
		?>
	</body>
</html>