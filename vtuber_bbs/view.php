<?php
session_start();
require('dbconnect.php');

$dbnamekana = htmlspecialchars($_GET['dbnamekana'],);

// 一時間ページを移動しなければ、ログインページに戻される
if(isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
  $_SESSION['time'] = time();
  $members = $db->prepare('SELECT * FROM members WHERE id=?');
  $members->execute(array($_SESSION['id']));
  $member = $members->fetch();
} else {
  header('Location: login.php');
  exit();
}

// リスト内の順番(番号)  
$stmt = $db->query("SELECT * FROM list WHERE dbnamekana='$dbnamekana'");
foreach ($stmt as $row) {
  $listnumber = intval($row['id']);
}

// $_REQUEST['id']がない場合
if (empty($_REQUEST['id'])) {
  header("Location: bbs.php?idol={$_GET['idol']}&dbnamekana={$_GET['dbnamekana']}");
  exit();
}


$posts = $db->prepare("SELECT m.name,m.picture,p.*FROM members m,posts$listnumber p WHERE m.id=p.member_id AND p.id=?");
$posts->execute(array($_REQUEST['id']));

?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>ひとこと掲示板</title>

	<link rel="stylesheet" href="stylesheet.css" />
</head>詳細については、ここをクリックしてください。

<body>
<header>
    <div class="header-left">
      <h1 class="logo">ぶいけー</h1>
    </div>
    <div class="wrapper">
      <div class="header-right"><a class="menu" href="logout.php">ログアウト</a></div>
      <div class="header-right"><a class="menu" href="individual.php">マイページ</a></div>
      <div class="header-right"><a class="menu" href="list.php">トップ</a></div>
    </div>  
</header>
<div class="main_margin">
  <?php if ($post = $posts->fetch()): ?>
    <div class="msg">
    <?php if ($p_picture != 0):?>
      <img  width="48" height="48" src="member_picture/<?php print(htmlspecialchars($post['picture'])); ?>" />
    <?php endif ;?>
      <p><?php print(htmlspecialchars($post['message'])); ?><span class="name">（<?php print(htmlspecialchars($post['name'])); ?>）</span></p>
      <p><?php print(htmlspecialchars($post['created'])); ?></p>
    </div>
  <?php else: ?>
	  <p>その投稿は削除されたか、URLが間違えています</p>
  <?php endif; ?>
  <p>&laquo;<a href="bbs.php?idol=<?php print $_GET['idol']; ?>&dbnamekana=<?php print$_GET['dbnamekana']; ?>">戻る</a></p>
</div>
</body>
</html>
