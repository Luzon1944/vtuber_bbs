<?php
require('dbconnect.php');

$dbnamekana =  htmlspecialchars($_GET['dbnamekana'],ENT_QUOTES);

$stmt = $db->query("SELECT * FROM list WHERE dbnamekana='$dbnamekana'");
foreach ($stmt as $row) {
  $listnumber = intval($row['postname']);
}

if (isset($_GET["postname"])) {
  $postname = htmlspecialchars($_GET["postname"], ENT_QUOTES);
  $db->query("DELETE FROM list WHERE postname=$postname");
  $db->query("DROP TABLE posts$listnumber");

  echo "削除しました。<br><br>";
  echo "<a href='list.php'>掲示板へ戻る</a>";
} else {
  echo "削除に失敗しました" ;
}
echo $listnumber;
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
      <div class="header-right"><a href="logout.php">ログアウト</a></div>
      <div class="header-right"><a href="individual.php">マイページ</a></div>
      <div class="header-right"><a href="list.php">トップ</a></div>
    </div>  
  </header>
  <div>
  </body>
</html>