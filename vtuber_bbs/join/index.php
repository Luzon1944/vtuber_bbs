<?php
session_start();
require('../dbconnect.php');
// $_POSTのエラー
if (!empty($_POST)) {
	if ($_POST['name'] === '') {
		$error['name'] = 'blank';
	}
	if ($_POST['email'] === '') {
		$error['email'] = 'blank';
	}
	if (strlen($_POST['password']) < 6) {
		$error['password'] = 'length';
	}
	$fileName = $_FILES['image']['name'];
	if (!empty($fileName)) {
		$ext = substr($fileName,-3);
		if ($ext != 'jpg' && $ext != 'gif' && $ext != 'png' ) {
			$error['image'] = 'type';
		}
	}

// メールアドレスの重複チェック
	if(empty($error)) {
		$member = $db->prepare('SELECT COUNT(*) AS cnt FROM members WHERE email=?');
		$member->execute(array($_POST['email']));
		$record = $member->fetch();
		if ($record['cnt'] > 0) {
			$error['email'] = 'duplicate';
		}
	}

	// エラーが発生しなかった場合
	if (empty($error)) {
		if ($_FILES['image']['name'] !=='') {
			$image = date('YmdHis').$_FILES['image']['name'];
			move_uploaded_file($_FILES['image']['tmp_name'],'../member_picture/'.$image);
		} else {
			$image = 0;
		}
		$_SESSION['join'] = $_POST;
		$_SESSION['join']['image'] = $image;

		header('Location: check.php');
		exit();
	}
}
// 書き直し
if ($_REQUEST['action'] == 'rewrite' && isset($_SESSION['join'])) {   
	$_POST = $_SESSION['join'];
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
		<div>
			<div class="header-left">
				<h1 class="logo">ぶいけー</h1>
			</div>
		</div>
		</header>
	<div class="main_margin">
		<h1>会員登録</h1>
		<div>
		<p>次のフォームに必要事項をご記入ください。</p>
			<form action="" method="post" enctype="multipart/form-data">
				<dl>
					<dt>ニックネーム<span class="required">必須</span></dt>
					<dd>
						<input type="text" name="name" size="35" maxlength="255" value="<?php print(htmlspecialchars($_POST['name'],ENT_QUOTES)); ?>" />
						<?php if ($error['name'] === 'blank'): ?>
							<p class="error">*ニックネームを入力して下さい</p>
						<?php endif; ?>
					</dd>
					<dt>メールアドレス<span class="required">必須</span></dt>
					<dd>
						<input type="text" name="email" size="35" maxlength="255" value="<?php print(htmlspecialchars($_POST['email'],ENT_QUOTES)); ?>" />
						<?php if ($error['email'] === 'blank'): ?>
							<p class="error">*メールアドレスを入力して下さい</p>
						<?php endif; ?>
						<?php if ($error['email'] === 'duplicate'): ?>
							<p class="error">*指定されたメールアドレスは、既に登録されています</p>
						<?php endif; ?>
					<dt>パスワード<span class="required">必須</span></dt>
					<dd>
						<input type="password" name="password" size="10" maxlength="20" value="<?php print(htmlspecialchars($_POST['password'],ENT_QUOTES)); ?>" />
						</dd>
						<?php if ($error['password'] === 'length'): ?>
							<p class="error">*パスワードは6文字以上入力して下さい</p>
						<?php endif; ?>
						<?php if ($error['password'] === 'blank'): ?>
							<p class="error">*パスワードを入力して下さい</p>
						<?php endif; ?>

					<dt>画像</dt>
					<dd>
						<input type="file" name="image" size="35" value="test"  />
						<?php if ($error['image'] === 'type'): ?>
							<p class="error">*「.gif」または「.jpg」「.png」の画像を指定して下さい</p>
						<?php endif; ?>
						<?php if (!empty($error)): ?>
							<p class="error">画像を指定して下さい</p>
						<?php endif; ?>
					</dd>
					<dt>プロフィール文</dt>
					<dd>
					<textarea name="profile" cols="50" rows="5" maxlength="255"><?php print (mb_substr(htmlspecialchars($message,ENT_QUOTES),0,30)); ?><?php print(htmlspecialchars($_POST['profile'],ENT_QUOTES)); ?></textarea>
					</dd>	
					<dt>Twitter ID</dt>
					<dd>
						<input type="text" name="twitter" size="35" value="<?php print(htmlspecialchars($_POST['twitter'],ENT_QUOTES)); ?>" />
					</dd>				
				</dl>
				<div><input type="submit" value="入力内容を確認する" /></div>
			</form>
		</div>
	</body>
</html>
