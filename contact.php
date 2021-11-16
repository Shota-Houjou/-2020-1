<?php
    session_start();
    $mode = 'input';
    $errmessage = array();
    if( isset($_POST['back']) && $_POST['back'] ){
        //何もしない
        } else if( isset($_POST['confirm']) && $_POST['confirm'] ){
            //確認画面
            $_SESSION['item'] = $_POST['item'];
            if( !$_POST['fullname'] ) {
                $errmessage[] = "名前を入力してください";
            } else if( mb_strlen($_POST['fullname']) > 30 ){
                $errmessage[] = "名前は30文字以内で入力して下さい";
            }
            $_SESSION['fullname'] = htmlspecialchars($_POST['fullname'], ENT_QUOTES);

            if( !$_POST['email'] ) {
                $errmessage[] = "Emailアドレスを入力してください";
            } else if( mb_strlen($_POST['email']) > 100 ){
                $errmessage[] = "アドレスは100文字以内で入力して下さい";
            } else if( !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) ){
                $errmessage[] = "メールアドレスが不正です。";
            }
            $_SESSION['email'] = htmlspecialchars($_POST['email'], ENT_QUOTES);

            if( !$_POST['tel'] ) {
                $errmessage[] = "電話番号を入力してください";
            } else if( !preg_match("/^0[0-9]{9,10}\z/", $_POST['tel'] ) ) {
                $errmessage[] = "固定電話の場合は市外局番から入力して下さい。また「-」ハイフンは抜いて記入してください";
            }
            $_SESSION['tel'] = htmlspecialchars($_POST['tel'], ENT_QUOTES);

            if( !$_POST['message'] ){
                $errmessage[] = "お問い合わせ内容を入力してください";
            } else if( mb_strlen($_POST['message']) > 500 ){
                $errmessage[] = "お問い合わせ内容は500文字以内にしてください";
            }
            $_SESSION['message'] = htmlspecialchars($_POST['message'], ENT_QUOTES);     

            if( $errmessage ){
                $mode = 'input';
            } else {
                $mode = 'confirm';
            }
    } else if( isset($_POST['send']) && $_POST['send'] ){
        //送信ボタン押した時
        $message  = "お問い合わせを受け付けました \r\n"
                 . "件名: " . $_SESSION['item'] . "\r\n"
                 . "お名前: " . $_SESSION['fullname'] . "\r\n"
                 . "Email: " . $_SESSION['email'] . "\r\n"
                 . "電話番号:" . $_SESSION['tel'] . "\r\n"
                 . "お問い合わせ内容:\r\n"
                 . preg_replace("/\r\n|\r|\n/", "\r\n", $_SESSION['message']);
                 mail($_SESSION['email'],'お問い合わせありがとうございます',$message);
                 mail('','お問い合わせ有難う御座います。',$message);
        $_SESSION = array();
        $mode = 'send';
    } else {
        $_SESSION['item'] = "";
        $_SESSION['fullname'] = "";
        $_SESSION['email'] = "";
        $_SESSION['tel'] = "";
        $_SESSION['message'] = "";
    }
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
    <title>お問い合わせフォーム</title>
</head>
<body>
    <?php if( $mode == 'input' ){ ?>
        <!--入力画面-->
        <?php
            if( $errmessage ){
                echo '<div style="color:red;">';
                echo implode('<br>', $errmessage );
                echo '</div>';
            }
        ?>
        <form action="./contact.php" method="post">
            件名<select name= "item" value="<?php echo $_SESSION['item'] ?>">
                <option value = "ご意見">ご意見</option>
                <option value = "ご感想">ご感想</option>
                <option value = "その他">その他</option>
                </select><br>
            お名前（必須） <input type="text" name="fullname" value="<?php echo $_SESSION['fullname'] ?>"><br>
            Email（必須） <input type="email" name="email"    value="<?php echo $_SESSION['email'] ?>"><br>
            電話番号（必須） <input type="number" name="tel"   placeholder="012345678910"  value="<?php echo $_SESSION['tel'] ?>"><br>
            お問い合わせ内容（必須）<br>
            <textarea cols="40" rows="8" name="message"><?php echo $_SESSION['message'] ?></textarea><br>
            <input type="submit" name="confirm" value="確認"/>
        </form>
        <?php } else if( $mode == 'confirm' ){ ?>
        <!--確認画面-->
        <form action="./contact.php" method="post">
            件名<?php echo $_SESSION['item'] ?><br>
            お名前（必須）<?php echo $_SESSION['fullname'] ?><br>
            Email（必須）<?php echo $_SESSION['email'] ?><br>
            電話番号（必須）<?php echo $_SESSION['tel'] ?><br>
            お問い合わせ内容（必須）<br>
            <?php echo nl2br($_SESSION['message']) ?><br>
            <input type="submit" name="back" value="戻る"/>
            <input type="submit" name="send" value="送信"/>
        </form>
    <?php } else { ?>
    送信しました。お問い合わせ有難う御座いました。<br>
    <?php } ?>
</body>
</html>