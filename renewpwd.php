<?php
/*
  @author: Ademcan (ademcan@ademcan.net)
  @name: login.php
  @description: login page
 */

     include "config.php";
     $lng = language();

     switch ($lng) {
         case "en":
             require("./en.php");
             break;
         case "fr":
             require("./fr.php");
             break;
     }

    $token = $_GET['token'];
    $date = date("Y-m-d H:i:s");
    // echo $date;
    $dbname='snippets.sqlite';
    if(!class_exists('SQLite3'))
      die("SQLite 3 NOT supported.");
    $base=new SQLite3($dbname);

    // check if email exists in DB
    $count_query = "SELECT * from pwdtoken WHERE token=\"".$token."\" ";
    $results_count = $base->query($count_query);
    $row_count = $results_count->fetchArray();
    $email = $row_count['email'];
    $timestamp = date($row_count['timestamp']);
    $seconds = strtotime($date) - strtotime($timestamp);


    if ($seconds > 300){
        echo '<html><head><link rel="icon" type="image/jpg" href="images/favicon.png"><link rel="stylesheet" href="css/flat.css" type="text/css" media="screen" /><title>',$messages['forgotpwdtitle'],'</title></head><body><div id="loginWindow"><img src="images/canSnippetLogoBlack.png" style="width:70px; height:60px;float:left;"/><br /><center><h2>&nbsp;&nbsp;',$messages['linkexpired'],'</h2><br /><button type="button" class="homeButton" onclick="document.location.href=\'forgotpwd.php\';" ">',$messages['forgotpwdbutton'],'</button></center><br></body><html>';
    }
    else {
        echo '<html><head><link rel="icon" type="image/jpg" href="images/favicon.png"><link rel="stylesheet" href="css/flat.css" type="text/css" media="screen" /><title>',$messages['forgotpwdtitle'],'</title></head><body><div id="loginWindow"><img src="images/canSnippetLogoBlack.png" style="width:70px; height:60px;float:left;"/><br><h2>&nbsp;&nbsp;',$messages['forgotpwdtitle'],'</h2><br><form method="POST" ><table><tr><td width="200px">',$messages['newpwd'],':</td><td> <input class="login" type="password" name="pwd1" ></td></tr><tr><td width="200px">',$messages['repeatpassword'],':</td><td> <input class="login" type="password" name="pwd2" ></td></tr></table><input type="submit" value="',$messages['forgotpwdbutton'],'" class="loginButton" /></form></div><center></body><html>';
    }


    if($_SERVER['REQUEST_METHOD'] == 'POST'){

        $pass1=$_POST['pwd1'];
        $pass2=$_POST['pwd2'];

        if ( strlen($pass1) < 8){
            ?>
            <script>
                alert("<?php echo $messages['pwdtooshort'];  ?>");
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
                        alert("<?php echo $messages['repeatsamepwd'];  ?>");
                    </script>
                    <?php
                }
                else {

                    if (preg_match('/[\s\'^£$%&*()}{@#~?><>,|=+¬-]/', $username)){
                        ?>
                        <script>
                            alert("<?php echo $messages['invalidchar'];  ?>");
                        </script>
                        <?php
                    }
                    else{
                        // Update pwd and salt
                        $hash = hash('sha256', $pass1);
                        //creates a 3 character sequence for salt
                        function createSalt() {
                            $string = md5(uniqid(rand(), true));
                            return substr($string, 0, 3);
                        }
                        $salt = createSalt();
                        $hash = hash('sha256', $salt . $hash);
                        echo "UPDATE user SET password='$hash', salt='$salt' WHERE email=\"".$email."\" ";
                        $updateUser = "UPDATE user SET password='$hash', salt='$salt' WHERE email=\"".$email."\" ";
                        $base->exec($updateUser);
                        ?>
                        <script>
                            location.href = 'thankyoupwd.php';
                        </script>
                        <?php
                    }
                }
            }
            else {
                ?>
                <script>
                    alert("<?php echo $messages['onecapitalonenumber'];  ?>");
                </script>
                <?php
            }
        }
    }


?>
