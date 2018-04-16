<?php

$dateFile = 'bbs.dat';

session_start();

function setToken(){
    $token = sha1(uniqid(mt_rand(),true));
    $_SESSION['token'] = $token;
}

function checkToken(){
    if(empty($_SESSION['token']) || ($_SESSION['token'] != $_POST['token'])){
        echo "不正なPOSTが行われました!";
        exit;
    }

}

function h($s){
    return htmlspecialchars($s,ENT_QUOTES,'UTF-8');

}

if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['message']) && isset($_POST['user'])){

    checkToken();

    $message = trim($_POST['message']);
    $user = trim($_POST['user']);


    if($message !== ''){

        $user = ($user === '') ?'名無しのAさん' :$user;

        $message =str_replace("\t", ' ',$message);
        $user =str_replace("\t", ' ',$user);
        $postesAt=date('Y-m-d H:i:s');


        $newDate = $message ."\t".$user."\t".$postesAt."\n";


        $fp =fopen($dateFile,'a');
        fwrite($fp,$newDate);
        fclose($fp);
    }
} else{
    setToken();
}

$posts=file($dateFile,FILE_IGNORE_NEW_LINES);

$posts = array_reverse($posts);

?>



    <!DOCTYPE html>
    <html lang="ja">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!-- Bootstrap -->
        <link rel="stylesheet" media="screen" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.5/css/bootstrap.min.css">
        <title>簡易掲示板</title>
    </head>

    <body>
        <div class="container">
            <h1>簡易掲示板</h1>
            <form action="" method="post">
                message:
                <input type="text" name="message"> user :
                <input type="text" name="user">
                <input type="submit" value="投稿">
                <input type="hidden" name="token" value="<?php echo h($_SESSION['token']); ?>" >
                <h4>userに何も入力が無ければ「名無しのAさん」で投稿されます。</h4>

            </form>
            <h2>投稿一覧 (<?php echo count($posts); ?>件)</h2>
            <ul>
                <?php if (count($posts)) : ?>
                    <?php foreach ($posts as $post) :?>
                       <?php list($message,$user,$postesAt)=explode("\t",$post); ?>

                       <li><?php echo h($message); ?> <?php echo h($user); ?>-<?php echo h($postesAt); ?></li>

                    <?php endforeach; ?>

                <?php else : ?>
                    <li>投稿はまだありません</li>
                <?php endif; ?>
            </ul>
        </div>


    </body>

    </html>
