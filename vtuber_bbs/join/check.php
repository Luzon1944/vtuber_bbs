<?php
session_start();
require('../dbconnect.php');

if(!isset($_SESSION['join'])) {
	header('$Location: ../list.php');
	exit();
}

if (!empty($_POST)) {
	$statement = $db->prepare('INSERT INTO members SET name=?,email=?,password=?,picture=?,profile=?,twitter=?,created=NOW()');
	echo $statement->execute(array(
		$_SESSION['join']['name'],
		$_SESSION['join']['email'],
		sha1($_SESSION['join']['password']),
		$_SESSION['join']['image'],
		$_SESSION['join']['profile'],
		$_SESSION['join']['twitter'],
	));
	unset($_SESSION['join']);
	header('Location: thanks.php');
	exit();
}

?>

<!DOCTYPE html> 
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>会員登録</title>

	<link rel="stylesheet" href="../stylesheet.css" />
</head>
<body>
	<header>
		<div class="header-left">
			<h1 class="logo">ぶいけー</h1>
		</div> 
  </header>
	<div class="main_margin">
	<h1>会員登録</h1>
		<p>記入した内容を確認して、「登録する」ボタンをクリックしてください</p>
		<form action="" method="post">
			<input type="hidden" name="action" value="submit" />
			<dl>
				<dt>ニックネーム</dt>
				<dd>
				<?php print(htmlspecialchars($_SESSION['join']['name'],ENT_QUOTES)); ?>
						</dd>
				<dt>メールアドレス</dt>
				<dd>
				<?php print(htmlspecialchars($_SESSION['join']['email'],ENT_QUOTES)); ?>		
						</dd>
				<dt>パスワード</dt>
				<dd>
				<?php 
				$password_id=mb_strlen(htmlspecialchars($_SESSION['join']['password'],ENT_QUOTES));
				for ($i = 0;$i < $password_id;$i++) {
					echo '*';
				};
				?>
				</dd>
				<dt>写真など</dt>
				<dd>
				<?php if ($_SESSION['join']['image'] !== ''): ?>
					<img src="../member_picture/<?php print(htmlspecialchars($_SESSION['join']['image'],ENT_QUOTES)); ?>" width="48" height="48">
				<?php endif; ?>
				</dd>
				<dt>プロフィール文</dt>
				<dd>
					<?php print(htmlspecialchars($_SESSION['join']['profile'],ENT_QUOTES)); ?>
				</dd>
				<dt>Twitter ID</dt>
				<dd>
					<?php print(htmlspecialchars($_SESSION['join']['twitter'],ENT_QUOTES)); ?>
				</dd>
			</dl>
			<div><a href="index.php?action=rewrite">&laquo;&nbsp;書き直す</a> | <input type="submit" value="登録する" /></div>
		</form>
	</div>
</body>
</html>
