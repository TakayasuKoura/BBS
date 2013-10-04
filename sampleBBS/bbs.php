<?php


session_start();


// 1�ڡ�����ɽ��������
define("PAGE_LENGTH", "3");


// ���顼����äƤ�����硢���å�����ѿ�����¸���Ƥ����ͤ�ɽ��
$name = htmlspecialchars($_SESSION['name'], ENT_QUOTES);
$email = htmlspecialchars($_SESSION['email'], ENT_QUOTES);
$title = htmlspecialchars($_SESSION['title'], ENT_QUOTES);
$contents = htmlspecialchars($_SESSION['contents'], ENT_QUOTES);
$error_message = $_SESSION['error_mes'];


// ���顼��å�������ɽ�������ꥢ
$_SESSION['error_mes'] = "";


// �����Ԥǥ����󤷡�����⡼�ɤʤ�������
if($_SESSION['login'] == "success" && $_GET['mode'] == "del") {
	$deleteMes = Delete($_GET['id']);
}


// HTML�����Ƥ�ɽ��
print <<<EOF
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="utu-8">
	<title>�Ǽ���</title>
	<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/3.10.1/build/cssreset/cssreset-min.css">
	<link rel="stylesheet" href="mystyle.css">
</head>
<body>
<div>
	<h1>�����Ǽ���</h1>
	<p>
	<table>
	<tr><td>
	<form action="posts.php" method="post">
		<font color="#ff0000">$error_message</font>
		<font color="#ff0000">$deleteMes</font>	
		<br>
		<br>
		̾��<br>
		<input type="text" name="name" value="$name"><br><br>
		���ɥ쥹<br>
		<input type="text" name="email" value="$email"><br><br>
		�����ȥ�<br>
		<input type="text" name="title" value="$title"><br><br>
		��ʸ<br>
		<textarea id="textarea" rows="6" cols="50" name="contents" value="$contents"></textarea>
		<br><br>
		<input type="submit" name="submit" value="����">
		<input type="reset" value="���ꥢ">
	</form>
	</tr></td>
	</table>
	<br><br>
	<h2>��ư���</h2>
	<a href="login.php">�����ԥ�����</a>
</div>
EOF;
		
	// �ڡ����ֹ�����
	if(isset($_GET['p']) && is_numeric($_GET['p'])) {
		$p = $_GET['p'];
	}else {
		$p = 0;
	}
	
	
	// ɽ������ǡ������ɤ߹���ؿ���ƤӽФ�
	$data = read_bbs($p);
	
	for($i = 0; $i < count($data); $i++) {
		// HTML�ü�ʸ�����Ѵ�
		$data[$i]['name'] = htmlspecialchars($data[$i]['name'], ENT_QUOTES);
		$data[$i]['title'] = htmlspecialchars($data[$i]['title'], ENT_QUOTES);
		$data[$i]['contents'] = nl2br(htmlspecialchars($data[$i]['contents'], ENT_QUOTES));
		$data[$i]['date'] = htmlspecialchars($data[$i]['date'], ENT_QUOTES);
		
		// �����Ԥǥ����󤷤��������󥯤�ɽ������
		if($_SESSION['login'] == "success") {
			$data[$i]['del'] = "<a href=\"bbs.php?mode=del&id={$data[$i]['cnt']}\">���</a>";
		}
		
		
print <<<EOF
		<hr>
		������� {$data[$i]['date']} {$data[$i]['del']}
		<br><br>
		̾����{$data[$i]['name']}
		<br><br>
		�����ȥ� {$data[$i]['title']}
		<br><br>
		��ʸ��{$data[$i]['contents']}
		<br>
EOF;
	}
	
	print "<hr>\n";
	
	// �ڡ����ֹ���֤����
	$page = page_bbs($p);
	
	// ���ڡ�������
	if(isset($page['previous'])) {
		print "<a href=\"./bbs.php?p={$page['previous']}\">���ڡ���</a>";
	}
	
	// ���ڡ�������
	if($page['next']) {
		print "<a href=\"./bbs.php?p={$page['next']}\">���ڡ���</a>";
	}

