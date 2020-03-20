<?php

define('DB_DATABASE','phpkiso');
define('DB_USERNAME','root');
define('DB_PASSWORD','');
define('PDO_DSN','mysql:dbhost=localhost;dbname='.DB_DATABASE.';charset=utf8');

$message_id = null;
$mysqli = null;
$sql = null;
$res = null;
$error_message = array();
$message_data = array();

date_default_timezone_set('Japan');

function h($s){
  return htmlspecialchars($s,ENT_QUOTES,'UTF-8');
}


session_start();

if(empty($_SESSION['admin_login']) || $_SESSION['admin_login']!==true){
  //ログインページにリダイレクト
  header("location: ./admin.php");
}
if(!empty($_GET['text_id']) && empty($_POST['text_id'])){
  //投稿取得
  $message_id = (int)h($_GET['text_id'],ENT_QUOTES);

  //データベース接続
  try {
    $db = new PDO(PDO_DSN,DB_USERNAME,DB_PASSWORD);
    $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT * FROM forum WHERE id=$message_id";
    $res = $db->query($sql);

    if($res){
      $message_data = $res->fetch(PDO::FETCH_ASSOC);
    }else{
      header("Location: ./admin.php");
    }

    $db = null;

  } catch (PDOException $e) {
    echo $e->getMessage();
  }
}elseif(!empty($_POST['text_id'])){
  $message_id = (int)h($_POST['text_id'],ENT_QUOTES);

  //データベース保存処理
  $db = new PDO(PDO_DSN,DB_USERNAME,DB_PASSWORD);
  $db -> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

  try {
    $sql = "DELETE FROM forum WHERE id=$message_id";
    $res = $db->query($sql);

    $db = null;
    if($res){
      header("Location: ./admin.php");
    }

  } catch (PDOException $e) {
    echo $e->getMessage();
  }

}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>なんでも掲示板・削除ページ</title>
  <link rel="stylesheet" href="forum.css">
</head>
<body>
  <div class="data_area">
    <h1>なんでも掲示板・削除ページ</h1>
    <?php if(!empty($error_message)): ?>
      <ul class="error_message">
        <?php foreach($error_message as $value): ?>
          <li><?php echo $value; ?></li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
    <p class="text-confirm">以下の投稿を削除します。<br>よろしければ「削除」ボタンを押してください。</p>
    <form method="post">
      <h2>表示名</h2>
      <input type="text" name="name" value="<?php if(!empty($message_data['name'])){ echo $message_data['name'];} ?>" disabled>
      <h2>内容</h2>
      <textarea name="text_data" rows="8" cols="80" disabled><?php if(!empty($message_data['text_data'])){ echo $message_data['text_data'];} ?></textarea>
      <div class="submit-btn">
        <input type="submit" name="btn_submit" value="削除">
        <input type="hidden" name="text_id" value="<?php echo $message_data['id']; ?>">
      </div>
      <a class="btn_cancel" href="admin.php">キャンセル</a>
    </form>
  </div>
</body>
</html>
