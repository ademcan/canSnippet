<?php
/*
  @author: Ademcan (ademcan@ademcan.net)
  @name: login.php
  @description: login page
 */
include "../autoload.php";

session_start();
?>
<html>
    <head>
        <link rel="stylesheet" href="../css/flat.css" type="text/css" media="screen" />
        <title>Login canSnippet</title>
    </head>
    <body>
        <div id="loginWindow">
            <img src="../images/canSnippetLogoBlack.png" style="width:70px; height:60px;float:left;"/>
            <br><h2>&nbsp;&nbsp;Login</h2><br>
                <form method="POST" >
                    <table>
                        <tr><td width="100px">Username :</td><td> <input class="login" type="text" name="username" ></td></tr>
                        <tr><td>Password :</td><td> <input class="login" type="password" name="password" ></td></tr>
                    </table>
                  <input type="submit" value="Login" class="loginButton" />
                </form>
        </div>
    </body>
<html>

<?php

// Checking all login information 
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Create users table
    $mytable ="user";

    if(!class_exists('SQLite3'))
      die("SQLite 3 NOT supported.");

    $base = Factory::database($parameters);

    $username = $_POST['username'];
    $password = $_POST['password'];
    //connect to the database here
    $username = SQLite3::escapeString($username);
    $query = "SELECT password, salt
            FROM user
            WHERE username = '$username';";
    $result = $base-> query($query);
    
    if( !$result ) //no such user exists
    {
        header('Location: index.php');
    }
    
    $userData = $result->fetchArray();
    $hash = hash('sha256', $userData['salt'] . hash('sha256', $password) );
    
    // incorrect password
    if($hash != $userData['password']) 
    {
        ?>
            <script>
                alert("Wrong Username or Password !");
                location.href = 'index.php';
            </script>
        <?php
    }
    //login successful
    else
    {
        $_SESSION['valid'] = 1;
        $_SESSION['username'] = $username;
        ?>
            <script>
                location.href = '../index.php';
            </script>
        <?php
    }   
}
?>
