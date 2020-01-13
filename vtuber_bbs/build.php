<?php
session_start();
require('dbconnect.php');

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
// $_SESSION['listcount']++;
// $listnumber = intval($_SESSION['listcount']);


$_POST['newidol'] = $_SESSION['build']['newidol'];
$_POST['newdbnamekana'] = $_SESSION['build']['newdbnamekana'];


// 何もアイドル名が入力されていなかった場合、アイドル名は【総合】と表示される
if ($_POST['newidol'] === ""){
  $_POST['newidol'] = '総合';
}

if ($_POST["newbbs"] !== "" ) {
  $newidol = mb_substr(htmlspecialchars($_POST["newidol"],ENT_QUOTES),0,20);
  $newdbnamekana = mb_substr(htmlspecialchars($_POST["newdbnamekana"],ENT_QUOTES),0,30);
  $id = $member['id'];
  $stmt = $db->query("INSERT INTO list (idol, dbnamekana,member_id) VALUES ('$newidol','$newdbnamekana','$id')");  
  $list = $db->query("SELECT * FROM list WHERE dbnamekana='$newdbnamekana'");
  foreach ($list as $row) {
    $list_id = intval($row['id']);
  }

  $db->query("CREATE TABLE posts$list_id (id INT(11) AUTO_INCREMENT PRIMARY KEY, message TEXT,member_id INT(11), reply_message_id INT(11), created DATETIME,modifide TIMESTAMP)");
  $_SESSION['listcount'] = $listnumber; 
  $db = null;
}else{
$error = "エラーが起こりました。もう一度ログインしなおして下さい";
$login = "login.php";
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
<title>ぶいけー</title>
<meata charset="UTF-8">
<meta name="viewport" content="initial-scale=1.0">
<link rel="stylesheet" href="stylesheet.css" media="all">
</head>

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
  <main class="main_margin">
    <p><?php print $listcount; ?></p>
    <p>新しい掲示板「【<?php print $newidol; ?>】<?php print $newdbnamekana; ?>」を作りました。</p>
    <p><?php print $error; ?></p>
  </main>
</html>

