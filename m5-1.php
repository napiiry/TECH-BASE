<?php
//データベース接続設定
$dsn='mysql:dbname=データベース名;host=localhost';
$user = 'ユーザー名';
$password = 'パスワード';

try{
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
}catch(PDOException $e){
    echo 'データベース接続エラー:'.$e->getMessage();
}
    
    $sql = "CREATE TABLE IF NOT EXISTS thread"
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name CHAR(32),"
    . "comment TEXT,"
    . "password TEXT,"
    . "date_table TIMESTAMP DEFAULT CURRENT_TIMESTAMP"
    .");";

    $pdo->exec($sql);
    
//投稿処理
    if(isset($_POST["submit"])){
        $name=$_POST["name"];
        $comment=$_POST["comment"];
        $password=$_POST["password"];
        //$date=date('Y-m-d H:i');
        
        if(!empty($name)&&!empty($comment)){
            //新規投稿
            if($_POST["editor"]!=true){
            
            //データ入力(insert文)
            $sql="INSERT INTO thread(name,comment,password,date_table) VALUES(:name,:comment,:password,NOW())";
            $stmt=$pdo->prepare($sql);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt->bindParam(':password', $password, PDO::PARAM_STR);
            $stmt->execute();
                
            }else{
                //更新処理
                $editor = $_POST["editor"];
                $sql = "UPDATE thread SET name = :name, comment = :comment, password = :password WHERE id = :editor";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                $stmt->bindParam(':password', $password, PDO::PARAM_STR);
                $stmt->bindParam(':editor', $editor, PDO::PARAM_INT);
                $stmt->execute();
            }
        }
    }

//削除処理
    if(isset($_POST["delete"])){
        $delete_id=$_POST["delete_id"];
        $deletepassword=$_POST["deletepassword"];
        
        //パスワード確認・削除
        $sql="DELETE FROM thread WHERE id=:id AND password=:password";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $delete_id, PDO::PARAM_INT);
        $stmt->bindParam(':password', $deletepassword, PDO::PARAM_STR);
        $stmt->execute();
    }
    

//編集処理
    if(isset($_POST["edit_submit"])){
        $edit_id=$_POST["edit_id"];
        $editpassword=$_POST["editpassword"];
        
        //パスワードを確認
        $sql="SELECT*FROM thread WHERE id=:id AND password=:password";
        $stmt=$pdo->prepare($sql);
        $stmt->bindParam(':id',$edit_id,PDO::PARAM_INT);
        $stmt->bindParam(':password',$editpassword,PDO::PARAM_STR);
        $stmt->execute();
        
        $row=$stmt->fetch();
        
        //パスワード一致したら
        if($row){
            $edit_name=$row['name'];
            $edit_comment=$row['comment'];
            $edit_password=$row['password'];
        }else{
            echo "パスワードが違います。";
        }
    }

?>





<html>
<head>
<meta charset="utf-8">
<title>冬が来る</title>
</head>
<body>
    <h3>冬に食べたいものといえば！！！</h3>
<form action="" method="post">
    [投稿フォーム]
    <input type="hidden" name="editor" value="<?php if (!empty ($edit_id))echo $edit_id; ?>">
    <br>
    <input type="text" name="name" value="<?php if(!empty($edit_name))echo $edit_name; ?>" placeholder ="名前">
    <br>
    <input type="text" name="comment" value="<?php if(!empty($edit_comment))echo $edit_comment;?>"placeholder = "メッセージ">
    <br>
    <input type ="text" name="password" value="<?php if(!empty($edit_password))echo $edit_password;?>" placeholder="パスワード">
    <br>
    <input type="submit" name="submit" value="送信">
</form>
<form action="" method="post">
    [削除フォーム]
    <br>
    <input type="number" name="delete_id"placeholder ="削除対象番号">
    <br>
    <input type ="text" name="deletepassword" placeholder="パスワード">
    <br>
    <input type="submit" name="delete" value="削除">
</form>
<form action="" method="post">
    [編集フォーム]
    <br>
    <input type="number" name="edit_id" placeholder="編集対象番号">
    <br>
    <input type ="text" name="editpassword" placeholder="パスワード">
    <br>
    <input type="submit" name="edit_submit" value="編集">
</form>




<?php  
    //入力したデータレコードを抽出・表示
    $sql='SELECT*FROM thread';
    $stmt=$pdo->query($sql);
    $results=$stmt->fetchAll();
    foreach($results as $row){
        echo $row['id'].' : ';
        echo $row['name'].' : ';
        echo $row['comment'].' : ';
        echo $row['date_table'].'<br>';
        //改行を追加して表示する
    }
?>   




</body>
</html>
