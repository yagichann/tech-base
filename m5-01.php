<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-01</title>
</head>
<body>
    

    <?php

        //データベースへの接続
        $dsn = 'mysql:dbname=tb250554db;host=localhost';
        $user = 'tb-250554';
        $password = '6NyXDghatG';
        $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
        $pass_flag = 0;
        $edit_flag = 0;
        if(!empty($_POST["edit"])){
            //コメント編集
            if(!empty($_POST["edit_number"] and !empty($_POST["edit_password"]))){
                $edit_password = $_POST["edit_password"];
                $id = $_POST["edit_number"];
                $sql = "select pass from m501 where id =:id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(":id", $id, PDO::PARAM_INT);
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                if($row && $row['pass'] === $edit_password){
                    $sql = "SELECT * FROM m501 where id=:id";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(":id",$id,PDO::PARAM_INT);
                    $stmt->execute();
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    if($result !== false){
                        $edit_name = $result["name"];
                        $edit_comment = $result["text"];
                        $edit_number = $id;
                    }
                    
                }
                $edit_flag = 1;
            }
            
        }
    ?>
    
    <form action ="" method = "post">
        <input type = "text" name = "name" placeholder="名前" 
            value = "<?php 
                if(isset($edit_name)){
                    echo $edit_name;   
                }?>"
        >
        
        <input type = "text" name = "text" placeholder="コメント"
            value = "<?php
                if(isset($edit_comment)){
                    echo $edit_comment;   
                }?>"
        >
        
        <input type = "hidden" name ="edit_flag"
            value = "<?php
                if(isset($edit_number)){
                    echo $edit_number;
                }?>"
        >
        <input type = "text" name = "post_password" placeholder = "パスワード">
        <input type = "submit" name = "submit"><br><br>
        
        <input type = "number" name = "del_number" placeholder = "削除対象番号">
        <input type = "text" name = "del_password" placeholder = "パスワード">
        <input type = "submit" name = "delete" value = "削除"><br><br>
        
        <input type = "number" name = "edit_number" placeholder = "編集対象番号">
        <input type = "text" name = "edit_password" placeholder = "パスワード">
        <input type = "submit" name = "edit" value = "編集"><br><br>
    </form>

    <?php
        //create
        $sql = "CREATE TABLE IF NOT EXISTS m501"
        ." ("
        . "id INT AUTO_INCREMENT PRIMARY KEY,"
        . "name CHAR(32),"
        . "text TEXT,"
        . "date TEXT,"
        . "pass TEXT"
        .");";
        $stmt = $pdo->query($sql);
    ?>
    
    <?php
        if(!empty($_POST["post_password"])){
            $pass_flag = 1;
        }
        
        if(isset($_POST["submit"])){
            //コメント送信
            if(!empty($_POST["text"]) and !empty($_POST["name"])){
                $text = $_POST["text"];
                $name = $_POST["name"];
                $pass = $_POST["post_password"];
                $date = date("Y-m-d H:i:s");
                
            }
            if(!empty($text) and !empty($name)){
                //編集
                if(!empty($_POST["edit_flag"])){
                    $edit_number = intval($_POST["edit_flag"]);
                    $id = $edit_number;
                    $sql = "UPDATE m501 SET name=:name,text=:text WHERE id=:id";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                    $stmt->bindParam(':text', $text, PDO::PARAM_STR);
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt->execute();               
                }
                //送信(ノーマル)
                else{

                    //insert
                    $sql = "INSERT INTO m501 (name, text, date, pass) VALUES (:name, :text, :date, :pass)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                    $stmt->bindParam(':text', $text, PDO::PARAM_STR);
                    $stmt->bindParam(':date', $date, PDO::PARAM_STR);
                    $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
                    $stmt->execute();
                    
                    
                }
            }
            
            
        }elseif(isset($_POST["delete"])){
            //コメント削除
            if(!empty($_POST["del_number"] and !empty($_POST["del_password"]))){
                $id = $_POST["del_number"];
                $del_password = $_POST["del_password"];
                $sql = "select pass from m501 where id =:id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(":id", $id, PDO::PARAM_INT);
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                //var_dump($row["pass"],$del_password);
                //echo "<br>";
                //echo $row["pass"]."<br>";
                if($row && $row['pass'] == $del_password){
                    $sql = 'delete from m501 where id=:id';
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt->execute();
                    //番号振り直し
                    $sqlReset = "SET @i := 0; UPDATE m501 SET id = (@i := @i + 1);";
                    $pdo->exec($sqlReset);
                    $sql = "alter table m501 auto_increment = 1";
                    $pdo->exec($sql);
                }
                else{
                    echo "ERROR : wrong password<br>";
                }
                
                
                
                
                
            }
            elseif(empty($_POST["del_password"])){
                echo "ERROR : enter your password<br>";
            } 
            elseif(empty($_POST["del_number"])){
                echo "ERROR : enter delete number<br>";
            }       
        }
        //表示
        $sql = "SELECT * FROM m501";
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach($results as $row){
            echo $row["id"]." ".$row["name"]." ".$row["text"]." ".$row["date"].$row["pass"]."<br>";
        }
    ?>
</body>
</html>