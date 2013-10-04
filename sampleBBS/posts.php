<?php


session_start();

// 書き込むデータをセッション変数に保存
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

// エラーチェック関数を呼び出す
error_Check($_POST['name'], $_POST['email'], $_POST['title'], $_POST['contents']);

// エラーがなければ書き込む内容を表示, 特殊文字を変換
$prev_name = htmlspecialchars($_SESSION['name'], ENT_QUOTES);
$prev_email = htmlspecialchars($_SESSION['email'], ENT_QUOTES);
$prev_title = htmlspecialchars($_SESSION['title'], ENT_QUOTES);
$prev_contents = htmlspecialchars($_SESSION['contents'], ENT_QUOTES);
$prev_contents = nl2br($prev_contents);


// 確認画面を表示
print <<<EOF
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="utf-8">
	<title>書き込み確認</title>
	<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/3.10.1/build/cssreset/cssreset-min.css">
	<link rel="stylesheet" href="mystyle.css">
</head>
<body>
	<h1>書き込み確認</h1>
	$error_message
	<br>
	<form action="write.php" method="post">
	名前:$prev_name
	<br>
	アドレス:$prev_email
	<br>
	タイトル:$prev_title
	<br>
	本文:$prev_contents
	<br><br>
	<input type="submit" value="投稿">
	<input type="button" value="戻る" onclick="location.href='bbs.php'">
	</form>
</body>
</html>
EOF;


// エラーチェック関数
function error_Check($name, $email, $title, $contents) {
	$error_message = "";
	
	if($name == "") {
		$error_message .= "名前を入力してください<br>";
	}else if(strlen($name) > 15) {
		$error_message .= "名前は15文字以内で入力してください<br>";
	}
	
	if($email == "") {
		$error_message .= "メールアドレスを入力してください<br>";
	}
	
	if($title == "") {
		$error_message .= "タイトルを入力してください<br>";
	}else if(strlen($title) > 30) {
		$error_message .= "タイトルは30文字以内で入力してください<br>";
	}
	
	if($contents == "") {
		$error_message .= "本文を入力してください<br>";
	}
	
	if($error_message != "" && $_SESSION['name'] == "") {
		$error_message .= "2重投稿はできません<br>";
	}
	// エラーがあった場合
	if($error_message != "") {
		$_SESSION['error_mes'] = $error_message;
		
		// 入力フォームへ遷移させる。 
		header("Location: bbs.php"); 
		exit();
	}
}


?>