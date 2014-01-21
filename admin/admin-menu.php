<?php
/*
  @author: Ademcan (ademcan@ademcan.net)
  @name: admin-menu.php
  @description: menu for the admin panel
 */
include "../autoload.php";

if (!isset($_SESSION))
    session_start();
?>

<html>
    <head>
        <?php
        $base = Factory::database($parameters);
        $queryT = "SELECT * FROM settings ";
        $rowT = $base->execute($queryT)->fetchArray();
        $themeT = $rowT['theme'];
        echo '<link rel="stylesheet" href="../css/flat.css" type="text/css" media="screen" />';
        ?>
        <link rel="icon" type="image/jpg" href="../images/canLogo.jpg">
        <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-15">
        <link href="../css/prism.css" rel="stylesheet" />
        <script src="../js/prism.js"></script>
        <title>
            canSnippet administration
        </title>
    </head>

    <body>
        <div id="menu">
            <a href="index.php" class="button"> My snippets </a>
            <a href="action.php?action=add" class="button"> New snippet </a>
            <a href="action.php?action=preferences" class="button"> Preferences </a>
            <hr>
            <a href="../index.php" class="button"> Back to canSnippet </a>
            <a href="../logout.php" class="logoutButton"> Logout </a>
        </div>
    <div id="content">