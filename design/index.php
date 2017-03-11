<?php
require('dbconnect.php');
session_start();
 
  
 
   
   if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
    
     $_SESSION['time'] = time();
 
    
     $sql = sprintf('SELECT * FROM `members` WHERE `member_id` = %d',
       mysqli_real_escape_string($db, $_SESSION['id'])
     );
 
     $record = mysqli_query($db, $sql) or die(mysqli_error($db));
     $member = mysqli_fetch_assoc($record);
 
   } else {
    
     header('Location: login.php');
   }
   
   if (!empty($_POST)) {
    if ($_POST['tweet'] != '') {
      $reply_tweet_id = 0;
      if (isset($_POST['reply_tweet_id'])){
        $reply_tweet_id = $_POST['reply_tweet_id'];
      }
      $sql = sprintf('INSERT INTO `tweets` SET `tweet` = "%s", `member_id` = %d, `reply_tweet_id` = %d, `created` = now()',
         mysqli_real_escape_string($db, $_POST['tweet']),
         mysqli_real_escape_string($db, $member['member_id']),
         $reply_tweet_id
       );
      //INSERT文実行
      mysqli_query($db, $sql) or die(mysqli_error($db));
      // SQL実行後、画面を再度表示
      header('Location: index.php');
      exit();
    }
   }
   //返信
   if (isset($_REQUEST['res'])) {
      // 「@返信したいメッセージを書いたユーザー名 返信元メッセージ」を初期表示
      $sql = sprintf('SELECT m.`nick_name`, t.* FROM `tweets` t, `members` m WHERE m.`member_id` = t.`member_id` AND t.`tweet_id` = %d ORDER BY t.`created` DESC',
       mysqli_real_escape_string($db, $_REQUEST['res'])
     );
      //SQL実行
      $record = mysqli_query($db, $sql) or die(mysqli_error($db));
      //データ取得
      $table = mysqli_fetch_assoc($record);
      //初期表示メッセージ作成
      $tweet = '@'. $table['nick_name'].' '.$table['tweet'];
    }
    //ページング処理
    $page = '';
   
   if (isset($_REQUEST['page'])) {
     $page = $_REQUEST['page'];
   }
   
   if ($page == '') {
     $page = 1;
   }
   
   $page = max($page, 1);
   
   if (isset($_GET['search_word']) && !empty($_GET['search_word'])) {
     $sql = sprintf('SELECT COUNT(*) AS cnt FROM `tweets` WHERE `tweet` LIKE "%%%s%%"',
       mysqli_real_escape_string($db, $_GET['search_word'])
     );
   } else {
     $sql = 'SELECT COUNT(*) AS cnt FROM `tweets`';
   }
   $recordSet = mysqli_query($db, $sql) or die(mysqli_error($db));
   $table = mysqli_fetch_assoc($recordSet);
 
   
   $maxPage = ceil($table['cnt'] / 5);
   
   $page = min($page, $maxPage);
   
   $start = ($page - 1) * 5;
   $start = max(0, $start);
   // 検索
   if (isset($_GET['search_word']) && !empty($_GET['search_word'])) {
     $sql = sprintf('SELECT m.`nick_name`, m.`picture_path`, t.* FROM `tweets` t, `members` m WHERE t.`member_id` = m.`member_id` AND t.`tweet` LIKE "%%%s%%" ORDER BY t.`created` DESC LIMIT %d, 5',
         mysqli_real_escape_string($db, $_GET['search_word']),
         $start
      );
   } else {
     // 投稿内容を取得する
     $sql = sprintf('SELECT m.`nick_name`, m.`picture_path`, t.* FROM `tweets` t, `members` m WHERE t.`member_id` = m.`member_id` ORDER BY t.`created` DESC LIMIT %d, 5',
         $start
       );
   }
   $tweets = mysqli_query($db, $sql) or die(mysqli_error($db));
  // 本文内のURLにリンク設定
  function makeLink($value) {
    return mb_ereg_replace("(https?)(://[[:alnum:]\+\$\;\?\.%,!#~*/:@&=_-]+)", '<a href="\1\2">\1\2</a>' , $value);
  }


function h($value){
  return htmlspecialchars($value,ENT_QUOTES,'UTF-8');
}

