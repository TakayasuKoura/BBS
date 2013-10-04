<?php


session_start();


// 1ページに表示する件数
define("PAGE_LENGTH", "3");


// エラーで戻ってきた場合、セッション変数に保存していた値を表示
$name = htmlspecialchars($_SESSION['name'], ENT_QUOTES);
$email = htmlspecialchars($_SESSION['email'], ENT_QUOTES);
$title = htmlspecialchars($_SESSION['title'], ENT_QUOTES);
$contents = htmlspecialchars($_SESSION['contents'], ENT_QUOTES);
$error_message = $_SESSION['error_mes'];


// エラーメッセージを表示しクリア
$_SESSION['error_mes'] = "";


// 管理者でログインし、削除モードなら削除する
if($_SESSION['login'] == "success" && $_GET['mode'] == "del") {
	$deleteMes = Delete($_GET['id']);
}


// HTML内で投稿を表示
print <<<EOF
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="utu-8">
	<title>掲示板</title>
	<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/3.10.1/build/cssreset/cssreset-min.css">
	<link rel="stylesheet" href="mystyle.css">
</head>
<body>
<div>
	<h1>画像掲示板</h1>
	<p>
	<table>
	<tr><td>
	<form action="posts.php" method="post">
		<font color="#ff0000">$error_message</font>
		<font color="#ff0000">$deleteMes</font>	
		<br>
		<br>
		名前<br>
		<input type="text" name="name" value="$name"><br><br>
		アドレス<br>
		<input type="text" name="email" value="$email"><br><br>
		タイトル<br>
		<input type="text" name="title" value="$title"><br><br>
		本文<br>
		<textarea id="textarea" rows="6" cols="50" name="contents" value="$contents"></textarea>
		<br><br>
		<input type="submit" name="submit" value="送信">
		<input type="reset" value="クリア">
	</form>
	</tr></td>
	</table>
	<br><br>
	<h2>投稿一覧</h2>
	<a href="login.php">管理者ログイン</a>
</div>
EOF;
		
	// ページ番号を取得
	if(isset($_GET['p']) && is_numeric($_GET['p'])) {
		$p = $_GET['p'];
	}else {
		$p = 0;
	}
	
	
	// 表示するデータを読み込む関数を呼び出す
	$data = read_bbs($p);
	
	for($i = 0; $i < count($data); $i++) {
		// HTML特殊文字を変換
		$data[$i]['name'] = htmlspecialchars($data[$i]['name'], ENT_QUOTES);
		$data[$i]['title'] = htmlspecialchars($data[$i]['title'], ENT_QUOTES);
		$data[$i]['contents'] = nl2br(htmlspecialchars($data[$i]['contents'], ENT_QUOTES));
		$data[$i]['date'] = htmlspecialchars($data[$i]['date'], ENT_QUOTES);
		
		// 管理者でログインした場合削除リンクを表示する
		if($_SESSION['login'] == "success") {
			$data[$i]['del'] = "<a href=\"bbs.php?mode=del&id={$data[$i]['cnt']}\">削除</a>";
		}
		
		
print <<<EOF
		<hr>
		投稿日時 {$data[$i]['date']} {$data[$i]['del']}
		<br><br>
		名前　{$data[$i]['name']}
		<br><br>
		タイトル {$data[$i]['title']}
		<br><br>
		本文　{$data[$i]['contents']}
		<br>
EOF;
	}
	
	print "<hr>\n";
	
	// ページ番号状態を取得
	$page = page_bbs($p);
	
	// 前ページ処理
	if(isset($page['previous'])) {
		print "<a href=\"./bbs.php?p={$page['previous']}\">前ページ</a>";
	}
	
	// 次ページ処理
	if($page['next']) {
		print "<a href=\"./bbs.php?p={$page['next']}\">次ページ</a>";
	}

print <<<EOF
</body>
</html>
EOF;




function read_bbs($page = 0) {
	$filename = "bbs.txt";
		
	// 読み込んでいるファイルの行数
	$row = 0;
	
	$data = array();
		
	if(($fp = fopen($filename, "r")) !== false) {
		while(($buffer = fgetcsv($fp, 1000, ",")) !== false) {
			$data[$row]['name'] = $buffer[0];
			$data[$row]['title'] = $buffer[2];
			$data[$row]['contents'] = $buffer[3];
			$data[$row]['date'] = $buffer[4];
			$row++;
		}
	}else {
		echo "ファイルが開けませんでした";
		exit;
	}
	fclose($fp);
	
	// 1ページに表示する件数
	$page_length = PAGE_LENGTH;
	
	// 全ページ数を求める
	$maxpage = $row / $page_length;
	$tmp = (int)$maxpage;
	if($maxpage > $tmp) {
		$maxpage = $tmp + 1;
	}
	$maxpage--;
	
	// 現在のページから開始と終わりを求める
	$start_page = $page * $page_length;
	$end_page = ($page * $page_length) + $page_length;
	
	$cnt = 0;
	$data1 = array();
	
	// ページの開始から終わりまでデータを格納する
	for($i = $start_page; $i < $end_page; $i++) {
		// データの最大数を超えたら終了
		if($i >= $row) {
			break;
		}
		$data1[$cnt]['name'] = $data[$i]['name'];
		$data1[$cnt]['title'] = $data[$i]['title'];
		$data1[$cnt]['contents'] = $data[$i]['contents'];
		$data1[$cnt]['date'] = $data[$i]['date'];
		$data1[$cnt]['cnt'] = $i + 1;	// 行数を保存
		$cnt++;
	}
	
	return $data1;
}

function page_bbs($page = 0) {
	$filename = "bbs.txt";
	
	$row = 0;
	$data = array();
	
	// ファイルをすべて読み込み、データが何件あるか求める
	$fp = fopen($filename, "r");
	while(($buffer = fgetcsv($fp, 1000, ",")) !== false) {
		$row++;
	}
	fclose($fp);
	
	// 1ページに表示するデータ数を読み込む
	$page_length = PAGE_LENGTH;
	
	// 最大ページを求める
	$maxpage = $row / $page_length;
	$tmp = (int)$maxpage;
	if($maxpage > $tmp) {
		$maxpage = $tmp + 1;
	}
	$maxpage--;
	
	// 次のページがあるかチェック
	if($maxpage > $page) {
		$data['next'] = $page + 1;
	}else {
		$data['next'] = false;
	}
	
	// 前のページがあるかチェック
	if($page - 1 >= 0) {
		$data['previous'] = $page - 1;
	}
	return $data;
}


// 削除処理
function Delete($delRow) {
	if($delRow == "") {
		return "";
	}
	
	
	$fileName = "bbs.txt";	// 読み込むファイル名
	$row = 1;	// 読み込む行番号を格納
	$bbsData = "";	// ファイルに書き込むデータを格納
	$deleteMes = "";	// 削除メッセージを格納
	
	
	$fp = fopen($fileName, "r");
	while(($tmp = fgetcsv($fp, 1000, ",")) !== FALSE) {
		// 行番号が一致しなかったら、書き込むデータに文字列を格納
		if($row != $delRow) {
			  $bbsData = $bbsData.$tmp[0].",".$tmp[1].",".$tmp[2].",".$tmp[3]."\n";
		}else {	// 一致した場合書き込まず削除メッセージを保存
			$deleteMes = $delRow."のデータを削除しました。";
		}
		$row++;
	}
	fclose($fp);
	
	
	// 削除した内容以外をファイルに書き込む
	$fp = fopen($fileName, "w");
	fwrite($fp, $bbsData);
	fclose($fp);
	
	
	return $deleteMes;
}


?>