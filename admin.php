<?php

//管理者ページパスワード
define('PASSWORD','sumple');

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

if(!empty($_GET['logout'])){
  unset($_SESSION['admin_login']);
}

if(!empty($_POST['btn_submit'])){
  if(!empty($_POST['login-password']) && $_POST['login-password'] === PASSWORD){
    $_SESSION['admin_login'] =true;
  }else{
    $error_message[] = 'ログインに失敗しました';
  }
}

try{
  //読み込みの処理内容
  $db = new PDO(PDO_DSN,DB_USERNAME,DB_PASSWORD);
  $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

  $sql = "SELECT id,name,text_data,post_date FROM forum ORDER BY post_date DESC";
  $res = $db->query($sql);

  if($res){
    $message_array = $res->fetchAll(PDO::FETCH_ASSOC);

    $db = null;
  }
}catch(PDOException $e){
  echo $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>掲示板管理ページ</title>
  <link rel="stylesheet" href="forum.css">
</head>
<body>
  <h1>掲示板管理ページ</h1>
  <?php if(!empty($error_message)):?>
    <ul>
      <?php foreach($error_message as $value): ?>
        <li><?php echo $value; ?></li>
      <?php endforeach; ?>
      <ul>
      <?php endif; ?>
      <?php if(!empty($_SESSION['admin_login']) && $_SESSION['admin_login'] === true): ?>
        <div class="data_area">
          <?php if(!empty($message_array)): ?>
            <?php foreach ($message_array as $value) :?>
              <article>
                <div class="data-container">
                  <h2><?php echo $value['name']; ?></h2>
                  <time><?php echo date('Y年m月d日 H:i',strtotime($value['post_date'])); ?></time>
                  <span><a href="edit.php?text_id=<?php echo $value['id']; ?>">編集</a>　<a href="delete.php?text_id=<?php echo $value['id']; ?>">削除</a></apan>
                    <p><?php echo $value['text_data']; ?></p>
                  </div>
                </article>
              <?php endforeach; ?>
            <?php endif; ?>
            <div class="log-out">
              <form method="get" action="">
                <input type="submit" name="logout" value="ログアウト">
              </form>
            </div>
          <?php else: ?>
            <!-- ログインフォーム -->
            <div  class="login">
              <form  method="post">
                <div>
                  <label for="admin_password">ログインパスワード</label>
                  <input id="admin_password" type="password" name="login-password">
                </div>
                <input type="submit" name="btn_submit" value="ログイン">
              </form>
            </div>
          <?php endif; ?>
        </div>
      </body>
      </html>
