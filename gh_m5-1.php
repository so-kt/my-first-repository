<?php
        // DB接続設定
    $dsn = '**********';
    $user = '**********';
    $password = '**********';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
        //テーブル作成
    $sql = "CREATE TABLE IF NOT EXISTS tb5_1"
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name char(32),"
    . "comment TEXT,"
    . "pass char(16),"
    . "date char(32)"
    .");";
    $stmt = $pdo->query($sql);
        //入力データのチェック
    if(isset($_POST["n_submit"]) && isset($_POST["name"]) && isset($_POST["comment"]) && isset($_POST["passA"]) && empty($_POST["e_num2"])){
        $name=$_POST["name"];$comment=$_POST["comment"];
        $pass=$_POST["passA"];$date=date("Y/m/d H:i:s");
        $pe_checks=array();
        if($name==""){$pe_checks[]="名前";}
        if($comment==""){$pe_checks[]="コメント";}
        if($pass==""){$pe_checks[]="パスワード";}
    }
        //入力データの記録
    if(isset($pe_checks) && empty($pe_checks)){
        $sql=$pdo -> prepare("INSERT INTO tb5_1 (name, comment, pass, date) VALUES (:name, :comment, :pass, :date)");
        $sql -> bindParam(':name', $name, PDO::PARAM_STR);
        $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
        $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
        $sql -> bindParam(':date', $date, PDO::PARAM_STR);
        $sql -> execute();
    }
        //削除前チェック
    if(isset($_POST["d_submit"]) && isset($_POST["d_num"]) && isset($_POST["passB"])){
        $d_num=$_POST["d_num"];$pass=$_POST["passB"];
        $stmt = $pdo->prepare('SELECT id,pass FROM tb5_1 WHERE id =:id');
        $stmt -> bindParam(':id',$d_num, PDO::PARAM_INT);
        $stmt -> execute();
        $d_result=$stmt -> fetch();
        function dn_check($d_result){
            if(empty($d_result)){
                return "正しい番号を入力してください";}
        }
    }
        //削除前パス確認
    if(!empty($d_result)){
        $d_pass=$d_result["pass"];
        if($d_pass!==$pass){$can_del=0;}
        else{$can_del=1;}
    }
        //削除処理
    if(!empty($can_del)){
        $sql='DELETE FROM tb5_1 WHERE id=:id';
        $stmt=$pdo->prepare($sql);
        $stmt->bindParam(':id',$d_num,PDO::PARAM_INT);
        $stmt->execute();
    }
        //編集1 前チェック
    if(isset($_POST["e_submit"]) && isset($_POST["e_num"]) && isset($_POST["passC"])){
        $e_num=$_POST["e_num"];$pass=$_POST["passC"];
        $stmt = $pdo->prepare('SELECT id,name,comment,pass FROM tb5_1 WHERE id =:id');
        $stmt -> bindParam(':id',$e_num, PDO::PARAM_INT);
        $stmt -> execute();
        $e_result=$stmt -> fetch();
        function en_check($e_result){
            if(empty($e_result)){
                return "正しい番号を入力してください";}
        }
    }
        //編集1 内容をvalueへ＆パスチェック
    if(!empty($e_result)){
        $v_enum=$e_result["id"];$v_name=$e_result["name"];
        $v_comment=$e_result["comment"];$v_pass=$e_result["pass"];
        if($v_pass==$pass){$can_edit=1;}
        else{$can_edit=0;}
    }
        //編集2 前チェック
    if(isset($_POST["n_submit"]) && isset($_POST["name"]) && isset($_POST["comment"]) && isset($_POST["passA"]) && !empty($_POST["e_num2"])){
        $name=$_POST["name"];$comment=$_POST["comment"];
        $pass=$_POST["passA"];$date=date("Y/m/d H:i:s");
        $e_num2=$_POST["e_num2"];
        $ee_checks=array();
        if($name==""){$ee_checks[]="名前";}
        if($comment==""){$ee_checks[]="コメント";}
        if($pass==""){$ee_checks[]="パスワード";}
    }
        //編集2 update文による上書き
    if(isset($ee_checks) && empty($ee_checks)){
        $sql='UPDATE tb5_1 SET name=:name,comment=:comment,pass=:pass,date=:date WHERE id=:id';
        $stmt=$pdo->prepare($sql);
        $stmt->bindParam(':id',$e_num2,PDO::PARAM_INT);
        $stmt->bindParam(':name',$name,PDO::PARAM_STR);
        $stmt->bindParam(':comment',$comment,PDO::PARAM_STR);
        $stmt->bindParam(':pass',$pass,PDO::PARAM_STR);
        $stmt->bindParam(':date',$date,PDO::PARAM_STR);
        $stmt->execute();
    }
