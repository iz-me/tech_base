<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charaset="UTF-8">
    <title>mission5</title>
</head>
<body>
    <?php
    $name1=@$_POST["name"]; //名前
    $comment1=@$_POST["comment"]; //コメント
    $del=@$_POST["del"]; //削除番号
    $edit1=@$_POST["postnum"]; //編集番号
    $edit=@$_POST["edit"]; //編集するかどうかの番号
    $date=date('Y/m/d h:i:s');
    $pass1=@$_POST["pass1"];//投稿pass
    $pass2=@$_POST["pass2"];//削除pass
    $pass3=@$_POST["pass3"];//編集pass
    
    
    //DB接続設定
    $dsn = 'データベース名';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn,$user,$password,array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_WARNING));
    //↑データベース操作でエラーがはっせいしたときに警告を表示
    
 
    $sql = "CREATE TABLE IF NOT EXISTS tbtest_5"
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "num INT,"
    . "name char(32),"
    . "comment TEXT,"
    . "date TEXT,"
    . "pass char(32)"
    .");";
    $stmt = $pdo->query($sql);
    
    
    $num=1;
    $sql = 'SELECT * FROM tbtest_5';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
        if ($row['num']>=$num){
            $num=$row['num']+1;
        }
    }
    
    
    //投稿フォーム
    if (!empty($name1) && !empty($comment1) && isset($_POST["sub1"]) && !empty($pass1)){
        if (empty($edit)){ //投稿番号が空
            $sql = $pdo -> prepare("INSERT INTO tbtest_5 (num,name,comment,date,pass) VALUES (:num,:name, :comment, :date, :pass)");
            $sql -> bindParam(':num',$num,PDO::PARAM_INT);
            $sql -> bindParam(':name',$name,PDO::PARAM_STR);
            $sql -> bindParam(':comment',$comment, PDO::PARAM_STR);
            $sql -> bindParam(':date',$date,PDO::PARAM_STR);
            $sql -> bindParam(':pass',$pass1,PDO::PARAM_STR);
            $name = $name1;
            $comment = $comment1;
            $pass = $pass1;
            $sql -> execute();
            $passa=$pass;
        }else{ //投稿番号がある
            $num = $edit; //変更する投稿番号
            $name = $name1;
            $comment = $comment1; 
            $date=date('Y/m/d h:i:s');
            $pass=$pass1;
            $sql = 'SELECT * FROM tbtest_5';
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            foreach ($results as $row){
                if ($pass==$row['pass']&&$num==$row['num']){
                    $sql = 'UPDATE tbtest_5 SET name=:name,comment=:comment,date=:date WHERE num=:num';
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                    $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                    $stmt->bindParam(':date', $date, PDO::PARAM_STR);
                    $stmt->bindParam(':num', $num, PDO::PARAM_INT);
                    $stmt->execute();
                    $passa=$pass;
                }
            }
        }
    }
    
    
    //削除フォーム
    if (!empty($del) && isset($_POST["sub2"]) && !empty($pass2)){
         $pass=$pass2;
         $sql = 'SELECT * FROM tbtest_5';
         $stmt = $pdo->query($sql);
         $results = $stmt->fetchAll();
         foreach ($results as $row){
             if ($pass==$row['pass']){
                 $num=$del;
                 $sql='delete from tbtest_5 where num=:num';
                 $stmt = $pdo->prepare($sql);
                 $stmt->bindParam(':num',$num,PDO::PARAM_INT);
                 $stmt->execute();
                 $passa=$pass;
             }else if (!empty($passa)){
                 $id=$row['id'];
                 $num=$row['num']-1;
                 if ($row['num']>=$del){
                     $sql='UPDATE tbtest_5 SET num=:num WHERE id=:id';
                     $stmt = $pdo->prepare($sql);
                     $stmt->bindParam(':num', $num, PDO::PARAM_INT);
                     $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                     $stmt->execute();//←SQLを実行
                 }
             }
         }
    }
    
   
    
    //編集フォーム
    if (!empty($edit1) && isset($_POST["sub3"]) && !empty($pass3)){
        $pass=$pass3;
        $num=$edit1;　// 編集番号
        $sql = 'SELECT * FROM tbtest_5 WHERE num=:num ';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':num', $num, PDO::PARAM_INT);
        $stmt->execute();                         
        $results = $stmt->fetchAll(); 
        foreach ($results as $row){
            if ($pass==$row['pass']){
                $edit2=$row['num'];
                $name2=$row['name'];
                $comment2=$row['comment'];
                $passa=$pass;
            }
        }
    }
    ?>
    
    <form action="" method="post">
        <!--入力フォームと削除番号の設置-->
        <p>【投稿フォーム】<br>
           名前：　　　<input type="text" name="name"
                        value=<?php if (!empty($edit1) && isset($_POST["sub3"])){
                            echo @$name2;
                        }?>><br>
           コメント：　<input type="text" name="comment"
                        value=<?php if (!empty($edit1) && isset($_POST["sub3"])){
                            echo @$comment2;
                        }?>><br>
           パスワード：<input type="text" name="pass1"><br>
           <input type="submit" name="sub1"></p>
        <p>【削除フォーム】<br>
           削除番号：　<input type="number" name="del"><br>
           パスワード：<input type="text" name="pass2"><br>
           <input type="submit" name="sub2"></p>
        <p>【編集フォーム】<br>
           投稿番号：　<input type="number" name="postnum"><br>
           パスワード：<input type="text" name="pass3"><br>
           <input type="submit" name="sub3"></p>
           <!--編集番号をかくしています-->
           <input type="hidden" name="edit"
　                   value="<?php if (!empty($edit1) && isset($_POST["sub3"])){
                                echo @$edit2;
                            }?>"><br>
            ぱすわーど: 
            <input type="text" name="passtext"
                value="<?php if (!empty($passa)){
                    echo $passa;
                }?>"><br>
            <br>
                            
            -------------【投稿一覧】-------------
            <br>
            <br>

    </form>
    
    <?php
    
    //画面表示
    $sql ='SELECT * FROM tbtest_5';
    $stmt = $pdo -> query($sql);
    $results = $stmt -> fetchAll();
    foreach ($results as $row){
        if (!empty($num)){
            //echo $row['id']."-  ";
            echo $row['num']."　";
            echo $row['name']."　";
            echo $row['comment']."　";
            echo $row['date']."　";
            //echo $row['pass']."<br>";
            echo "<hr>";
        }

    }
    ?>
    
</body>
</html>