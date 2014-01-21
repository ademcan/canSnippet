<?php
/*
  @author: Ademcan (ademcan@ademcan.net)
  @name: install.php
  @description: installation page
  REMOVE AFTER SUCCESSFUL INSTALLATION
 */
include "autoload.php";

$base = Factory::database($parameters);

if ($base->exist()) {
    echo "Installation process already done";
} else {

?>
<head>
    <link rel="stylesheet" href="css/flat.css" type="text/css" media="screen" />
    <title>
        Installation of canSnippets
    </title>
</head>
<html>
    <body>
        <div id="installWindow">
            <b><font color="#27ae60">Welcome to the installation page of canSnippet <br>
            Please fill in the following form </font></b><br><br><br>
            <center>
            <form name="register" action="install.php" method="post">
                <table>
                <tr><td width="150px">Title :</td><td> <input type="text" name="title" /></td></tr>
                <tr><td>Username :</td><td> <input type="text" name="username" maxlength="30" /></td></tr>
                <tr><td>Password :</td><td> <input type="password" name="pass1" /></td></tr>
                <tr><td>Password Again :</td><td> <input type="password" name="pass2" /></td></tr>
                </table>
                </center>
                <input type="submit" value="Install" class="installButton"/>
            </form>  
            
        </div>

        <?php
        // INSTALLATION
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            // Create users table
            if (!extension_loaded($parameters["db"]["driver"])) {
                die('PDO unavailable');
            }

            $base = Factory::database($parameters);

            $query = "CREATE TABLE user(
                    username VARCHAR(30) NOT NULL UNIQUE,
                    password VARCHAR(64) NOT NULL,
                    salt VARCHAR(3) NOT NULL,
                    PRIMARY KEY(username)
                )";
            $results = $base->execute($query);

            //retrieve our data from POST
            $username = $_POST['username'];
            $pass1 = $_POST['pass1'];
            $pass2 = $_POST['pass2'];
            // check if both passwords are identical
            if ($pass1 != $pass2)
                header('Location: install.php');
            // limit the size of the username to 30 characters
            if (strlen($username) > 30)
                header('Location: install.php');
            $hash = hash('sha256', $pass1);

            //creates a 3 character sequence for salt
            function createSalt() {
                $string = md5(uniqid(rand(), true));
                return substr($string, 0, 3);
            }

            $salt = createSalt();
            $hash = hash('sha256', $salt . $hash);

            // special characters protection
            $title = htmlentities($_POST['title'],ENT_QUOTES);
            
            // Add the user to the database
            $addUser = "INSERT INTO user(username, password, salt)
                VALUES ('$username' , '$hash' ,'$salt')";
            $base->execute($addUser);

            // Create snippets table
            $createSnippetsDatabase = "CREATE TABLE snippets(
                ID INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
                language longtext,
                description longtext,
                name longtext,
                code longtext,
                date date,
                private integer
                )";
            $base->execute($createSnippetsDatabase);

            // Create settings table
            $createSettingsDatabase = "CREATE TABLE settings(
                ID INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
                username longtext,
                title longtext,
                theme longtext
                )";
            $base->execute($createSettingsDatabase);

            // Add default settings to database  
            $addDefaultSettings = "INSERT INTO settings(username, title, theme)
                VALUES ('$username' , '$title', 'flat')";
            $base->execute($addDefaultSettings);
            
            ?>
            <script>
                location.href = 'index.php';
            </script>
            <?php
        }
        ?>
    </body>
</html>

<?php } ?>