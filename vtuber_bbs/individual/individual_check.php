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

$name = htmlspecialchars($_SESSION['edit']['name'],ENT_QUOTES);
$profile = htmlspecialchars($_SESSION['edit']['profile'],ENT_QUOTES);
$image = htmlspecialchars($_SESSION['edit']['image'],ENT_QUOTES);
$twitter = htmlspecialchars($_SESSION['edit']['twitter'],ENT_QUOTES);

$id = $member['id'];

if (!empty($_POST)) {
    $stmt = $db->prepare("UPDATE members SET $edit=? WHERE id=$id");
    $stmt->execute(array($name.$profile.$image.$twitter));
  unset($_SESSION['edit']);
	header('Location: individual_thanks.php');
	exit();
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
  <div class='main_margin'>
    <h2>変更</h2>
    <p>記入した内容を確認して、「変更する」ボタンをクリックしてください</p>
		<form action="" method="post">
			<input type="hidden" name="action" value="submit" />
      <?php if ($edit==='name'): ?>
        <dt>ニックネーム</dt>
        <dd>
          <?php print $name?>
        </dd>
      <?php endif;?>
      <?php if ($edit==='profile'): ?>
        <dt>プロフィール</dt>
        <dd>
          <?php print $profile;?>
        </dd>
      <?php endif;?>
      <?php if ($edit==='twitter'): ?>
        <dt>Twitter ID</dt>
        <dd>
          <?php print $twitter?>
        </dd>
      <?php endif;?>
      <div><a href="individual_edit.php?edit=<?php print $edit; ?>&action=rewrite">&laquo;&nbsp;書き直す</a> | <input type="submit" value="変更する" /></div>
		</form>
  </div>
</body>
</html>