?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>SeedSNS</title>

    <!-- Bootstrap -->
    <link href="../assets/css/bootstrap.css" rel="stylesheet">
    <link href="../assets/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="../assets/css/form.css" rel="stylesheet">
    <link href="../assets/css/timeline.css" rel="stylesheet">
    <link href="../assets/css/main.css" rel="stylesheet">


    
  </head>
  <body>
  <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
          
          <div class="navbar-header page-scroll">
              <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                  <span class="sr-only">Toggle navigation</span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="index.php"><span class="strong-title"><i class="fa fa-twitter-square"></i>SNS SRVICE</span></a>
          </div>
          
          <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
              <ul class="nav navbar-nav navbar-right">
                <li><a href="logout.php">ログアウト</a></li>
              </ul>
          </div>
          
      </div>
      
  </nav>

  <div class="container">
    <div class="row">
      <div class="col-md-4 content-margin-top">
        <legend>ようこそ<?php echo h($member['nick_name']); ?>さん！</legend>
        <form method="post" action="" class="form-horizontal" role="form">
            <!-- つぶやき -->
            <div class="form-group">
              <label class="col-sm-4 control-label">つぶやき</label>
              <div class="col-sm-8">
                <?php if (isset($tweet)): ?>
                <textarea name="tweet" cols="50" rows="5" class="form-control" placeholder="例：Hello World!"><?php echo h($tweet); ?></textarea>
                <input type="hidden" name="reply_tweet_id" value="<?php echo h($_REQUEST['res']); ?>">
                <?php else: ?>
                  <textarea name="tweet" cols="50" rows="5" class="form-control" placeholder="例：Hello World!"></textarea>
                <?php endif ?>

              </div>
            </div>
          <ul class="paging">
             <?php
               $word = '';
               if (isset($_GET['search_word'])) {
                 $word = '&search_word=' . $_GET['search_word'];
               }
             ?>
            <input type="submit" class="btn btn-info" value="つぶやく">
                &nbsp;&nbsp;&nbsp;&nbsp;
                <?php if ($page == 1){ ?>
                <li>前</li>

               <?php }else{ ?>
                <li><a href="index.php?page=<?php echo $page -1; ?><?php echo $word; ?>" class="btn btn-default">前</a></li>

                <?php } ?>
                &nbsp;&nbsp;|&nbsp;&nbsp;
                <?php if ($page == $maxPage){ ?>
                  <li>次</li>

                <?php }else{ ?>
                <li><a href="index.php?page=<?php echo $page +1; ?><?php echo $word; ?>" class="btn btn-default">次</a></li>
                <?php } ?>
          </ul>
        </form>
      </div>

      <div class="col-md-8 content-margin-top">
      
         <form action="" method="get" class="form-horizontal" role="form">
           <?php if (isset($_GET['search_word']) && !empty($_GET['search_word'])): ?>
             <input type="text" name="search_word" value="<?php echo $_GET['search_word']; ?>">
           <?php else: ?>
             <input type="text" name="search_word" value="">
           <?php endif ?>
           <input type="submit" class="btn btn-success btn-xs" value="検索">
         </form>
        
        <?php while($tweet = mysqli_fetch_assoc($tweets)): ?>

        <div class="msg">
          <img src="member_picture/<?php echo h($tweet['picture_path'], ENT_QUOTES, 'UTF-8'); ?>" width="48" height="48">
          <p>
            <?php echo makeLink(h($tweet['tweet'])); ?><span class="name"> (<?php echo htmlspecialchars($tweet['nick_name'], ENT_QUOTES, 'UTF-8'); ?>) </span>
            [<a href="index.php?res=<?php echo h($tweet['tweet_id']); ?>">Re</a>]
          </p>
          <p class="day">
            <a href="view.php?tweet_id=<?php echo $tweet['tweet_id']; ?>">
              <?php echo h($tweet['created']); ?>
            </a>
            <?php if ($_SESSION['id'] == $tweet['member_id']): ?>
             [<a href="edit.php?tweet_id=<?php echo $tweet['tweet_id']; ?>" style="color: #00994C;">編集</a>]
            [<a href="delete.php?tweet_id=<?php echo $tweet['tweet_id']; ?>" style="color: #F33;">削除</a>]
            <?php endif ?>
          </p>
        </div>
        <?php endwhile ?>


    </div>
  </div>

    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>