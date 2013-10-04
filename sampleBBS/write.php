<?php


session_start();


$filename = "bbs.txt";

$name = $_SESSION['name'];
$email = $_SESSION['email'];
$title = $_SESSION['title'];
$contents = $_SESSION['contents'];


$fp = fopen($filename, "r");
// ファイルロックされているか
	if(filesize($filename) != "0") {
		// ファイルを読み込む
		$cont = fread($fp, filesize($filename));
		fclose($fp);
	}
	$posts = $name.",".$email.",".$title.",".$contents.",".date("Y/m/d H:i:s")."\n";
	
	
	$fp = fopen($filename, "w");
	fwrite($fp, $posts);
	fwrite($fp, $cont);
	fclose($fp);
	
	$_SESSION['name'] = "";
	$_SESSION['email'] = "";
	$_SESSION['title'] = "";
	$_SESSION['contents'] = "";


print <<<EOF
<!DOCTYPE html>
<html lang="ja"

<head>
	<meta charset="utf-8">
	<title>書き込み完了</title>
	<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/3.10.1/build/cssreset/cssreset-min.css">
	<link rel="stylesheet" href="mystyle.css">
</head>
<body>
	<h1>書き込み完了</h1>
	<br>
	書き込みが完了しました
	<br><br>
	<a href="bbs.php">トップページへ戻る</a>
</body>
</html>
EOF;
?>