<?php include "../inc/dbinfo.inc"; ?>

<html>
<head>
  <style>
    table {
      border-collapse: collapse;
      width: 100%;
    }

    th, td {
      border: 1px solid black;
      padding: 8px;
      text-align: left;
    }
  </style>
</head>
<body>
  <h1>Sample page</h1>

  <?php
    /* Connect to MySQL and select the database. */
    $connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);

    if (mysqli_connect_errno()) echo "Failed to connect to MySQL: " . mysqli_connect_error();

    $database = mysqli_select_db($connection, DB_DATABASE);

    /* Ensure that the livro table exists. */
    VerifyLivroTable($connection, DB_DATABASE);

    /* If input fields are populated, add a row to the livro table. */
    $titulo = htmlentities($_POST['TITULO']);
    $autor = htmlentities($_POST['AUTOR']);
    $genero = htmlentities($_POST['GENERO']);
    $ano_publicacao = htmlentities($_POST['ANO_PUBLICACAO']);
    $data_termino_leitura = htmlentities($_POST['DATA_TERMINO_LEITURA']);

    if (strlen($titulo) || strlen($autor) || strlen($genero) || strlen($ano_publicacao) || strlen($data_termino_leitura)) {
      AddLivro($connection, $titulo, $autor, $genero, $ano_publicacao, $data_termino_leitura);
    }
  ?>

  <!-- Input form -->
  <form action="<?PHP echo $_SERVER['SCRIPT_NAME'] ?>" method="POST">
    <table>
      <tr>
        <td>TITULO</td>
        <td>AUTOR</td>
        <td>GENERO</td>
        <td>ANO_PUBLICACAO</td>
        <td>DATA_TERMINO_LEITURA</td>
      </tr>
      <tr>
        <td><input type="text" name="TITULO" maxlength="255" size="30" /></td>
        <td><input type="text" name="AUTOR" maxlength="255" size="30" /></td>
        <td><input type="text" name="GENERO" maxlength="50" size="30" /></td>
        <td><input type="text" name="ANO_PUBLICACAO" maxlength="4" size="10" /></td>
        <td><input type="date" name="DATA_TERMINO_LEITURA" /></td>
        <td><input type="submit" value="Add Data" /></td>
      </tr>
    </table>
  </form>

  <!-- Display table data. -->
  <table>
    <tr>
      <th>ID</th>
      <th>TITULO</th>
      <th>AUTOR</th>
      <th>GENERO</th>
      <th>ANO_PUBLICACAO</th>
      <th>DATA_TERMINO_LEITURA</th>
    </tr>

    <?php
    $result = mysqli_query($connection, "SELECT * FROM livro");

    while($query_data = mysqli_fetch_assoc($result)) {
      echo "<tr>";
      echo "<td>", $query_data['id'], "</td>",
           "<td>", $query_data['titulo'], "</td>",
           "<td>", $query_data['autor'], "</td>",
           "<td>", $query_data['genero'], "</td>",
           "<td>", $query_data['ano_publicacao'], "</td>",
           "<td>", $query_data['data_termino_leitura'], "</td>";
      echo "</tr>";
    }
    ?>

  </table>

  <!-- Clean up. -->
  <?php
    mysqli_free_result($result);
    mysqli_close($connection);
  ?>

</body>
</html>

<?php
/* Adicione um livro à tabela. */
function AddLivro($connection, $titulo, $autor, $genero, $ano_publicacao, $data_termino_leitura) {
  $t = mysqli_real_escape_string($connection, $titulo);
  $a = mysqli_real_escape_string($connection, $autor);
  $g = mysqli_real_escape_string($connection, $genero);
  $ano = mysqli_real_escape_string($connection, $ano_publicacao);
  $data = mysqli_real_escape_string($connection, $data_termino_leitura);

  $query = "INSERT INTO livro (titulo, autor, genero, ano_publicacao, data_termino_leitura) VALUES ('$t', '$a', '$g', '$ano', '$data');";

  if(!mysqli_query($connection, $query)) echo("<p>Error adding livro data.</p>");
}

/* Verifique se a tabela livro existe e, se não existir, crie-a. */
function VerifyLivroTable($connection, $dbName) {
  if(!TableExists("livro", $connection, $dbName)) {
    $query = "CREATE TABLE livro (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        titulo VARCHAR(255) NOT NULL,
        autor VARCHAR(255) NOT NULL,
        genero VARCHAR(50),
        ano_publicacao INT,
        data_termino_leitura DATE
      )";

    if(!mysqli_query($connection, $query)) echo("<p>Error creating livro table.</p>");
  }
}

/* Verifique a existência de uma tabela. */
function TableExists($tableName, $connection, $dbName) {
  $t = mysqli_real_escape_string($connection, $tableName);
  $d = mysqli_real_escape_string($connection, $dbName);

  $checktable = mysqli_query($connection,
      "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_NAME = '$t' AND TABLE_SCHEMA = '$d'");

  if(mysqli_num_rows($checktable) > 0) return true;

  return false;
}
?>