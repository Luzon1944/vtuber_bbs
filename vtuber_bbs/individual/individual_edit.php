<?php
session_start();
require('../dbconnect.php');

// 一時間ページを移動しなければ、ログインページに戻される
if(isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
  $_SESSION['time'] = time();
  $members = $db->prepare('SELECT * FROM members WHERE id=?');
  $members->execute(array($_SESSION['id']));
  $member = $members->fetch();
} else {
  header('Location: ../login.php');
  exit();
}

$edit = htmlspecialchars($_GET['edit'],ENT_QUOTES);
// エラーチェック
if (!empty($_POST) || !empty($_FILES)) {
	if (!empty($_POST)){
		if ($_POST['name'] === '' && $edit === 'name') {
			$error['name'] = 'blank';
		}
		if ($_POST['profile'] === '' && $edit === 'profile') {
			$error['profile'] = 'blank';
		}
		if ($_POST['twitter'] === '' && $edit === 'twitter') {
			$error['twitter'] = 'blank';
		}
	}
	if (!empty($_FILES)) {
		$image = date('YmdHis').$_FILES['image']['name'];
		move_uploaded_file($_FILES['image']['tmp_name'],'../member_picture/'.$image);
		$fileName = $_FILES['image']['name'];
		if ($fileName !== '') {
			$ext = substr($fileName,-3);
			if ($ext != 'jpg' && $ext != 'gif' && $ext != 'png' ) {
				$error['image'] = 'type';
			} 
		} else {
			$error['image'] = 'blank';
		}
	}

	
	if (empty($error)) {
		// $image = date('YmdHis').$_FILES['image']['name'];
		// move_uploaded_file($_FILES['image']['tmp_name'],'../member_picture/'.$image);
		$_SESSION['edit'] = $_POST;
		$_SESSION['edit']['image'] = $image;
		header("Location: individual_check.php?edit=$edit");
		exit();
	}
}

if ($_REQUEST['action'] == 'rewrite' && isset($_SESSION['edit'])) {   
	$_POST = $_SESSION['edit'];
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<title>ぶいけー</title>
<meata charset="UTF-8">
<meta name="viewport" content="initial-scale=1.0">
<link rel="stylesheet" href="../stylesheet.css" media="all">
</head>

<body>
	<header>		
		<div class="header-left">
			<h1 class="logo">ぶいけー</h1>
		</div>
	</header>
	<div class="main_margin">
		<h2>マイページ変更</h2>
			<form action="" method="post" enctype="multipart/form-data">
			<dl>
				<?php if ($edit==='name'): ?>
					<dt>ニックネームを変更</dt>
					<dt>
						<input type="text" name="name" size="35" maxlength="255" value="<?php print(htmlspecialchars($_POST['name'],ENT_QUOTES)); ?>" />
					</dt>
					<?php if ($error['name'] === 'blank'): ?>
						<p class="error">*ニックネームを入力して下さい</p>
					<?php endif; ?>
				<?php endif; ?>			
				<?php if ($edit==='profile'): ?>
					<dt>プロフィール文</dt>
					<dt>
						<textarea name="profile" cols="50" rows="5" maxlength="255"><?php print (mb_substr(htmlspecialchars($message,ENT_QUOTES),0,30)); ?><?php print(htmlspecialchars($_POST['profile'],ENT_QUOTES)); ?></textarea>
						<?php if ($error['profile'] === 'blank'): ?>
							<p class="error">*プロフィールを入力して下さい</p>
						<?php endif; ?>
					</dt>	
				<?php endif; ?>	
				<?php if ($edit==='picture'):?>
					<dt>画像</dt>
					<dt>
						<input type="file" name="image" size="35" value="test"  />
						<?php if ($error['image'] === 'type'): ?>
							<p class="error">*「.gif」または「.jpg」「.png」の画像を指定して下さい</p>
						<?php endif; ?>
						<?php if ($error['image'] === 'blank'): ?>
							<p class="error">画像を指定して下さい</p>
						<?php endif; ?>
					</dt>
				<?php endif ;?> 
				<?php if ($edit==='twitter'):?>
					<dt>Twitter ID</dt>
					<dt>
						<input type="text" name="twitter" size="35" maxlength="255" value="<?php print(htmlspecialchars($_POST['twitter'],ENT_QUOTES)); ?>" />
					</dt>			
					<?php if ($error['twitter'] === 'blank'): ?>
						<p class="error">*TwitterのIDを入力して下さい</p>
					<?php endif; ?>
				<?php endif ;?>
			</dl>
			<div><input type="submit" value="入力内容を確認する" /></div>
			</form>
	</div>
</body>
</html>