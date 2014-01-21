<?php 
/*
@author: Ademcan (ademcan@ademcan.net)
@name: menu.php
@description: menu for the user interface
*/

function isLoggedIn()
{
    if(isset($_SESSION['valid']) && $_SESSION['valid'])
        return true;
    return false;
}
?>
    
<html>
    <head>
        <link rel="icon" type="image/jpg" href="images/canLogo.jpg">
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
        <link href="css/prism.css" rel="stylesheet" />
        <script src="js/prism.js"></script>
        <title>
            <?php echo $title; ?>
        </title>
    </head>
        
    <body>
        <div id="menu">
        <center>
            <img src="images/canSnippetLogoWhite.png" style="width:70px; height:60px;margin-top:-35px;margin-bottom:5px;"/>
            <form method="POST" name="search" action="action.php" style="margin-bottom:2px;">
            <input type="textarea" class="searchBox" name="search"/>
            <img src="images/searchFlat.png" style="width:16px; height:16px;vertical-align: middle;margin-left: 5px;"/>
            </form>
        </center>
        <a href="index.php" class="button"> Home </a>
        <a href="browse.php" class="button"> Browse </a>
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
                echo ' Login </a> ';
            }
        ?>
        
        </div>
        <div id="content">