print <<<EOF
</body>
</html>
EOF;




function read_bbs($page = 0) {
	$filename = "bbs.txt";
		
	// �ɤ߹���Ǥ���ե�����ιԿ�
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
		echo "�ե����뤬�����ޤ���Ǥ���";
		exit;
	}
	fclose($fp);
	
	// 1�ڡ�����ɽ��������
	$page_length = PAGE_LENGTH;
	
	// ���ڡ����������
	$maxpage = $row / $page_length;
	$tmp = (int)$maxpage;
	if($maxpage > $tmp) {
		$maxpage = $tmp + 1;
	}
	$maxpage--;
	
	// ���ߤΥڡ������鳫�ϤȽ��������
	$start_page = $page * $page_length;
	$end_page = ($page * $page_length) + $page_length;
	
	$cnt = 0;
	$data1 = array();
	
	// �ڡ����γ��Ϥ��齪���ޤǥǡ������Ǽ����
	for($i = $start_page; $i < $end_page; $i++) {
		// �ǡ����κ������Ķ�����齪λ
		if($i >= $row) {
			break;
		}
		$data1[$cnt]['name'] = $data[$i]['name'];
		$data1[$cnt]['title'] = $data[$i]['title'];
		$data1[$cnt]['contents'] = $data[$i]['contents'];
		$data1[$cnt]['date'] = $data[$i]['date'];
		$data1[$cnt]['cnt'] = $i + 1;	// �Կ�����¸
		$cnt++;
	}
	
	return $data1;
}

function page_bbs($page = 0) {
	$filename = "bbs.txt";
	
	$row = 0;
	$data = array();
	
	// �ե�����򤹤٤��ɤ߹��ߡ��ǡ��������濫�뤫����
	$fp = fopen($filename, "r");
	while(($buffer = fgetcsv($fp, 1000, ",")) !== false) {
		$row++;
	}
	fclose($fp);
	
	// 1�ڡ�����ɽ������ǡ��������ɤ߹���
	$page_length = PAGE_LENGTH;
	
	// ����ڡ��������
	$maxpage = $row / $page_length;
	$tmp = (int)$maxpage;
	if($maxpage > $tmp) {
		$maxpage = $tmp + 1;
	}
	$maxpage--;
	
	// ���Υڡ��������뤫�����å�
	if($maxpage > $page) {
		$data['next'] = $page + 1;
	}else {
		$data['next'] = false;
	}
	
	// ���Υڡ��������뤫�����å�
	if($page - 1 >= 0) {
		$data['previous'] = $page - 1;
	}
	return $data;
}


// �������
function Delete($delRow) {
	if($delRow == "") {
		return "";
	}
	
	
	$fileName = "bbs.txt";	// �ɤ߹���ե�����̾
	$row = 1;	// �ɤ߹�����ֹ���Ǽ
	$bbsData = "";	// �ե�����˽񤭹���ǡ������Ǽ
	$deleteMes = "";	// �����å��������Ǽ
	
	
	$fp = fopen($fileName, "r");
	while(($tmp = fgetcsv($fp, 1000, ",")) !== FALSE) {
		// ���ֹ椬���פ��ʤ��ä��顢�񤭹���ǡ�����ʸ������Ǽ
		if($row != $delRow) {
			  $bbsData = $bbsData.$tmp[0].",".$tmp[1].",".$tmp[2].",".$tmp[3]."\n";
		}else {	// ���פ������񤭹��ޤ������å���������¸
			$deleteMes = $delRow."�Υǡ����������ޤ�����";
		}
		$row++;
	}
	fclose($fp);
	
	
	// ����������ưʳ���ե�����˽񤭹���
	$fp = fopen($fileName, "w");
	fwrite($fp, $bbsData);
	fclose($fp);
	
	
	return $deleteMes;
}


?>