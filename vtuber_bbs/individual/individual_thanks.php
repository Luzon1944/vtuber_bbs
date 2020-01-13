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
    <div class="wrapper">
      <div class="header-right"><a href="logout.php">ログアウト</a></div>
      <div class="header-right"><a href="list.php">トップ</a></div>
    </div>  
  </header>
  <div class='main_margin'>
    <h2>変更が完了しました</h2>
    <p><a href="../individual.php">戻る</a></p>
  </div>
</body>
</html>