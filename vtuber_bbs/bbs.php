<?php
session_start();
require('dbconnect.php');

// listからgetを受け取る
$dbnamekana = htmlspecialchars( $_GET['dbnamekana'],ENT_QUOTES);
$idol = htmlspecialchars( $_GET['idol'],ENT_QUOTES); 

// 上記の値をエンコード
$dbnamekana = urldecode($dbnamekana);
$idol = urldecode($idol);
// post

// 一時間操作されてなければログインページに戻るmember
if(isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
  $_SESSION['time'] = time();
  $members = $db->prepare('SELECT * FROM members WHERE id=?');
  $members->execute(array($_SESSION['id']));
  $member = $members->fetch();
} else {
  header('Location: login.php');
  exit();
}

// postnameを$listnumberに格納
$stmt = $db->query("SELECT * FROM list WHERE dbnamekana='$dbnamekana'");
foreach ($stmt as $row) {
  $listnumber = intval($row['id']);
}

// postで受け取ったメッセージをデータベースへ格納
if (!empty($_POST)) {
  if ($_POST['message'] !=='') {
    $message = $db->prepare("INSERT INTO posts$listnumber SET member_id=?,message=?,reply_message_id=?,created=NOW()");
    $message->execute(array(
      $member['id'],
      $_POST['message'],
      $_POST['reply_post_id']
    ));
    header("Location:bbs.php?idol=$idol&dbnamekana=$dbnamekana");   
    exit();
  }
}

// ページング
// $pageがない場合、$page=1とする
$page = $_REQUEST['page'];
if ($page == '') {
  $page = 1;
}
$page = max($page,1);

// 画面に10レスだけ表示させる
// $k_count = このスレッドのレスの数
$counts = $db->query("SELECT COUNT(*) as cnt FROM posts$listnumber");
$cnt = $counts->fetch(); 
$k_count = $cnt['cnt'];
$maxPage = ceil($k_count / 10);
$page = min($page,$maxPage);

// レスの上限(1000)
$zyougen = null;
if ($k_count>=1000) {
  $zyougen_message = "このスレッドは上限に達しました。これ以上書き込めません";
  $zyougen = 1;
}


// 件数を更新してデータベースへ
$stmt = $db->query("UPDATE list SET k_number=$k_count WHERE dbnamekana='$dbnamekana'");

$start = ($page - 1) * 10;  

// ページングされているレスリスト
$posts = $db->prepare("SELECT m.name,m.picture,p.*FROM members m,posts$listnumber p WHERE m.id=p.member_id ORDER BY p.created DESC LIMIT ?,10 ");
$posts->bindParam(1,$start,PDO::PARAM_INT);
$posts->execute();


// 返信機能
// 返信->textboxに相手の名前とレスを表示
if ($zyougen === null){
  if (isset($_REQUEST['res'])) {
    $response = $db->prepare("SELECT m.name,m.picture,p.* FROM members m,posts$listnumber p WHERE m.id=p.member_id AND p.id=?");
    $response->execute(array($_REQUEST['res']));
    $table = $response->fetch();
    $message = '>>' .$table['name'];
  }
}

$html = "【$idol 】$dbnamekana";

?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>ぶいけー</title>

	<link rel="stylesheet" href="stylesheet.css" />
</head>

<body>
  <header>
    <div>
      <div class="header-left">
        <h1 class="logo">ぶいけー</h1>
      </div>
      <div class="wrapper">
        <div class="header-right"><a class="menu" href="logout.php">ログアウト</a></div>
        <div class="header-right"><a class="menu" href="individual.php">マイページ</a></div>
        <div class="header-right"><a class="menu" href="list.php">トップ</a></div>
      </div>  
    </div>
  </header>
  <div class="lesson-wrapper">
    <form action="" method="post">
      <dl style="margin-top:0;">
      <?php if($zyougen === null): ?>
        <h2><?php print $html ;?></h2>
        <dt><?php print(htmlspecialchars($member['name'],ENT_QUOTES)); ?>さん、メッセージをどうぞ</dt>
        <dd>
          <textarea name="message" cols="50" rows="5"><?php print (mb_substr(htmlspecialchars($message,ENT_QUOTES),0,30)); ?></textarea>
          <input type="hidden" name="reply_post_id" value="<?php print(htmlspecialchars($_REQUEST['res'],ENT_QUOTES)); ?>" />
      </dl>
        <p>
          <input type="submit" value="投稿する" class="shadow" style="margin-left:45px;">
        </p>
      </div>
      </dd>
      <?php else: ?>
        <dt><?php print $zyougen; ?></dt>
      <?php endif;  ?>
    </form>
  <div class="message-wrapper-bbs">
    <div class="text-box">
    <?php foreach ($posts as $post): ?>
      <div class="msg">
      <?php
        $p_picture = htmlspecialchars($post['picture'],ENT_QUOTES);
        $p_name = htmlspecialchars($post['name'],ENT_QUOTES);
        $p_message = htmlspecialchars($post['message'],ENT_QUOTES);
        $p_id = htmlspecialchars($post['id'],ENT_QUOTES);
        $p_created = htmlspecialchars($post['created'],ENT_QUOTES);
        $url_p_name = urlencode($p_name);
      ?>
      <?php if ($p_picture != 0):?>
        <img src="member_picture/<?php print $p_picture; ?>" width="48" height="48" alt="<?php print $p_name; ?>" style="float:left;">
      <?php endif; ?>
      <p><?php print $p_message; ?><span class="name">(<a href="individual.php?dbnamekana=<?php print $p_name; ?>"><?php print $p_name; ?></a>）</span>[<a href="bbs.php?idol=<?php print $idol; ?>&dbnamekana=<?php print $dbnamekana; ?>&res=<?php print $p_id; ?>">返信</a>]</p>
      <p class="day"><a href="view.php?idol=<?php print $idol; ?>&dbnamekana=<?php print $dbnamekana; ?>&id=<?php print $p_id; ?>"><?php print $p_created; ?></a>
      <?php if ($post['reply_message_id'] > 0): ?>
        <a href="view.php?idol=<?php print $idol; ?>&dbnamekana=<?php print $dbnamekana; ?>&id=<?php print(htmlspecialchars($post['reply_message_id'],ENT_QUOTES)); ?>">
        返信元のメッセージ</a>
      <?php endif ; ?>
    <?php if ($_SESSION['id'] == $post['member_id']): ?>
      [<a href="delete.php?list_id=<?php print $listnumber; ?>&id=<?php print $p_id; ?>"
      class="error">削除</a>]
      <?php endif ; ?>
      </p>
      </div>
    <?php endforeach; ?>
<ul class="paging">
<?php if($page > 1): ?>
  <li><a href="bbs.php?idol=<?php print $idol;?>&dbnamekana=<?php print $dbnamekana ; ?>&page=<?php print($page-1); ?>">前のページへ</a></li>
<?php else: ?>
  <li>前のページへ</li>
<?php endif; ?>
<?php if($page < $maxPage): ?>
  <li><a href="bbs.php?idol=<?php print $idol ;?>&dbnamekana=<?php print $dbnamekana ; ?>&page=<?php print($page+1); ?>">次のページへ</a></li>
<?php else: ?>
  <li>次のページ</li>
<?php endif; ?>
</ul>
  </div>
</div>

</body>
</html>
