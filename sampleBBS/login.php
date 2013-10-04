<?php


session_start();


// ログイン名･パスワード
define("LOGIN_NAME", "login");
define("LOGIN_PASSWORD", "password");


if($_POST['loginName'] != "" && $_POST['loginPassword'] != "") {
	$loginMes = Login($_POST);
}


if($_SESSION['login'] != "logout" && $_POST['logout'] == "ログアウト") {
	$logoutMes = Logout($_POST);
}


print<<<EOF
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>管理者ログイン</title>
	<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/3.10.1/build/cssreset/cssreset-min.css">
	<link rel="stylesheet" href="mystyle.css">
</head>
<body>
<div align="center">
	<h1>管理者ログイン</h1>
	<form action="login.php" method="post">
		<font color="#ff0000">$loginMes</font>
		<font color="#ff0000">$logoutMes</font>
		<br>
		<br>
		ログイン名<input type="text" name="loginName">
		<br><br>
		パスワード<input type="password" name="loginPassword">
		<br><br>
		<input type="submit" value="ログイン">
		<input type="submit" name="logout" value="ログアウト">
	</form>
	<br>
	<a href="bbs.php">掲示板に戻る</a>
</div>
</body>
</html>
EOF;



// 管理者ログイン処理
function Login($login) {
	$loginStr = "";
	
	$_SESSION['login'] = "failure";
	
	// ログイン名･パスワードが一致するか確認
	if($login['loginName'] == LOGIN_NAME &&
		 $login['loginPassword'] == LOGIN_PASSWORD) {
		$_SESSION['login'] = "success";
		$loginStr = "管理者としてログインしました";
	}else {
		$_SESSION['login'] = "failure";
		$loginStr = "ログイン名･パスワードが違います";
	}
	return $loginStr;
}


// 管理者ログアウト処理
function Logout($logout) {
	$logoutStr = "";
	$_SESSION['login'] = "logout";
	$logoutStr = "ログアウトしました";
	return $logoutStr;
}


?>