<html>
<meta charset = "utf-8">
<body>

<?php

//mission_4-1のデータベースへの接続
	$dsn = 'データベース名';
	$user = 'ユーザー名';
	$password = 'パスワード';
	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_WARNING));

$sql = "CREATE TABLE IF NOT EXISTS toukou"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32),"
	. "comment TEXT,"
	. "jdate TEXT,"
	. "password TEXT"
	.");";
	$stmt = $pdo->query($sql);

////名前・コメント書き込みフォーム////


if(!empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["password"]) && !empty($_POST["send"]) && empty($_POST["editNum"]))
{
	$sql = $pdo -> prepare("INSERT INTO toukou (name, comment, jdate, password) VALUES (:name, :comment, :jdate, :password )");
	$sql -> bindParam(':name', $name, PDO::PARAM_STR);
	$sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
	$sql -> bindParam(':jdate', $jdate, PDO::PARAM_STR);
	$sql -> bindParam(':password', $password, PDO::PARAM_STR);
	$name = $_POST["name"];
	$comment = $_POST["comment"];
	$date = new DateTime("now");
	$jdate = $date -> format('Y-m-d H:i:s');
	$password = $_POST["password"];
	$sql -> execute();
}


////削除処理フォーム////
if(!empty($_POST["delete"]) && !empty($_POST["delPassword"]) && !empty($_POST["delButton"]))
{
	$id = $_POST["delete"];
	$delpass = $_POST["delPassword"];
	$sql = 'SELECT * FROM toukou';	//データを取得する；
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();	//配列
	//var_dump($results);
	foreach ($results as $row){	//$rowの中にはテーブルのカラム名が入る;
		//var_dump($row);
		if($id  == $row['id'])	//削除番号と投稿番号が一致して、
		{
			if($delpass == $row['password'])	//削除フォームに入力されたパスワードが一致したら、
			{	//mission4-8の入力したデータを削除を使う。
				//------------------↓作成したテーブル名------------------------------------------------------------------
				$sql = 'delete from toukou where id=:id';
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':id', $id, PDO::PARAM_INT);
				$stmt->execute();	//処理を実行
			}
			else	//削除フォームに入力されたパスワードが違う場合
			{
				echo "パスワードが違います";
			}
		}
	}
}


////編集対象投稿の編集フォーム////

	//編集対象番号が入力されていて、編集するボタンが押された時
if(!empty($_POST["editId"]) && !empty($_POST["editPassword"]) && !empty($_POST["editButton"]))
{
	$editpas = $_POST["editPassword"];
	$editId = $_POST["editId"];	//変数$editIdを用意してフォームで入力されたeditIdの値を格納する
	$editPassword = $_POST["editPassword"];
			//----------------↓作成したテーブル名-----------------------------------------------------------------------------------
	$sql = 'SELECT * FROM toukou';	//データを取得する；
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();	//配列
	//var_dump($results);
	foreach ($results as $row)
	{	//$rowの中にはテーブルのカラム名が入る;
		//var_dump($row);
		if($editId  == $row['id'])	//編集対象番号と一致した投稿番号で、
		{	
			if($editpas == $row['password'])	//パスワードが一致したら
			{	
				$editNum = $row['id'];	//投稿番号の取得;
				$editName = $row['name'];	//名前の取得；
				$editComment = $row['comment'];	//コメントの取得；
			}
			else
			{
				echo "パスワードが違います";
			}
		}
	}
}


////編集した投稿の書き換えフォーム////

//3-4-7で作ったテキストボックスがセットされていたら
if(!empty($_POST["editNum"]))
{
	$id = $_POST["editNum"];	//変数idに編集する投稿番号を入れる
	$name = $_POST["name"];
	$comment = $_POST["comment"];
	$date = new DateTime("now");//日時を取得している
	$jdate = $date->format('Y-m-d H:i:s');//出力する形式を整えている
	$password = $_POST["password"];
			//データベースをアップデートするmission4-7の入力したデータを編集を使う
			//---------↓作成したテーブルの名前-------------------------------------
	$sql = 'update yuitest set name=:name,comment=:comment, jdate=:jdate, password=:password where id=:id';
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':name', $name, PDO::PARAM_STR);
	$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
	$stmt->bindParam(':jdate', $jdate, PDO::PARAM_STR);
	$stmt -> bindParam(':password', $password, PDO::PARAM_STR);
	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
	$stmt->execute();
}

//////////////////////////////////////////////////////////////////////////////
?>

	<h1>☆掲示板☆</h1>
	<h3>好きな映画について語り合いましょう～！！</h3>
<!--POST送信する入力フォームを作成-->
<!--valueにphpで投稿編集フォームで取得した名前とコメントを表示させる-->
	<form method="POST" action="">
	<p>お名前　：<input type = "text"  name = "name" value = <?php 
			if(!empty($editName))
			{
				echo $editName;//$editNameがセットされてたらその値を出力
			} 
			?>><br/>
			コメント：<input type = "text" name = "comment" value = <?php
			if(!empty($editComment))
			{
				echo $editComment;//$editCommentがセットされてたらその値を出力
			} 
			?>><br/>
			パスワード：<input type = "password" name = "password"></p>
			<!--編集したい投稿番号が表示されるテキストボックスを用意-->
			<input type = "hidden" name = "editNum" value = <?php
				if(!empty($editNum))
				{
					echo $editNum;//$editNumがセットされてたらその値を出力
				}
				?>>
<!--送信ボタン作成-->
	<p><input type = "submit" name = "send" value = "送信する"><input type="reset" value="リセット"></p>
<!--削除番号を指定する-->
	<p>◎投稿を削除する◎<br/>
		削除番号(半角)：<input type = "text" name = "delete" size="8" maxlength="8"><br/>
		パスワード：<input type = "password" name = "delPassword"></p>
<!--削除ボタン作成-->
	<p><input type = "submit" name = "delButton" value = "削除する"></p>
<!--編集番号を指定する-->
	<p>◎投稿を編集する◎<br/>
		編集番号(半角)：<input type = "text" name = "editId" size="8" maxlength="8"><br/>
		パスワード：<input type = "password" name = "editPassword"></p>
<!--編集投稿ボタン-->
	<p><input type = "submit" name = "editButton" value = "編集する"></p>
	</form>
	<h2>投稿</h2>
<?php
/////////////////////データベースの中身を表示////////////////////////////////////////
			//mission 4-6の入力したデータを表示するを使う
			//--------------↓作成したテーブルの名前
$sql = 'SELECT * FROM toukou';	//データを取得する；
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();	//配列
	foreach ($results as $row)
	{		//$rowの中にはテーブルのカラム名が入る
		echo $row['id'].',';
		echo $row['name'].',';
		echo $row['comment'].',';
		echo $row['jdate'].'<br>';
		//echo $row['password'].'<br>';
	echo "<hr>";
	}
///////////削除する投稿番号を半角英数で直接タイプしないと消せない！！
?>

</body>
</html>
