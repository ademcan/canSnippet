<?php
/*
  @author: Ademcan (ademcan@ademcan.net)
  @name: login.php
  @description: login page
 */

session_start();
require('../fr.php');
?>
<html>
    <head>
        <link rel="icon" type="image/jpg" href="../images/favicon.png">
        <link rel="stylesheet" href="../css/flat.css" type="text/css" media="screen" />
        <title>Login canSnippet</title>
    </head>
    <body>
        <div id="loginWindow">
            <img src="../images/canSnippetLogoBlack.png" style="width:70px; height:60px;float:left;"/>
            <br><h2>&nbsp;&nbsp;<?php echo($messages['identification']); ?></h2><br>
                <form method="POST" >
                    <table>
                        <tr><td width="200px"><?php echo($messages['username']); ?> :</td><td> <input class="login" type="text" name="username" ></td></tr>
                        <tr><td><?php echo($messages['pwd']); ?> :</td><td> <input class="login" type="password" name="password" ></td></tr>
                    </table>
                  <input type="submit" value="<?php echo($messages['login']); ?>" class="loginButton" />
                </form>
                <div style="padding-top:30px;">
                    <center><a href="../forgotpwd.php"><?php echo($messages['pwdforgotten']); ?></a></center>
                </div>

        </div>

        <div id="registerWindow">
            <br><h2>&nbsp;Nouveau compte</h2><br>
                <form method="POST" >
                    <input type="hidden" value="register" name="register" />
                    <table>
                        <tr><td width="200px">Nom d'utilisateur :</td><td> <input class="login" type="text" name="username2" ></td></tr>
                        <tr><td>Email :</td><td> <input class="login" type="email" name="email2" ></td></tr>
                        <tr><td>Mot de passe :</td><td> <input class="login" type="password" name="password1" ></td></tr>
                        <tr><td>Répéter le mot de passe :</td><td> <input class="login" type="password" name="password2" ></td></tr>
                    </table>
                  <input type="submit" value="Enregistrer" class="loginButton" />
                </form>

        </div>
        <center>
            <br /><br />
        <button type="button" class="homeButton" onclick='document.location.href="/";'>Accueil</button>
        </center>

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

        $username = $_POST['username2'];
        $email = $_POST['email2'];
        $pass1=$_POST['password1'];
        $pass2=$_POST['password2'];

        // must provide a username
        if (strlen($username) < 1){
            ?>
            <script>alert("Veuillez saisir un nom d\'utilisateur")</script>
            <?php
            header('Location: index.php?view=all');
        }
        // if username is present
        else{
            // DO NOT ALLOW THE SAME EMAIL TWICE
            $count_query = "SELECT count(*) AS count FROM user WHERE email=\"".$email."\" ";
            $results_count = $base->query($count_query);
            $row_count = $results_count->fetchArray();
            $snippets_count = $row_count['count'];

            // check that email does not already exist
            if($snippets_count>0){
                ?>
                <script>alert("Cette adresse email est déjà enregistré.")</script>
                <?php
                header('Location: index.php?view=all');
            }
            else{
                if ( strlen($pass1) < 8){
                    ?>
                    <script>
                        alert("Mot de passe trop court!");
                    </script>
                    <?php
                }
                //length is OK
                else {
                    // contains at least one capital letter and one number
                    if (preg_match('/[A-Z]/', $pass1) && preg_match('/[0-9]/', $pass1)){
                        if (strcmp($pass1, $pass2) !== 0){
                            ?>
                            <script>
                                alert("Veuillez saisir deux fois le même mot de passe!");
                            </script>
                            <?php
                        }
                        else {

                            if (preg_match('/[\s\'^£$%&*()}{@#~?><>,|=+¬-]/', $username)){
                                ?>
                                <script>
                                    alert("Charactère invalide pour le nom d'utilisateur");
                                </script>
                                <?php
                            }
                            else{
                                // Send email to admins
                                // $to = "ademcan@ademcan.net"; // this is your Email address
                                $to = "admin@bioinfo-fr.net"; // this is your Email address

                                $from = $_POST['email2']; // this is the sender's Email address
                                $username = SQLite3::escapeString($username);
                                // $usernameDB = mysql_real_escape_string($username);
                                $subject = "Nouveau compte pour canSnippet";
                                $message = "Chèr(e)s admins,\n\nL'utilisateur suivant souhaiterait créer un nouveau compte sur canSnippet\n\nNom d'utilisateur: ".$username."\nAdresse email: ".$from.".\n\nSi vous ne souhaitez pas activer ce compte vous pouvez ignorer cet email.\nPour valider/activer ce compte veuillez vous identifier et vous rendre dans la section 'Utilisateurs' de votre instance canSnippet\n\nvia canSnippet";

                                $header_array = [
                                    "MIME-Version: 1.0",
                                    "Content-type: text/plain; charset=UTF-8",
                                    "From: ".$from."",
                                ];
                                $headers = implode("\r\n", $header_array);
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

                                // Send email to user
                                $to = $_POST['email2']; // this is your Email address
                                $from = "admin@bioinfo-fr.net"; // this is the sender's Email address
                                $username = $_POST['username2'];
                                $username = SQLite3::escapeString($username);
                                $subject = "Nouveau compte pour canSnippet";
                                $message = "Bonjour ".$username.", \n\nNous vous remercions pour la création d'un compte sur canSnippet. Votre compte sera validé sous peu.\n\nLes admins de bioinfo-fr.net";

                                $header_array = [
                                    "MIME-Version: 1.0",
                                    "Content-type: text/plain; charset=UTF-8",
                                    "From: ".$from."",
                                ];
                                $headers = implode("\r\n", $header_array);
                                mail($to,$subject,$message,$headers);

                                ?>
                                    <script>
                                        location.href = '../thankyou.php';
                                    </script>
                                <?php
                            }
                        }
                    }
                    else {
                        ?>
                        <script>
                            alert("Le mot de passe doit contenir au moins une lettre majuscule et un chiffre!");
                        </script>
                        <?php
                    }
                }
            }
        }

    }
    // Login
    else{
        $username = $_POST['username'];
        $password = $_POST['password'];
        //connect to the database here
        $username = SQLite3::escapeString($username);
        $query = "SELECT password, salt, active, status
                FROM user
                WHERE username = '$username';";
        $result = $base-> query($query);

        if( !$result ) //no such user exists
        {
            header('Location: index.php?view=all');
        }

        $userData = $result->fetchArray();
        $hash = hash('sha256', $userData['salt'] . hash('sha256', $password) );

        // incorrect password
        if($hash != $userData['password'])
        {
            ?>
                <script>
                    alert("Nom d'utilisateur ou mot de passe incorrect !");
                    location.href = 'index.php?view=all';
                </script>
            <?php
        }
        // correct pwd
        else
        {
            if( $userData['active'] != 1)
            {
                ?>
                    <script>
                        alert("Votre compte a été inactivé. Veuillez contacter les admins!");
                        location.href = 'index.php?view=all';
                    </script>
                <?php
            }
            else{

                $_SESSION['admin'] = $userData['status'];
                $_SESSION['valid'] = True;
                $_SESSION['username'] = $username;
                echo $_SESSION;
                ?>
                    <script>

                        location.href = '../admin/index.php?view=all';
                    </script>
                <?php
            }
        }

    }

}
?>
