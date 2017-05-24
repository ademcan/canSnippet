<?php
/*
  @author: Ademcan (ademcan@ademcan.net)
  @name: login.php
  @description: login page
 */

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
            <br><h2>&nbsp;&nbsp;Identification</h2><br>
                <form method="POST" >
                    <table>
                        <tr><td width="200px">Username :</td><td> <input class="login" type="text" name="username" ></td></tr>
                        <tr><td>Password :</td><td> <input class="login" type="password" name="password" ></td></tr>
                    </table>
                  <input type="submit" value="Login" class="loginButton" />
                </form>
        </div>

        <div id="registerWindow">
            <br><h2>&nbsp;&nbsp;Nouveau compte</h2><br>
                <form method="POST" >
                    <input type="hidden" value="register" name="register" />
                    <table>
                        <tr><td width="200px">Username :</td><td> <input class="login" type="text" name="username2" ></td></tr>
                        <tr><td>Email :</td><td> <input class="login" type="email" name="email2" ></td></tr>
                        <tr><td>Password :</td><td> <input class="login" type="password" name="password1" ></td></tr>
                        <tr><td>Repeat password :</td><td> <input class="login" type="password" name="password2" ></td></tr>
                    </table>
                  <input type="submit" value="Request account" class="loginButton" />
                </form>

        </div>
    </body>
<html>

<?php

// Checking all login information
if($_SERVER['REQUEST_METHOD'] == 'POST'){


    $dbname='../snippets.sqlite';
    $mytable ="user";

    if(!class_exists('SQLite3'))
      die("SQLite 3 NOT supported.");

    $base=new SQLite3($dbname);

    // ask for registration
    if($_POST['register']){


        $pass1=$_POST['password1'];
        $pass2=$_POST['password2'];

        if (strcmp($pass1, $pass2) !== 0){
            ?>
            <script>
                alert("Your passwords do not match!");
            </script>
            <?php
        }

        else {
            $to = "ademcan@ademcan.net"; // this is your Email address
            $from = $_POST['email2']; // this is the sender's Email address
            $username = $_POST['username2'];
            $subject = "Nouveau compte pour canSnippet";
            $message = utf8_encode("Chér(e)s admins,\n\nL'utilisateur suivant souhaiterai créer un nouveau compte sur canSnippet\n\nNom d'utilisateur: ".$username."\nAdresse email: ".$from.".\n\nSi vous ne souhaitai pas activer ce compte vous pouvez ignorer cet email.\nPour valider/activer ce compte veuillez vous identifié et vous rendre dans la seciton 'Utilisateurs' de votre instance canSnippet\n\nvia canSnippet");

            $headers  = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/plain; charset=UTF-8' . "\r\n";
            $headers .= 'From: '.$from.'' . "\r\n";
            mail($to,$subject,$message,$headers);


            $hash = hash('sha256', $pass1);
            //creates a 3 character sequence for salt
            function createSalt() {
                $string = md5(uniqid(rand(), true));
                return substr($string, 0, 3);
            }
            $salt = createSalt();
            $hash = hash('sha256', $salt . $hash);
            $status = "noadmin";
            $addUser = "INSERT INTO user(username, status, password, salt, active, email) VALUES ('$username', '$status' , '$hash' ,'$salt', 0, '$from')";
            $base->exec($addUser);

            ?>
                <script>
                    location.href = '../thankyou.php';
                </script>
            <?php
        }
    }

    else{
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
                    location.href = '../admin/index.php';
                </script>
            <?php
        }

    }

}
?>
