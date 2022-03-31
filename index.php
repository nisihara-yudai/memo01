<?php
// echo 'Hello World'; // ファイルの作成

function dbConnect()
{
  $dsn = 'mysql:host=localhost;dbname=app;charset=utf8'; //データソース名
  $user = 'root'; //ユーザー名
  $password = 'root'; //パスワード

  try {
    new PDO($dsn, $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,]);

    var_dump("疎通確認OK!");
  } catch (PDOException $e) {
    header('Content-Type: text/plain; charset=utf8', true, 500);
    var_dump($e->getMessage());
    exit();
  }
}

function fetchAll()
{
  $sql = "SELECT * FROM todo";
  $query = dbConnect()->query($sql);
  return $query->fetchAll(PDO::FETCH_ASSOC);
}

function create($text)
{
  $now = date('Y-m-d H:i:s');
  $sql = 'insert into todo(text,created_at,updated_at) values(?,?,?,?)';
  $stmt = dbConnect()->prepare($sql);
  $stmt = execute([$text, $now, $now]);
}

function update($id, $text)
{
  $sql = 'UPDATE todo SET text = ?,updated_at = ? where todo.id = ?';
}

function delete($id)
{
  $sql = 'delete from todo WHERE todo.id = ?';
  $stmt = dbConnect()->prepare($sql);

  $stmt->execute([$id]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!empty($_POST['submit'])) {
    create($_POST['submit']);
  } else if (isset($_POST['update'])) {
    update($_POST['id'], $_POST['text']);
  } else if (isset($_POST['delete'])) {
    delete($_POST['id']);
  }

  // index.phpにリダイレクト
  header('Location: ' . $_SERVER['SCRIPT_NAME']);
  exit;
}

$DATA = fetchALL();


// dbConnect();

?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TodoApp</title>
</head>

<body>
  <h1>TODOアプリ</h1>

  <section>
    <form method="post">
      <input type="text" name="submit" required>
      <button type="submit">作成する</button>
    </form>

    <table>
      <?php
      if ($DATA) {
      ?>
        <tr>
          <th background="#808080" rowspan="2">
            <td color="#FFFFFF">TODO</td>
          </th>
          <th background="#808080" rowspan="2">
            <td color="#FFFFFF">作成日</td>
          </th>
          <th background="#808080" colspan="2" id="action">
            <td color="#FFFFFF">操作</td>
          </th>
        </tr>
        <tr>
          <th background="#808080" headers="action">
            <td color="#FFFFFF">更新</td>
          </th>
          <th background="#808080" headers="action">
            <td color="#FFFFFF">削除</td>
          </th>
        </tr>
      <?php
      }
      ?>

      <?php foreach ((array)$DATA as $row) : ?>
        <form method="post">
          <tr>
            <input type="hidden" name="id" value="<?php echo $row["id"]; ?>">
            <td>
              <input type="text" name="text" value=<?php echo $row["text"]; ?> required>
            </td>
            <td>
              <?php echo $row["created_at"]; ?>
            </td>
            <td>
              <button type="submit" name="update">更新する</button>
            </td>
            <td>
              <button type="submit" name="delete">削除する</button>
            </td>
          </tr>
        </form>
      <?php endforeach; ?>
    </table>
  </section>
</body>

</html>