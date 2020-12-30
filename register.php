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
		註冊頁面
		</title>
		<link rel="stylesheet" type="text/css" href="main.css">
	</head>
	<body>
		<form name="register" action="register.php" method="post">
			<p>新使用者名稱<input type="text" name="newusername"></p>
			<p>新密碼<input type="password" name="newpassword"></p>
			<p>確認密碼<input type="password" name="repassword"></p>
			<p><input type="submit" name="newregister" value="註冊帳戶"></p>	
		</form>
		<form name="register" action="login.php" method="post">
			<p><input type="submit" name="gologin" value="返回至登入介面"></p>
		</form>
	</body>
</html>			
<?php
	function timeout()
	{
		echo "
		<script>
		setTimeout(function(){window.location.href='register.php';},1000);
		</script>";
	}			
	
	if(empty($_POST['newusername'])&&empty($_POST['newpassword'])&&empty($_POST['repassword']))
	{
		exit();
	}
	elseif(empty($_POST['newusername'])||empty($_POST['newpassword'])||empty($_POST['repassword']))
	{	
		timeout();
		exit("請填寫完整!!");
	}

	$newusername = $_POST['newusername'];
	$newpassword = $_POST['newpassword'];
	$repassword  = $_POST['repassword'];

	if($newpassword != $repassword)
	{
		timeout();
		exit("密碼不一致!!");	
	}
	include("userdata.php");
	$sql = $pw->query("SELECT*FROM username WHERE username='$newusername'");
	
	if(mysqli_num_rows($sql) != null)
	{
		timeout();
		$pw->close();
		exit("使用者已經存在!!");
	}
	
	$hash = password_hash($newpassword, PASSWORD_DEFAULT);
	try
	{
		$sql = $pw->prepare("INSERT INTO username (username,password,level) VALUES ('$newusername','$hash','2')");//一般帳戶權限
		$sql->execute();
	}
	catch (PDOException $e)
	{
		die('創建失敗...'. $e->getMessage());
	}
	echo "創建成功!!";
	echo "
	<script>
	setTimeout(function(){window.location.href='login.php';},1000);
	</script>";
	$pw->close();
	/*
	CREATE DATABASE password CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
	use password;
	create table username(
		id int(10) not null auto_increment,
		username varchar(30),
		password varchar(255),
		level varchar(1),
		primary key(id));
	*/
?>