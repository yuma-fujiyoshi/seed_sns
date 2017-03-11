<?php
  
  session_start();
  require('../dbconnect.php');
  
  $error = array();
  if (isset($_POST) && !empty($_POST)){
    
    if (empty($_POST['nick_name'])){
      
      $error['nick_name'] = 'blank';
    }
    
    if (empty($_POST['email'])){
      
      $error['email'] = 'blank';
    }
    
    if (empty($_POST['password'])){
      
      $error['password'] = 'blank';
    }elseif(strlen($_POST['password']) < 4){
     
      $error['password'] = 'length';
    }
    
     $fileName = $_FILES['picture_path']['name'];
     if (!empty($fileName)) {
       $ext = substr($fileName, -3);
       if ($ext != 'jpg' && $ext != 'gif' && $ext != 'png') {
         $error['picture_path'] = 'type';
       }
     }

      if (empty($error)){
      
      $picture_path = date('YmdHis') . $_FILES['picture_path']['name'];
      move_uploaded_file($_FILES['picture_path']['tmp_name'], '../member_picture/' . $picture_path);
      
      $_SESSION['join'] = $_POST;
      $_SESSION['join']['picture_path'] = $picture_path;
      
      header('Location:check.php');
      exit();
      }
     
     
      
    
    
  }
  
  if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'rewrite') {
     $_POST = $_SESSION['join'];
     
     $error['rewrite'] = true;
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

    <title>SNS SERVICE</title>

    <!-- Bootstrap -->
    <link href="../../assets/css/bootstrap.css" rel="stylesheet">
    <link href="../../assets/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="../../assets/css/form.css" rel="stylesheet">
    <link href="../../assets/css/timeline.css" rel="stylesheet">
    <link href="../../assets/css/main.css" rel="stylesheet">
    
  </head>
  <body>
  <nav class="navbar navbar-default navbar-fixed-top"　height="150px">
      <div class="container">
          
          <div class="navbar-header page-scroll">
              <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                  <span class="sr-only">Toggle navigation</span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="index.php"><span class="strong-title"><i class="fa fa-twitter-square"></i>SNS　SERVIICE</span></a>
          </div>
          
          <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
              <ul class="nav navbar-nav navbar-right">
                <li><a href="../login.php">ログイン画面へ</a></li>
              </ul>
          </div>

          <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
              <ul class="nav navbar-nav navbar-right">
              </ul>
          </div>
          
      </div>
      
  </nav>
  

  <div class="container">
    <div class="row">
      <div class="col-md-6 col-md-offset-3 content-margin-top">
        <legend>会員登録</legend>
        <form method="post" action="" class="form-horizontal" enctype="multipart/form-data">
         
          <div class="form-group">
            <label class="col-sm-4 control-label">ニックネーム</label>
            <div class="col-sm-8">
              <?php if(isset($_POST['nick_name'])){ ?>
                <input type="text" name="nick_name" class="form-control" placeholder="例： Seed kun" value="  <?php echo h($_POST['nick_name']); ?>">
              <?php }else{ ?>
                <input type="text" name="nick_name" class="form-control" placeholder="例： Seed kun" >
              <?php } ?>
              <?php if(isset($error['nick_name']) && $error['nick_name']=='blank'): ?>
                <p class="error">ニックネームを入力してください</p>
              <?php endif ?>
            </div>
          </div>
        
          
          <div class="form-group">
            <label class="col-sm-4 control-label">メールアドレス</label>
            <div class="col-sm-8">
              <?php if(isset($_POST['email'])){ ?>
                <input type="email" name="email" class="form-control" placeholder="例： seed@nex.com" value=  "<?php echo h($_POST['email']); ?>">
              <?php }else{ ?>
                <input type="email" name="email" class="form-control" placeholder="例： seed@nex.com">
              <?php } ?> 
              <?php if(isset($error['email']) && $error['email']=='blank'): ?>
                  <p class="error">メールアドレスを入力してください</p>
              <?php endif ?>
             
               
            </div>            
          </div>
          
          <div class="form-group">
            <label class="col-sm-4 control-label">パスワード</label>
            <div class="col-sm-8">
              <?php if(isset($_POST['password'])){ ?>
                <input type="password" name="password" class="form-control" placeholder="" value="<?php echo h($_POST['password']); ?>">
              <?php }else{ ?>
                <input type="password" name="password" class="form-control" placeholder="">
              <?php } ?> 
              <?php if(isset($error['password']) && $error['password']=='blank'){ ?>
                <p class="error">パスワードを入力してください</p>
              <?php }elseif(isset($error['password']) && strlen($_POST['password'])<4){ ?>
                <p class="error">4文字以上入力してください</p>
              <?php } ?>
            </div>
          </div>
          
          <div class="form-group">
            <label class="col-sm-4 control-label">プロフィール写真</label>
            <div class="col-sm-8">
              <input type="file" name="picture_path" class="form-control">
                <?php if(isset($error['picture_path']) && ($error['picture_path']=='type')): ?>
                  <p class='error'>写真などは「.gif」または「.jpg」または「.png」の画像を指定してください</p>
                <?php endif ?>
                <?php if(!empty($error)): ?>
                  <p class='error'>恐れ入りますが画像を改めて指定してください</p>
                <?php endif ?>
            </div>
          </div>

          <input type="submit" class="btn btn-info" value="確認画面へ">
        </form>
      </div>
    </div>
  </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>
