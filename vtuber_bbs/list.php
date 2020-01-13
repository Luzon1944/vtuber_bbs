<!-- トップ画面 -->
<?php
session_start();
require('dbconnect.php');

// 一時間ページを移動しなければ、ログインページに戻されるlist
if(isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
  $_SESSION['time'] = time();
  $members = $db->prepare('SELECT * FROM members WHERE id=?');
  $members->execute(array($_SESSION['id']));
  $member = $members->fetch();
} else {
  header('Location: login.php');
  exit();
}

$i = 1;
$html = "";
// $listcount＝スレッドの数

// スレッド名が記入されていない場合エラーを出す
if (!empty($_POST)){
  if ($_POST['newdbnamekana'] === "") {
    $error['newdbnamekana'] = 'blank';
  }  
} 
// スレッド名の重複エラー
if (!empty($_POST)){
  if(empty($error)) {
    $member = $db->prepare('SELECT COUNT(*) AS cnt FROM list WHERE dbnamekana=?');
    $member->execute(array($_POST['newdbnamekana']));
    $record = $member->fetch();
    if ($record['cnt'] > 0) {
      $error['newdbnamekana'] = 'duplicate';
      }
    }
  }
  
if (!empty($_POST)){
  if (empty($error)) {
    $_SESSION['build'] = $_POST;
    header('Location: build.php');
    exit();
  }
}



// 掲示板の数を取得
$stmt = $db->query("SELECT COUNT(*) FROM list");
$listcount = $stmt->fetchColumn();
$stmt->closeCursor();

$_SESSION['listcount'] = $listcount;

// ページング
// $pageがない場合、$page=1とする
$page = $_REQUEST['page'];
if ($page == '') {
  $page = 1;
}
$page = max($page,1);

$k_count = $listcount;
$maxPage = ceil($k_count / 10);
$page = min($page,$maxPage);

$start = ($page - 1) * 10;  

// スレッドを表示する
// 検索がなされた場合
// k_number=レスの件数
$list_search = htmlspecialchars($_POST['list_search'],ENT_QUOTES);
if ($list_search !== "") {
  $stmt = $db->query("SELECT * FROM list WHERE idol LIKE '%$list_search%'  ORDER BY updtime DESC");
  $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
  foreach ($list as $row) {
    $html .="<a class='listbox' href='bbs.php?idol={$row["idol"]}&dbnamekana={$row["dbnamekana"]}'>".$i.":【".$row["idol"]."】".$row["dbnamekana"]." (".$row['k_number'].") ".$row['updtime']."</a>"."<br><br>";
    $i++;
  }
  // 検索がなされなかった場合$
}else{
  if ($listcount > 0) {  
    $stmt = $db->prepare("SELECT * FROM list ORDER BY updtime DESC LIMIT ?,10 ");
    $stmt->bindParam(1,$start,PDO::PARAM_INT);
    $stmt->execute();
    foreach ($stmt as $row) {
      $row["idol"] = urldecode($row["idol"]);
      $html .="<a class='listbox' href='bbs.php?idol={$row["idol"]}&dbnamekana={$row["dbnamekana"]}'>".$i.":【".$row["idol"]."】".$row["dbnamekana"]." (".$row['k_number'].") ".$row['updtime']."</a>"."<br><br>";
      $i++;
    }
  }
}

$db = null;
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
      </div>  
  </header>
  <div class="lesson-wrapper">
    <div class="textbox">
      <p>
        <form method="POST" action="" style="padding:20px 20px;">
          <div>名前で検索：<input type="text" name="list_search"></div>
          <input class="shadow" type="submit" value="検索">                           
        </form>
      </p>
    </div>
    <div class="textbox">
      <?php if ($listcount < 100): ?>
        <form action="" method="post" style="padding:20px 20px;">
          名前：<input type="text" name="newidol" value=""><br>
          スレッド名：<input type="text" name="newdbnamekana" value="">
          <?php if ($error['newdbnamekana'] === 'blank'): ?>
            <p class="error">スレッド名を入力してください</p>
          <?php endif; ?>
          <?php if ($error['newdbnamekana'] === 'duplicate'): ?>
					  <p class="error">このスレッド名は、既に使用されています</p>
					<?php endif; ?>
          <br>
          <input class="shadow" type="submit" name="newbbs" value="新しく掲示板を立てる">
        </form>
      <?php else: ?>
        <p>これ以上スレッドは立てられません。</p>
      <?php endif; ?>
      
    </div>
  </div>
  <h3>スレッド一覧</h3>
  <div class="message-wrapper">
    <?php echo $html; ?>
<ul>
  <?php if($page > 1): ?>
    <li><a href="list.php?page=<?php print($page-1); ?>">前のページへ</a></li>
  <?php else: ?>
    <li>前のページへ</li>
  <?php endif; ?>
  <?php if($page < $maxPage): ?>
    <li><a href="list.php?page=<?php print($page+1); ?>">次のページへ</a></li>
  <?php else: ?>
    <li>次のページ</li>
  <?php endif; ?>
  <?php print $maxpage;?>
</ul>
<?php print $n;?>
  </div>
</body>
</html>