<?php

define('DB_DATABASE','phpkiso');
define('DB_USERNAME','root');
define('DB_PASSWORD','');
define('PDO_DSN','mysql:dbhost=localhost;dbname='.DB_DATABASE.';charset=utf8');

date_default_timezone_set('Japan');

$date = null;
$error_message = array();
$clean = array();
$message_array = array();
//エスケープ処理
function h($s){
  return htmlspecialchars($s,ENT_QUOTES,'UTF-8');
}

session_start();

if(!empty($_POST['btn_submit'])){

  //入力漏れ確認/無ければエスケープ処理
  if(empty($_POST['name'])){
    $error_message[] = '・表示名が入力されていません';
  }else{
    $clean['name'] = h($_POST['name']);
    $_SESSION['name'] = $clean['name'];
  }

  if(empty($_POST['text_data'])){
    $error_message[] = '・投稿内容が入力されていません';
  }else{
    $clean['text_data'] = h($_POST['text_data']);
  }

  if(empty($error_message)){
    //エラー無ければデータベース接続
    try {
      $db = new PDO(PDO_DSN,DB_USERNAME,DB_PASSWORD);
      $db -> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

      $date = date("Y-m-d H:i");
      $sql = "INSERT INTO forum(name,text_data,post_date) VALUES ('$clean[name]','$clean[text_data]','$date')";
      $res = $db->query($sql);
      if($res){
        $_SESSION['success_message'] = '投稿しました';
      }else{
        $error_message[] = '書き込みに失敗しました';
      }
      $db = null;
      //リロードによる多重投稿回避
      header("Location: ./forum.php");

    } catch (PDOException $e) {
      echo $e->getMessage();
      exit;
    }
  }
}
//読み込み処理
try {
  $db = new PDO(PDO_DSN,DB_USERNAME,DB_PASSWORD);
  $db -> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

  $sql = "SELECT name,text_data,post_date FROM forum ORDER BY post_date DESC";
  $res = $db->query($sql);

  if($res){
    //そのままの配列で渡す
    $message_array = $res->fetchAll(PDO::FETCH_ASSOC);
  }

  $db = null;
} catch (PDOException $e) {
  echo $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>掲示板サイト</title>
  <link rel="stylesheet" href="forum.css">
</head>
<body>
  <div class="forum-container">
    <h1>掲示板サイト</h1>
    <?php if(empty($_POST['btn_submit']) && !empty($_SESSION['success_message'])): ?>
      <p class = "success_message"><?php echo $_SESSION['success_message']; ?></p>
      <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    <?php if(!empty($error_message)):?>
      <ul>
        <?php foreach($error_message as $value): ?>
          <li><?php echo $value; ?></li>
        <?php endforeach; ?>
        <ul>
        <?php endif; ?>
        <div class="form">
          <form method="post">
            <h2>表示名</h2>
            <input type="text" name="name" value="<?php if(!empty($_SESSION['name'])){ echo $_SESSION['name']; } ?>">
            <h2>投稿内容</h2>
            <textarea name="text_data" rows="8" cols="80"></textarea>
            <div class="btn_submit">
              <input type="submit" name="btn_submit" value="投稿">
            </div>
          </form>
        </div>
      </div>
      <div class="data_area">
        <?php if(!empty($message_array)): ?>
          <?php foreach ($message_array as $value) :?>
            <article>
              <div class="data-container">
                <h2><?php echo $value['name']; ?></h2>
                <time><?php echo date('Y年m月d日 H:i',strtotime($value['post_date'])); ?></time>
                <p><?php echo $value['text_data']; ?></p>
              </div>
            </article>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </body>
    </html>