?>
<!DOCTYPE HTML>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>mission5-1</title>
    </head>
    <body>
        <form action="" method="post">
            <!--新規投稿スペース-->
            <p><label>名前　　　 : <input type="text" name="name" value=<?php if(!empty($can_edit)){echo $v_name;}?>></label><br>
               <input type="hidden" name="e_num2" value=<?php if(!empty($can_edit)){echo $v_enum;}?>>
               <label>テキスト　 : <input type="text" name="comment" value=<?php if(!empty($can_edit)){echo $v_comment;}?>></label><br>
               <label>パスワード : <input type="password" name="passA" value=<?php if(!empty($can_edit)){echo $v_pass;}?>></label>
            <input type="submit" name="n_submit"></p>
            
            <!--削除番号-->
            <p><label>削除番号　 : <input type="number" name="d_num"></label><br>
               <label>パスワード : <input type="password" name="passB"></label>
               <input type="submit" name="d_submit"></p>
            <!--編集番号-->
            <p><label>編集番号　 : <input type="number" name="e_num"></label><br>
               <label>パスワード : <input type="password" name="passC"></label>
               <input type="submit" name="e_submit"></p>
            <hr>
        <?php
                //エラー（投稿時）の表示
            if(!empty($pe_checks)){
                foreach($pe_checks as $pe_check){
                    echo "・".$pe_check."を入力してください"."<br>";}
            echo "<hr>";}
                //エラー（編集番号）の表示
            if(empty($e_result) && isset($e_result)){echo en_check($e_result)."<hr>";}
                //エラー（編集パス）の表示
            if(isset($can_edit) && $can_edit==0){echo "パスワードが違います"."<hr>";}
                //エラー（編集投稿時）を表示
            if(!empty($ee_checks)){
                foreach($ee_checks as $ee_check){
                    echo "・".$ee_check."を入力してください"."<br>";}
            echo "<hr>";}
                //エラー（削除申請時）を表示
            if(empty($d_result) && isset($d_result)){echo dn_check($d_result)."<hr>";}
            if(isset($can_del) && empty($can_del)){echo "パスワードが違います"."<hr>";}
                //投稿内容の表示
            $sql='SELECT id,name,comment,date FROM tb5_1';
            $stmt=$pdo -> query($sql);
            $results = $stmt->fetchAll();
            foreach ($results as $row){
            echo $row['id'].' ,';
            echo $row['name'].' ,';
            echo $row['comment'].' ,';
            echo $row['date'].'<br>';
            echo "<hr>";}
                //パス確認用裏口
            /*
            $sql='SELECT id,name,comment,date,pass FROM tb5_1';
            $stmt=$pdo -> query($sql);
            $results = $stmt->fetchAll();
            foreach ($results as $row){
            echo $row['id'].' ,';
            echo $row['name'].' ,';
            echo $row['comment'].' ,';
            echo $row['date'].' ,';
            echo $row['pass'].'<br>';
            }
            */
            //要注意！！テーブル削除
            /*
            $sql='DROP TABLE tb5_1';
            $stmt=$pdo->query($sql);
            */
        ?>
        </form>
    </body>
</html>