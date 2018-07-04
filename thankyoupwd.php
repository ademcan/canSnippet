

<?php
/*
  @author: Ademcan (ademcan@ademcan.net)
  @name: thankyou.php
  @description: successffull password renewal
 */

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
</head>
<body>
    <center>
        <img src="images/canSnippetLogo_CE_200x200_new.png" style="width:200px;padding-top:50px;"/><br />
    <div style="">
        <br /><br />
        <?php echo($messages['successfullnewpwd']); ?><br /><br />
        <button type="button" class="homeButton" onclick='document.location.href="admin/login.php";'><?php echo($messages['connect']); ?></button>
    </div>

    </center>

</body>

</html>
