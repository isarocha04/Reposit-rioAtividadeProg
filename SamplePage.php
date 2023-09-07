<?php include "../inc/dbinfo.inc"; ?>
<html>
<body>
<h1>Sample page</h1>
<?php

  /* Connect to MySQL and select the database. */
  $connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);

  if (mysqli_connect_errno()) echo "Failed to connect to MySQL: " . mysqli_connect_error();

  $database = mysqli_select_db($connection, DB_DATABASE);

  /* Ensure that the EMPLOYEES table exists. */
  VerifyEmployeesTable($connection, DB_DATABASE);

  /* Ensure that the patinhos table exists. */
  VerifyPatinhosTable($connection, DB_DATABASE);

  /* If input fields are populated, add a row to the EMPLOYEES table. */
  $employee_name = htmlentities($_POST['NAME']);
  $employee_address = htmlentities($_POST['ADDRESS']);

  if (strlen($employee_name) || strlen($employee_address)) {
    AddEmployee($connection, $employee_name, $employee_address);
  }

  /* If input fields for patinhos are populated, add a row to the patinhos table. */
  $patinho_species = htmlentities($_POST['SPECIES']);
  $patinho_color = htmlentities($_POST['COLOR']);
  $patinho_size = htmlentities($_POST['SIZE']);
  $patinho_birthdate = htmlentities($_POST['BIRTH_DATE']);

  if (strlen($patinho_species) || strlen($patinho_color) || strlen($patinho_size) || strlen($patinho_birthdate)) {
    AddPatinho($connection, $patinho_species, $patinho_color, $patinho_size, $patinho_birthdate);
  }
?>

<!-- Input form for employees -->
<h2>Add Employee Data</h2>
<form action="<?PHP echo $_SERVER['SCRIPT_NAME'] ?>" method="POST">
  <table border="0">
    <tr>
      <td>NAME</td>
      <td>ADDRESS</td>
    </tr>
    <tr>
      <td>
        <input type="text" name="NAME" maxlength="45" size="30" />
      </td>
      <td>
        <input type="text" name="ADDRESS" maxlength="90" size="60" />
      </td>
      <td>
        <input type="submit" value="Add Employee" />
      </td>
    </tr>
  </table>
</form>

<!-- Input form for patinhos -->
<h2>Add Patinho Data</h2>
<form action="<?PHP echo $_SERVER['SCRIPT_NAME'] ?>" method="POST">
  <table border="0">
    <tr>
      <td>Species</td>
      <td>Color</td>
      <td>Size</td>
      <td>Birth Date (YYYY-MM-DD)</td>
    </tr>
    <tr>
      <td>
        <input type="text" name="SPECIES" maxlength="45" size="30" />
      </td>
      <td>
        <input type="text" name="COLOR" maxlength="45" size="30" />
      </td>
      <td>
        <input type="text" name="SIZE" maxlength="10" size="10" />
      </td>
      <td>
        <input type="text" name="BIRTH_DATE" placeholder="YYYY-MM-DD" />
      </td>
      <td>
        <input type="submit" value="Add Patinho" />
      </td>
    </tr>
  </table>
</form>

<!-- Display employees data. -->
<h2>Employee Data</h2>
<table border="1" cellpadding="2" cellspacing="2">
  <tr>
    <td>ID</td>
    <td>NAME</td>
    <td>ADDRESS</td>
  </tr>

<?php

$result_employees = mysqli_query($connection, "SELECT * FROM EMPLOYEES");

while($query_data = mysqli_fetch_row($result_employees)) {
  echo "<tr>";
  echo "<td>",$query_data[0], "</td>",
       "<td>",$query_data[1], "</td>",
       "<td>",$query_data[2], "</td>";
  echo "</tr>";
}
?>

</table>

<!-- Display patinhos data. -->
<h2>Patinho Data</h2>
<table border="1" cellpadding="2" cellspacing="2">
  <tr>
    <td>ID</td>
    <td>Species</td>
    <td>Color</td>
    <td>Size</td>
    <td>Birth Date</td>
  </tr>

<?php

$result_patinhos = mysqli_query($connection, "SELECT * FROM patinhos");

while($query_data = mysqli_fetch_row($result_patinhos)) {
  echo "<tr>";
  echo "<td>",$query_data[0], "</td>",
       "<td>",$query_data[1], "</td>",
       "<td>",$query_data[2], "</td>",
       "<td>",$query_data[3], "</td>",
       "<td>",$query_data[4], "</td>";
  echo "</tr>";
}
?>

<!-- Clean up. -->
<?php

  mysqli_free_result($result_employees);
  mysqli_free_result($result_patinhos);
  mysqli_close($connection);

?>

</body>
</html>

<?php

/* Add an employee to the table. */
function AddEmployee($connection, $name, $address) {
   $n = mysqli_real_escape_string($connection, $name);
   $a = mysqli_real_escape_string($connection, $address);

   $query = "INSERT INTO EMPLOYEES (NAME, ADDRESS) VALUES ('$n', '$a');";

   if(!mysqli_query($connection, $query)) echo("<p>Error adding employee data.</p>");
}

/* Add a patinho to the table. */
function AddPatinho($connection, $species, $color, $size, $birthdate) {
   $s = mysqli_real_escape_string($connection, $species);
   $c = mysqli_real_escape_string($connection, $color);
   $sz = (int)$size; // Certificar-se de que o tamanho seja um número inteiro.

   $bd = date('Y-m-d', strtotime($birthdate));

   $query = "INSERT INTO patinhos (Species, Color, Size, BirthDate) VALUES ('$s', '$c', $sz, '$bd');";

   if(!mysqli_query($connection, $query)) echo("<p>Error adding patinho data.</p>");
}

/* Check whether the table exists and, if not, create it. */
function VerifyEmployeesTable($connection, $dbName) {
  if(!TableExists("EMPLOYEES", $connection, $dbName))
  {
     $query = "CREATE TABLE EMPLOYEES (
         ID int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
         NAME VARCHAR(45),
         ADDRESS VARCHAR(90)
       )";

     if(!mysqli_query($connection, $query)) echo("<p>Error creating table.</p>");
  }
}

/* Check whether the patinhos table exists and, if not, create it. */
function VerifyPatinhosTable($connection, $dbName) {
  if(!TableExists("patinhos", $connection, $dbName))
  {
     $query = "CREATE TABLE patinhos (
         ID int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
         Species VARCHAR(45),
         Color VARCHAR(45),
         Size INT,
         BirthDate DATE
       )";

     if(!mysqli_query($connection, $query)) echo("<p>Error creating table.</p>");
  }
}

/* Check for the existence of a table. */
function TableExists($tableName, $connection, $dbName) {
  $t = mysqli_real_escape_string($connection, $tableName);
  $d = mysqli_real_escape_string($connection, $dbName);

  $checktable = mysqli_query($connection,
      "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_NAME = '$t' AND TABLE_SCHEMA = '$d'");

  if(mysqli_num_rows($checktable) > 0) return true;

  return false;
}
?>
