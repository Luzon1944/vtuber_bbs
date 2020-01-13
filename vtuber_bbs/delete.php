<?php 
session_start();
require('dbconnect.php');

$postname = htmlspecialchars($_GET['list_id']);
$id = htmlspecialchars($_REQUEST['id'],ENT_QUOTES);

if (isset($_SESSION['id'])) {
  $message = $db->prepare("SELECT * FROM posts$postname WHERE id=?");
  $message->execute(array($id));
  $message = $message->fetch();

  if ($message['member_id'] == $_SESSION['id']) {
    $del = $db->prepare("DELETE FROM posts$postname WHERE id=?");
    $del->execute(array($id));
  }
  $list = $db->query("SELECT * FROM list WHERE id=$postname");
  $lists = $list->fetch();

  header("Location:bbs.php?idol=".$lists['idol']."&dbnamekana=".$lists['dbnamekana']);
  exit();
} else {
  header('Location: list.php');
  exit();
}
?>