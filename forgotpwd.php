<?php
/*
  @author: Ademcan (ademcan@ademcan.net)
  @name: forgotpwd.php
  @description: Renew password page
 */

session_start();
require_once("./config.php");
$lng = language();

switch ($lng) {
    case "en":
        require("./en.php");
        break;
    case "fr":
        require("./fr.php");
        break;
}

?>
<html>
    <head>
        <link rel="icon" type="image/jpg" href="images/favicon.png">
        <link rel="stylesheet" href="css/flat.css" type="text/css" media="screen" />
        <title>Login canSnippet</title>
    </head>
    <body>
        <div id="loginWindow">
            <img src="images/canSnippetLogoBlack.png" style="width:70px; height:60px;float:left;"/>
            <br><h2><?php echo($messages['forgotpwdtitle']); ?></h2><br>
                <form method="POST" >
                    <table>
                        <tr><td width="200px">Email:</td><td> <input class="login" type="email" name="email" ></td></tr>
                    </table>
                  <input type="submit" value="<?php echo($messages['forgotpwdbutton']); ?>" class="loginButton" />
                </form>
        </div>
        <center>
    </body>
<html>



<?php
    // send email to the user to renew pwd
    if($_SERVER['REQUEST_METHOD'] == 'POST'){

        $dbname='snippets.sqlite';
        if(!class_exists('SQLite3'))
          die("SQLite 3 NOT supported.");
        $base=new SQLite3($dbname);

        $email = $_POST['email'];
        // check if email exists in DB
        $count_query = "SELECT count(*) AS count FROM user WHERE email=\"".$email."\" ";
        $results_count = $base->query($count_query);
        $row_count = $results_count->fetchArray();
        $snippets_count = $row_count['count'];
        $username = $_POST['username2'];
        if($snippets_count>0){
            // generate unique token
            $randomNum=substr(str_shuffle("0123456789abcdefghijklmnopqrstvwxyz"), 0, 16);
            // save token to token table
            $date = date("Y-m-d H:i:s");
            $addToken = "INSERT INTO pwdtoken(email, token, timestamp) VALUES ('$email', '$randomNum' , '$date' )";
            $base->exec($addToken);

            // Send email to user
            $from = "canSnippet"; // this is the sender's Email address
            $subject = "".$messages['newpwdmailsubject']."";

            $rawurl = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            $url = explode('/', $rawurl );
            array_pop($url);
            $url2=implode("/",$url);
            $message = "".$messages['newpwdmailbody']."".$url2."/renewpwd.php?token=".$randomNum."";

            $header_array = [
                "MIME-Version: 1.0",
                "Content-type: text/plain; charset=UTF-8",
                "From: ".$from."",
            ];
            $headers = implode("\r\n", $header_array);
            mail($email,$subject,$message,$headers);

            ?>
                <script>
                    location.href = 'thankyou.php';
                </script>
            <?php

        }

        else{
            ?>
            <script>alert("<?php echo($messages['emailaddresserror']); ?>")</script>
            <?php
        }
    }
?>
