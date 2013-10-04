<?php


session_start();

// �񤭹���ǡ����򥻥å�����ѿ�����¸
if(isset($_POST['name'])) {
	$_SESSION['name'] = $_POST['name'];
}

if(isset($_POST['email'])) {
	$_SESSION['email'] = $_POST['email'];
}

if(isset($_POST['title'])) {
	$_SESSION['title'] = $_POST['title'];
}

if(isset($_POST['contents'])) {
	$_SESSION['contents'] = $_POST['contents'];
}

// ���顼�����å��ؿ���ƤӽФ�
error_Check($_POST['name'], $_POST['email'], $_POST['title'], $_POST['contents']);

// ���顼���ʤ���н񤭹������Ƥ�ɽ��, �ü�ʸ�����Ѵ�
$prev_name = htmlspecialchars($_SESSION['name'], ENT_QUOTES);
$prev_email = htmlspecialchars($_SESSION['email'], ENT_QUOTES);
$prev_title = htmlspecialchars($_SESSION['title'], ENT_QUOTES);
$prev_contents = htmlspecialchars($_SESSION['contents'], ENT_QUOTES);
$prev_contents = nl2br($prev_contents);


// ��ǧ���̤�ɽ��
print <<<EOF
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="utf-8">
	<title>�񤭹��߳�ǧ</title>
	<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/3.10.1/build/cssreset/cssreset-min.css">
	<link rel="stylesheet" href="mystyle.css">
</head>
<body>
	<h1>�񤭹��߳�ǧ</h1>
	$error_message
	<br>
	<form action="write.php" method="post">
	̾��:$prev_name
	<br>
	���ɥ쥹:$prev_email
	<br>
	�����ȥ�:$prev_title
	<br>
	��ʸ:$prev_contents
	<br><br>
	<input type="submit" value="���">
	<input type="button" value="���" onclick="location.href='bbs.php'">
	</form>
</body>
</html>
EOF;


// ���顼�����å��ؿ�
function error_Check($name, $email, $title, $contents) {
	$error_message = "";
	
	if($name == "") {
		$error_message .= "̾�������Ϥ��Ƥ�������<br>";
	}else if(strlen($name) > 15) {
		$error_message .= "̾����15ʸ����������Ϥ��Ƥ�������<br>";
	}
	
	if($email == "") {
		$error_message .= "�᡼�륢�ɥ쥹�����Ϥ��Ƥ�������<br>";
	}
	
	if($title == "") {
		$error_message .= "�����ȥ�����Ϥ��Ƥ�������<br>";
	}else if(strlen($title) > 30) {
		$error_message .= "�����ȥ��30ʸ����������Ϥ��Ƥ�������<br>";
	}
	
	if($contents == "") {
		$error_message .= "��ʸ�����Ϥ��Ƥ�������<br>";
	}
	
	if($error_message != "" && $_SESSION['name'] == "") {
		$error_message .= "2����ƤϤǤ��ޤ���<br>";
	}
	// ���顼�����ä����
	if($error_message != "") {
		$_SESSION['error_mes'] = $error_message;
		
		// ���ϥե���������ܤ����롣 
		header("Location: bbs.php"); 
		exit();
	}
}


?>