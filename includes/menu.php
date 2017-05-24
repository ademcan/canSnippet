<?php
/*
@author: Ademcan (ademcan@ademcan.net)
@name: menu.php
@description: menu for the user interface
*/
require_once("./config.php");

function isLoggedIn()
{
    if(isset($_SESSION['valid']) && $_SESSION['valid'])
        return true;
    return false;
}
?>

<html>
    <head>
        <link rel="icon" type="image/png" href="images/logo_bioinfofr.png">
        <link rel="alternate" type="application/rss+xml" title="Flux RSS" href="/rss/" />
        <?php
        $dbname='snippets.sqlite';
        $base=new SQLite3($dbname);

        $query_name = "SELECT * FROM settings ";
        $results_name = $base->query($query_name);
        $row = $results_name->fetchArray();
        $theme = $row['theme'];
        $user = $row["username"];
        $title = $row["title"];

        echo '<link rel="stylesheet" href="css/flat.css" type="text/css" media="screen" />';
        ?>
        <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-15">
        <link href="<?=prismTheme()?>" rel="stylesheet" />
        <script src="js/prism.js"></script>
        <title>
            <?php echo $title; ?> prism_plugins
        </title>
    </head>

    <body>
        <div id="menu">
        <center>
            <img src="images/logo_bioinfofr.png" style="width:200px; height:120px;margin-top:-35px;margin-bottom:5px;padding-bottom:10px;"/>
            <img src="images/bubbles_bioinfofr.png" style="width:200px; height:40px;margin-top:-35px;margin-bottom:5px;"/>
            <form method="POST" name="search" action="action.php" style="margin-bottom:2px;">
            <input type="textarea" class="searchBox" name="search" placeholder="Recherche snippet par tag, titre..."/>
            </form>
        </center>
        <a href="index.php" class="button"> Accueil </a>
        <a href="browse.php" class="button"> Recherche </a>
        <a href="rss/" class="rssButton">RSS </a>
        <?php
            if(isLoggedIn())
            {
                echo '<hr>';
//                echo '<br>Welcome ', $_SESSION['username'] ,' ';
                echo '<a href="admin/index.php" class="button">';
                echo 'Admin </a>';
                echo '<a href="logout.php" class="logoutButton">';
                echo 'Logout </a>';
            }
            else
            {
                echo '<a href="admin/login.php" class="button">';
                echo ' Identification / Nouveau compte </a> ';
            }
        ?>

        </div>
        <div id="content">
