<?php


session_start();


// ������̾���ѥ����
define("LOGIN_NAME", "login");
define("LOGIN_PASSWORD", "password");


if($_POST['loginName'] != "" && $_POST['loginPassword'] != "") {
	$loginMes = Login($_POST);
}


if($_SESSION['login'] != "logout" && $_POST['logout'] == "��������") {
	$logoutMes = Logout($_POST);
}


print<<<EOF
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>�����ԥ�����</title>
	<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/3.10.1/build/cssreset/cssreset-min.css">
	<link rel="stylesheet" href="mystyle.css">
</head>
<body>
<div align="center">
	<h1>�����ԥ�����</h1>
	<form action="login.php" method="post">
		<font color="#ff0000">$loginMes</font>
		<font color="#ff0000">$logoutMes</font>
		<br>
		<br>
		������̾<input type="text" name="loginName">
		<br><br>
		�ѥ����<input type="password" name="loginPassword">
		<br><br>
		<input type="submit" value="������">
		<input type="submit" name="logout" value="��������">
	</form>
	<br>
	<a href="bbs.php">�Ǽ��Ĥ����</a>
</div>
</body>
</html>
EOF;



// �����ԥ��������
function Login($login) {
	$loginStr = "";
	
	$_SESSION['login'] = "failure";
	
	// ������̾���ѥ���ɤ����פ��뤫��ǧ
	if($login['loginName'] == LOGIN_NAME &&
		 $login['loginPassword'] == LOGIN_PASSWORD) {
		$_SESSION['login'] = "success";
		$loginStr = "�����ԤȤ��ƥ����󤷤ޤ���";
	}else {
		$_SESSION['login'] = "failure";
		$loginStr = "������̾���ѥ���ɤ��㤤�ޤ�";
	}
	return $loginStr;
}


// �����ԥ������Ƚ���
function Logout($logout) {
	$logoutStr = "";
	$_SESSION['login'] = "logout";
	$logoutStr = "�������Ȥ��ޤ���";
	return $logoutStr;
}


?>