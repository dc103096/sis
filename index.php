<?php 
session_start();
if(!empty($_SESSION['guest'])||!empty($_SESSION['user'])||!empty($_SESSION['robot'])||!empty($_SESSION['admin'])||!empty($_SESSION['superadmin']))
	include("userdata.php");
else
{
	echo "
	<script>
	setTimeout(function(){window.location.href='login.php';},1000);
	</script>";
	exit('請先登入!');
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
		查詢介面
		</title>
		<link rel="stylesheet" type="text/css" href="main.css">
	</head>
	<body>
		<form method="post" action="index.php">學號或姓名查詢<br>
			<input name="student" required maxlength="10"  ><br>
			<input type="submit" id="myButton" name="x" value="送出">
		</form><br>
		<form method="post" action="index.php" >
			<input type="submit" name="allstudents" value="學生訊息名單">
			<input type="submit" name="clean" value='清空顯示'>
			<?php
				if(!empty($_SESSION['superadmin'])||!empty($_SESSION['admin'])||!empty($_SESSION['robot']))
					echo '<input type="submit" name="op" value="新增學生">';
			?>
			<br>
		</form>
			<?php
			if(!empty($_SESSION['superadmin'])||!empty($_SESSION['admin']))
			{
				echo '<br><button onclick="del()">清空所有學生記錄</button>'.
				'<form method="post" action="index.php" enctype="multipart/form-data" id="csvReadWrite">'.
				'<input type="file" name="csvToUpload" id="csvToUpload">'.
				'<input type="submit" name="loadfromcsv" value="上傳本地CSV寫入服務器數據庫">'.
				'</form>';
				echo '<form method="post" ><br>'.
				'<input type="submit" name="user" value="帳戶管理" ></form>';
				echo '<form method="post" >'.
				'<input type="submit" name="deluser" value="清除所有帳戶" ></form><br>';
			}
			?>
		<form method="post" action="index.php">
			<?php
			if(!empty($_SESSION['guest']))
				echo '<input type="submit" name="url" value="轉跳至登入頁面"><br><br>';
			else
				echo '<input type="submit" name="logout" value="登出"><br><br>';	 	
			?>
			權限:
			<?php
			if(!empty($_SESSION['guest']))
				echo '訪客';
			elseif(!empty($_SESSION['user']))
				echo '一般用戶';
			elseif(!empty($_SESSION['robot']))
				echo '機器人';
			elseif(!empty($_SESSION['admin']))
				echo '管理員';
			elseif(!empty($_SESSION['superadmin']))
				echo '超級管理員';
			?>
		</form>
			<br/>
		<div id="lilbox">
			<form method="post">				
				<?php
				function deldir($path)
				{
					if(is_dir($path))
					{//如果是目錄則繼續
						$p = scandir($path);//掃描一個資料夾內的所有資料夾和檔案並返回陣列
						foreach($p as $val)
						{
							if($val !="." && $val !="..")
							{//排除目錄中的.和..
								if(is_dir($path.$val))
								{//如果是目錄則遞迴子目錄，繼續操作
									deldir($path.$val.'/');//子目錄中操作刪除資料夾和檔案
									@rmdir($path.$val.'/');//目錄清空後刪除空資料夾
								}
								else
								{//如果是檔案直接刪除
									unlink($path.$val);
								}
							}
						}
					}
				}
				
				if(!empty($_POST['clean']))
				{	
					unset($_SESSION['succ']);
					unset($_SESSION['inputname']);
					unset($_SESSION['allstudents']);
				}
				
				
				if(!empty($_POST['allstudents']))
				{
					unset($_SESSION['inputname']);
					unset($_SESSION['succ']);
				}

				if(!empty($_POST['user']))
				{
					unset($_SESSION['succ']);
					unset($_SESSION['inputname']);					
					unset($_SESSION['allstudents']);
				}
				
				if(!empty($_POST['op']))
				{
					unset($_SESSION['succ']);					
					unset($_SESSION['allstudents']);
				}
				
				if((!empty($_POST['op'])||!empty($_SESSION['inputname'])))
				{
					$_SESSION['inputname'] = true;					
					echo '<table id="studentlist">';
					echo '<thead>';
					echo '<tr>';
					echo '<th>班級</th>';
					echo '<th>學號</th>';
					echo '<th>姓名</th>';
					echo '<th></th>';
					echo '</tr>';
					echo '</thead>';
					echo '<tr><th><input type="text" name="inputclass" ></th>';
					echo '<th><input type="text" name="inputnumber" ></th>';
					echo '<th><input type="text" name="inputname" ></th>';
					echo '<th><input type="submit" name="inputok" value="送出" ></th></tr>';
					if(!empty($_POST['inputok'])&&!empty($_POST['inputclass'])&&!empty($_POST['inputnumber'])&&!empty($_POST['inputname']))
					{
						try
						{
							$inputclass  = $_POST['inputclass'];
							$inputnumber = $_POST['inputnumber'];
							$inputname   = $_POST['inputname'];
							$conn->query("INSERT INTO student (class,studentid,name) VALUES ('$inputclass','$inputnumber','$inputname')");
							echo '新增成功!!';
						}
						catch (PDOException $e) 
						{
							die($e);
						}		
					}
					else
						echo '請填寫完整!';
				}
				
			//	if(!empty($_POST['allstudents']))
					
				
				if(empty($_POST['clean'])&&(!empty($_POST['allstudents'])||!empty($_POST['x']))||!empty($_SESSION['allstudents']))
				{
					$_SESSION['allstudents'] = true;
					try
					{
						echo '<table id="studentlist">';
						echo '<thead>';
						echo '<tr>';
						echo '<th>編號</th>';
						echo '<th>班級</th>';
						echo '<th>學號</th>';
						echo '<th>姓名</th>';
						echo '</tr>';
						echo '</thead>';
						$i 	 	= false;
						$sql 	= "SELECT * FROM student ORDER BY id asc";
						$result = $conn->query($sql);
						while ($row = $result->fetch_assoc())
						{
							$id 		= $row["id"];
							$class 		= $row["class"];
							$studentid	= $row["studentid"];
							$name 		= $row["name"];
	
							if(!empty($_POST['allstudents'])&& $row["name"] != "NULL")//防止NULL
								echo ("<tr><th>$id</th><th>$class</th><th>$studentid</th><th>$name</th></tr>");//所有學生表單
							elseif(!empty($_POST['student']))
								if($name == $_POST['student'] || $studentid == $_POST['student'])
								{
									echo ("<tr><th>$id</th><th>$class</th><th>$studentid</th><th>$name</th></tr>");//查詢表單
									$i = true;
								}
						}
						if(empty($i)&&empty($_POST['allstudents']))
							echo '無相關此學生';
					}
					catch (PDOException $e)
					{
						die($e);
					}
				}
	
				if(!empty($_POST['loadfromcsv'])&&!empty($_FILES["csvToUpload"]["name"]))
				{
					$target_file 	= "./uploads/" . basename($_FILES["csvToUpload"]["name"]);
					$imageFileType 	= strtolower(pathinfo($target_file,PATHINFO_EXTENSION));// 副檔名
					$uploadOk 		= 1;
					
					if(file_exists($target_file)) 
					{// 檢查文件是否已經存在
						echo "<br>Sorry, file already exists.";
						$uploadOk = 0;
					}
					//echo $_FILES["csvToUpload"]["size"]; 
					if(($_FILES["csvToUpload"]["size"]) > 512800) 
					{// 檢查文件大小
						echo "<br>Sorry, your file is too large.";
						$uploadOk = 0;
					}//wampserver有上傳限制設定，需手動更改。
					// 僅允許CSV文檔
					if($imageFileType != "csv")
					{
						echo "<br>僅限CSV文檔上傳。";
						$uploadOk = 0;
					}
	
					if($uploadOk == 0)
						echo "<br>抱歉，您的文件無法上傳。";
					else 
					{
						if(move_uploaded_file($_FILES["csvToUpload"]["tmp_name"], $target_file))
						{
							echo "您的文件 ". htmlspecialchars( basename( $_FILES["csvToUpload"]["name"])). " 已成功上傳。";
							$csv_dir = getcwd().'\\'.$_FILES["csvToUpload"]["name"]; 
							$ntcpc 	 = fopen($csv_dir, "r");
							$var 	 = 0;
							while($ntcpc_data = fgetcsv($ntcpc, 1000, ','))
							{
								try
								{
									$pc0 = $ntcpc_data[0];
									$pc1 = $ntcpc_data[1];
									$pc2 = $ntcpc_data[2];
									$conn->query("INSERT INTO student (class,studentid,name) VALUES ('$pc0','$pc1','$pc2')");									
									$var++;
								}
								catch (PDOException $e) 
								{
									die($e);
								}								
							}
							echo ("<br>".$var."條紀錄新增成功!");
							echo ("<script>document.getElementById(\"csvReadWrite\").reset();</script>");
							echo ("<script>setTimeout(_ => location='index.php',1500)</script>");
						}
						else 
							echo "抱歉，請稍後重試上傳。";
					}	
				}
				
	/*			if(!empty($_POST['clean'])||!empty($_POST['allstudents']))
				{
					unset($_SESSION['succ']);
					
					if(!empty($_POST['clean'])&&!empty($_SESSION['inputname']))
					{
						unset($_SESSION['inputname']);
						echo "
						<script>
						setTimeout(function(){window.location.href='index.php';},1);
						</script>";
					}
				}*/
						
				if((!empty($_POST['user'])&&empty($_POST['clean']))||!empty($_SESSION['succ']))
				{
					if(empty($_POST['op'])&&empty($_POST['inputok']))
					{
						$_SESSION['succ'] = 1;
						unset($_SESSION['inputname']);
						$j = 0;
						$uservar = null;
						$userresult  = $pw->query("SELECT username, password, level FROM username");					
						echo '<table id="userlist">';
						echo '<thead>';
						echo '<tr>';
						echo '<th>用戶名</th>';
						echo '<th>權限</th>';
						echo '<th>修改帳戶權限</th>';
						echo '<th>刪除帳號</th>';
						echo '</tr>';
						echo '</thead>';
						while($userrow = $userresult->fetch_assoc())
						{
							$j++;
							$username 	  	 = $userrow["username"];
							$hash 		  	 = $userrow["password"];
							$level 		  	 = $userrow["level"];
							$levelID	  	 = null;

							$upsuperadmin 	 = "upSu$j";//最高管理員
							$upadmin 	 	 = "upAd$j";//管理員
							$uprobot 	  	 = "upRb$j";//機器人

							$downadmin    	 = "downAd$j";//管理員
							$downrobot    	 = "downRb$j";//機器人
							$downuser     	 = "downUs$j";//一般用戶
							$delete 	  	 = "del$j";//刪除

							$uplevel5     	 = "<input type='submit' name='$upsuperadmin' 	 value='升格為超級管理員'>";/////
							$uplevel4 	  	 = "<input type='submit' name='$upadmin' 		   	 value='升格為管理員'>";
							$uplevel3 	  	 = "<input type='submit' name='$uprobot' 		  	 value='升格為機器人'>";
							$downlevel4   	 = "<input type='submit' name='$downadmin'		 	 value='降格為管理員'>";
							$downlevel3   	 = "<input type='submit' name='$downrobot'		     value='降格為機器人'>";
							$downlevel2   	 = "<input type='submit' name='$downuser' 		   value='降格為一般用戶'>";
																								
							$deleteinput	 = "</th><th><input type='submit' name='$delete'  value='刪除該帳號'></th>";

							$upsuperadminSql = $pw->prepare("UPDATE username SET level='5' WHERE username='$username'");//升格最高管理員
							$upadminSql 	 = $pw->prepare("UPDATE username SET level='4' WHERE username='$username'");//升格管理員
							$uprobotSql 	 = $pw->prepare("UPDATE username SET level='3' WHERE username='$username'");//升格機器人

							$downadminSql 	 = $pw->prepare("UPDATE username SET level='4' WHERE username='$username'");//降格管理員			
							$downrobotSql	 = $pw->prepare("UPDATE username SET level='3' WHERE username='$username'");//降格機器人
							$downuserSql	 = $pw->prepare("UPDATE username SET level='2' WHERE username='$username'");//降格一般用戶
							$delSql  	 	 = $pw->prepare("DELETE FROM username WHERE username='$username'");//刪除帳號
							echo '<tr>';
							switch ($level)
							{
								case '5':
									$levelID = '超級管理員';
									echo "<th>$username</th><th>$levelID</th><th>";
									if(!empty($_SESSION['superadmin']))
									{
										echo $downlevel4;
										echo $downlevel3;
										echo $downlevel2;
										echo $deleteinput;
									}
									else
										echo '<th></th>';
									break;
								case '4':
									$levelID = '管理員';
									echo "<th>$username</th><th>$levelID</th><th>";
									if(!empty($_SESSION['superadmin']))
									{	
										echo $uplevel5;										
										echo $downlevel3;
										echo $downlevel2;
										echo $deleteinput;
									}
									else
										echo '<th></th>';
									break;
								case '3':
									$levelID = '機器人';
									echo "<th>$username</th><th>$levelID</th><th>";
									if(!empty($_SESSION['superadmin']))
									{
										echo $uplevel5;
										echo $uplevel4;
									}
									echo $downlevel2;
									echo $deleteinput;
									break;
								case '2':
									$levelID = '一般用戶';
									echo "<th>$username</th><th>$levelID</th><th>";
									if(!empty($_SESSION['superadmin']))
									{
										echo $uplevel5;
										echo $uplevel4;
									}
									echo $uplevel3; 
									echo $deleteinput;
									break;
								case '1':
									$levelID = '訪客';
									break;
								default:
									break;
							}
							echo '</tr>';						
							try
							{
								$code = mt_rand(0,1000000);
								echo "<input type='hidden' name='code' value='$code'>";
								
								if(!empty($_POST["upSu$j"])&&!empty($_SESSION['superadmin']))		
									$upsuperadminSql->execute();								
								if(!empty($_POST["upAd$j"])) 
									$upadminSql->execute();		
								if(!empty($_POST["upRb$j"])) 	
									$uprobotSql->execute();					
								if(!empty($_POST["downAd$j"])) 
									$downadminSql->execute();							
								if(!empty($_POST["downRb$j"])) 								
									$downrobotSql->execute();							
								if(!empty($_POST["downUs$j"])) 								
									$downuserSql->execute();							
								if(!empty($_POST["del$j"])) 								
									$delSql->execute();									
								if(empty($_SESSION['code']))
									$_SESSION['code'] = 1;							
								if(!empty($_POST['code'])&&!empty($_SESSION['code']))
									if($_SESSION['code'] != $_POST['code'])
									{
										$_SESSION['code'] = $_POST['code'];
										header("refresh:0;");
									}
							}
							catch (PDOException $e)
							{
								die($e);
							}						
						}	
					}
				}		
						
				if(!empty($_GET['value']))
				{
					$path = "./uploads/";		
					//清空資料夾函式和清空資料夾後刪除空資料夾函式的處理
					deldir($path);//呼叫函式，傳入路徑
					$sql = $conn->prepare("truncate table student");
					$sql->execute();//清除table
				}
				
				if(!empty($_POST['logout'])||!empty($_POST['url']))
				{	
					session_destroy();
					echo "
					<script>
					setTimeout(function(){window.location.href='login.php';},1);
					</script>";
				}
				?>
			</form>
		</div>
	</body>
	<script type="text/javascript">
	function del()
	{
		if(confirm('確定要刪除嗎?'))
		{
			var value="1";
			location.href="index.php?value=" +value;
			alert('刪除成功!!'); 
			return true;
		}
	}
	
	/*function sql()
	{
		  var nickname = prompt("請輸入暱稱", "路人甲"); // <-- 談出對話框。對話框標題為'請輸入暱稱'，預設值為'路人甲'
		  alert("你的暱稱是" + nickname); // <-- 顯示輸入結果
	}*/
	</script>
</html>
<?php
/*
CREATE DATABASE ntcpc CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
use ntcpc;
CREATE USER 'retarded'@'localhost' IDENTIFIED BY 'chingchong';
 CREATE TABLE student (
  id int auto_increment primary key,
  class varchar(40) NOT NULL,
  studentid varchar(20) NOT NULL,
  name varchar(40) NOT NULL
 ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
 GRANT ALL PRIVILEGES ON ntcpc.student TO 'retarded'@'localhost';
flush privilegs;
truncate student; //清空students
*/
?>