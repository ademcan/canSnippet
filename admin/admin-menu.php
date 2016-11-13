<?php
/*
  @author: Ademcan (ademcan@ademcan.net)
  @name: admin-menu.php
  @description: menu for the admin panel
 */

 ini_set('display_errors', 'On');
 error_reporting(E_ALL);

if (!isset($_SESSION))
    session_start();

require_once('../config.php');

// we block the acces to no authenticated people
if (!isset($_SESSION['valid']) || !$_SESSION['valid']) {
    header("location:login.php");
}
else {
?>

<html>
    <head>
        <?php
        $dbname = '../snippets.sqlite';
        $base = new SQLite3($dbname);

        $username = $_SESSION['username'];
        $queryU = "SELECT * FROM user WHERE username=\"".$username."\" ";
        $resultsU = $base->query($queryU);
        $rowU = $resultsU->fetchArray();
        $status = $rowU["status"];

        // $dbname = '../snippets.sqlite';
        // $base = new SQLite3($dbname);

        $queryT = "SELECT * FROM settings ";
        $resultsT = $base->query($queryT);
        $rowT = $resultsT->fetchArray();
        $themeT = $rowT['theme'];
        echo '<link rel="stylesheet" href="../css/flat.css" type="text/css" media="screen" />';
        ?>
        <link rel="icon" type="image/jpg" href="../images/canLogo.jpg">
        <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-15">
        <link href="../<?=prismTheme()?>" rel="stylesheet" />
        <script src="../js/prism.js"></script>
        <title>
            canSnippet administration
        </title>
    </head>

    <body>
        <div id="menu">
            <?php echo 'Logged in as <b>'.$username.'</b><br>Status: <b>'.$status.'</b>'; ?>
            <a href="index.php" class="button"> My snippets </a>
            <a href="action.php?action=add" class="button"> New snippet </a>

            <?php

            if ($status == "admin"){
                echo '<a href="action.php?action=users" class="button"> Users </a>';
            }
            ?>

            <a href="action.php?action=preferences" class="button"> Preferences </a>
            <hr>
            <a href="../index.php" class="button"> Back to canSnippet </a>
            <a href="../logout.php" class="logoutButton"> Logout </a>
        </div>
    <div id="content">
<?php } ?>
