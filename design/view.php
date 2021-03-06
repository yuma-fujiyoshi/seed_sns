<?php 

session_start();
require('dbconnect.php');

if(empty($_REQUEST['tweet_id'])){
  header('Location: index.php');
  exit();
}

// 投稿を取得する
$sql=sprintf('SELECT m.`nick_name`,m.`picture_path`,t. * FROM members m,tweets,t WHERE 
  m.`member_id`=t.`member_id` AND t.`tweet_id`=%d ORDER BY t.`created` DESC',
  mysqli_real_escape_string($db,$_REQUEST['tweet_id']));

$tweets=mysqli_query($db,$sql) or die(mysqli_error($db));


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
              <a class="navbar-brand" href="index.html"><span class="strong-title"><i class="fa fa-twitter-square"></i>SNS SERVICE</span></a>
          </div>
         
          <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
              <ul class="nav navbar-nav navbar-right">
                <li><a href="logout.html">ログアウト</a></li>
              </ul>
          </div>
          
      </div>
     

  <div class="container">
    <div class="row">
      <div class="col-md-4 col-md-offset-4 content-margin-top">
      <?php if($tweet=mysqli_fetch_assoc($tweets)): ?>
        <div class="msg">
          <img src="member_picture/<?php echo h($tweet['picture_path']); ?>" width="48" height="48" alt="<?php echo h($tweet['nick_name']); ?>">
          <p>
            <?php echo h($tweet['tweet']); ?><span class='name'>
            (<?php echo h($twet['nick_name']); ?>)</span>
            [<a href="index.php?res=<?php echo h($tweet['tweet_id']); ?>">Re</a>]
          </p>
        </div>
      <?php else: ?>
        <p>その投稿は削除されたかURLが間違っています</p>
      <?php endif ?>
        <a href="index.php">&laquo;&nbsp;一覧へ戻る</a>

      </div>
    </div>
  </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>
