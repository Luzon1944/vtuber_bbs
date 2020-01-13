<?php

session_start();
require('dbconnect.php');

// 一時間操作されてなければログインページに戻る
if(isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
  $_SESSION['time'] = time();
  $members = $db->prepare('SELECT * FROM members WHERE id=?');
  $members->execute(array($_SESSION['id']));
  $member = $members->fetch();
} else {
  header('Location: login.php');
  exit();
}

// $myname=1 ->自分   $myname=0 ->他人
$myname = 0;
  // $n = $member['name'];
  // 自分と他人を識別する
  // $getname=getで受け取った名前
$getname = htmlspecialchars($_GET['dbnamekana'],ENT_QUOTES);
$getname = urldecode($getname);
$name = $member['name'];
if ($getname !== ""){
  if ($getname !== $name) {
    $name = $getname;
  } else {
    $myname = 1;
  }
} else {
  $myname = 1;
}

$stmt = $db->prepare('SELECT * FROM members WHERE name=?');
$stmt->execute(array($name));
$individual = $stmt->fetch();

$email = $individual['email'];
$picture = $individual['picture']; 
$profile = $individual['profile'];
$twitter = $individual['twitter'];
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<title>ぶいけー</title>
<meata charset="UTF-8">
<meta name="viewport" content="initial-scale=1.0">
<link rel="stylesheet" href="stylesheet.css" media="all">
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
</head>

<body>
  <header>
    <div class="container">
      <div class="header-left">
        <h1 class="logo">ぶいけー</h1>
      </div>
      <div class="wrapper">
        <div class="header-right"><a class="menu" href="logout.php">ログアウト</a></div>
        <div class="header-right"><a class="menu" href="list.php">トップ</a></div>
      </div>  
    </div>
  </header>
  <div class="build">
    <?php if ($myname === 0): ?>  
      <h3>ニックネーム:<?php print $name;?></h3>
      <h3>プロフィール:<?php print $profile; ?></h3>     
      <h3>写真:<?php if ($picture != 0):?><img src="member_picture/<?php print $picture; ?>" width="48" height="48"> <?php endif ;?></h3>
      <?php if ($individual['profile'] !== ''): ?>
        <h3><a href="http://twitter.com/<?php print $twitter;?>"><span class="fa fa-twitter"></span></h3>
      <?php endif; ?>
    <?php endif; ?>
    <?php if ($myname === 1): ?>
      <h3>ニックネーム:<?php print $name;?><a class="h" href="individual/individual_edit.php?edit=name">編集</a></h3>
      <h3>メールアドレス:<?php print $email;?></h3>
      <h3>プロフィール:<?php print $profile; ?><a class="h" href="individual/individual_edit.php?edit=profile">編集</a></h3>
      <h3>写真:<?php if ($picture != 0): ?><img src="member_picture/<?php print $picture; ?>" width="48" height="48" /><?php endif ;?><a class="h" href="individual/individual_edit.php?edit=picture">編集</a></h3>
      <?php if ($twitter != ''): ?>
        <h3><a href="http://twitter.com/<?php print $twitter;?>"><span class="fa fa-twitter"></span></h3>
      <?php endif; ?>
      <h3>Twitter:<a href="individual/individual_edit.php?edit=twitter">Twitter IDの編集</a></h3>
    <?php endif; ?>
  </div>
</body>
</html>