<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>mission5-1.php</title>
</head>
<body>

<?php

//データベースに接続
    $dsn = 'データベース';//データベースを設定
    $user = 'ユーザー';//ユーザー名
    $password = 'パスワード';//パスワード
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));///データベースへの接続、エラー設定
    //pdoクラスのインスタンスを生成する処理↑

    /////////////テーブルを作成
    $sql = "CREATE TABLE IF NOT EXISTS blackbord"// blackbordと名付けたテーブルを作成
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"//投稿番号id　　番号が順に増える PRIMARY KEYは謎。。。　
	. "name char(32),"//名前は32文字まで
    . "comment TEXT,"//コメントはテキスト表示
    . "datetime timestamp"//日時の表示
	. ");";
	$stmt = $pdo->query($sql);//テーブルblackbordの作成を決行
	

    ////////////作成したテーブルに、insertを行って投稿を入力する。編集もしちゃう。

    if(!empty($_POST["name"])&&!empty($_POST["str"])&&!empty($_POST["password1"])){// [編集付き投稿！] 名前と投稿のフォームが両方書き込まれている時に以下の操作を行う
        $password=$_POST["password1"];//投稿フォームに書き込まれた英語を、このif内では＄passwordに代入
        if($password=="pass"){//編集フォームのパスワードが正しいとき
            $name=$_POST["name"];//名前の投稿を変数に代入
            $comment=$_POST["str"];//コメントの投稿を変数に代入
            $datetime = date("Y/m/d H:i:s"); 
            if(!empty($_POST["editnum"])){ //編集番号が埋まっている時の編集投稿                
                $id = $_POST["editnum"]; //変更する投稿番番号を設定           
                $sql = "UPDATE blackbord set name=:name,comment=:comment,datetime=:datetime WHERE id=:id";////編集のコード
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                $stmt->bindParam(':datetime', $datetime, PDO::PARAM_STR);
                $stmt->execute();//編集の実行            
            }else{//新規投稿をするとき
                $sql = $pdo -> prepare("INSERT INTO blackbord (name, comment, datetime) VALUES (:name, :comment, :datetime)");///一行、データベースに書き込み
                $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                $sql -> bindParam(':datetime', $datetime, PDO::PARAM_STR);
                $sql -> execute();//////$sqlの実行（投稿の表示）
                }
        }       
    }    


///////////////削除フォーム
    if(!empty($_POST["deletenum"])&&!empty($_POST["password2"])){//削除フォームの書き込みを受け付け、パスワードも埋まっている時
        $password=$_POST["password2"];//投稿フォームに書き込まれた数字を、このif内では＄passwordに代入
        if($password=="pass"){//編集フォームのパスワードが正しいとき
                    $id = $_POST["deletenum"];//削除指定番号と投稿番号が一致したら場合
                    $sql = 'delete from blackbord where id=:id';//削除を行うクエリを設定
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt->execute();//実行
                 }
            }
        

    
//編集フォームから投稿フォームに文字データを飛ばす
if(!empty($_POST["edit"])&&!empty($_POST["password3"])){//編集番号が書き込まれ、パスワードが埋まっている場合、
    $password3=$_POST["password3"];
    if($password3=="pass"){//編集フォームのパスワードが正しいとき
        //SELECT使ってデータを取り出さないといけない、、、
        $edit=$_POST["edit"];//名前、コメントを飛ばすのは、投稿フォームで行うことにした。改善の余地あり。                                          
        echo $edit;
        
    }
    }       


?>


    <form action="" method="post">
    【 投稿フォーム 】<br>
        名前：     <input type="text" name="name"
                        value="<?php if(isset($edit)){
                            $id=$edit;
                            $sql = 'SELECT * FROM blackbord WHERE id=:id';
                            $stmt = $pdo->prepare($sql); 
                            $stmt->bindParam(':id', $id, PDO::PARAM_INT); 
                            $stmt->execute();                            
                            $results = $stmt->fetchAll();
                                foreach ($results as $row){
                                //$rowの中にはテーブルのカラム名が入る
                                echo $row['name'];	
                            }
                        }?>"><br>
        コメント： <input type="text" name="str" 
                        value="<?php if(isset($edit)){
                             $id=$edit;
                             $sql = 'SELECT * FROM blackbord WHERE id=:id';
                             $stmt = $pdo->prepare($sql); 
                             $stmt->bindParam(':id', $id, PDO::PARAM_INT); 
                             $stmt->execute();                            
                             $results = $stmt->fetchAll();
                                 foreach ($results as $row){
                                 //$rowの中にはテーブルのカラム名が入る
                                 echo $row['comment'];
                             }
                            }?>"><br>
        パスワード：  <input type="text" name="password1"><br>
        <input type="submit" name="投稿する"><br>
        <!--↓に数字がある時のみ行う処理がある（＝フラグ・目印）-->
        <!--非表示:--> <input type="hidden" name="editnum" value="<?php if(isset($edit)){echo $edit;}?>" >
    
    <br>【 削除フォーム 】<br>
        削除番号：  <input type="text" name="deletenum"><br>
        パスワード：  <input type="password" name="password2"><br>
        <input type="submit" name="削除する"><br>

    <br>【 編集フォーム 】<br>
        投稿番号：  <input type="text" name="edit"><br> <!---こちらは数字を非表示フォームへ飛ばすのみ-->  
        パスワード：  <input type="password" name="password3"><br>
        <input type="submit" name="編集する"> <br><br><br>
    </form>

<?php
    //////////全てを表示する
 $sql = 'SELECT * FROM blackbord';
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();
	foreach ($results as $row){
		//$rowの中にはテーブルのカラム名が入る
		echo $row['id'].',';
		echo $row['name'].',';
        echo $row['comment'].',';
        echo $row['datetime'];
	echo "<hr>";
	}
	
	
?>  
   
</body>
</html>