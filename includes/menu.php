<?php
/*
@author: Ademcan (ademcan@ademcan.net)
@name: menu.php
@description: menu for the user interface
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


function isLoggedIn()
{
    if(isset($_SESSION['valid']) && $_SESSION['valid'])
        return true;
    return false;
}
?>

<!DOCTYPE html>
<head>
    <?php
    $dbname='snippets.sqlite';
    $base=new SQLite3($dbname);

    $query_name = "SELECT * FROM settings ";
    $results_name = $base->query($query_name);
    $row = $results_name->fetchArray();
    $theme = $row['theme'];
    $user = $row["username"];
    $title = $row["title"];
    ?>
    <link rel="icon" type="image/png" href="images/favicon.png">
    <link rel="alternate" type="application/rss+xml" title="Flux RSS" href="/rss/" />
    <link rel="stylesheet" href="css/flat.css" type="text/css" media="screen" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link href="<?=prismTheme()?>" rel="stylesheet" />
    <script src="js/clipboard.min.js"></script>
    <script src="js/prism.js"></script>
    <title>
        <?php echo $title; ?> prism_plugins
    </title>
</head>

<body>
    <div id="menu">
    <center>
        <img src="images/canSnippetLogo_CE_light.png" style="width:100px; height:90px;margin-bottom:5px;padding-top:10px;"/>
        <form method="POST" name="search" action="action.php" style="margin-bottom:20px; margin-top:20px;">
        <input type="textarea" class="searchBox" name="search" placeholder="<?php echo ($messages['searchboxtext']); ?>" />
        </form>
    </center>
    <a href="index.php" class="button"><?php echo($messages['home']); ?></a>
    <a href="browse.php" class="button"><?php echo($messages['search']); ?></a>
    <a href="rss/" class="button"><?php echo($messages['rss']); ?></a>
    <?php
        if(isLoggedIn())
        {
            echo '<hr>';
            echo '<a href="admin/index.php?view=all" class="button">';
            echo $messages['myaccount'],' </a>';
            echo '<a href="logout.php" class="logoutButton">';
            echo $messages['deconnection'],'</a>';
        }
        else
        {
            echo '<a href="admin/login.php" class="button">';
            echo $messages['identification'],'</a> ';
        }
    ?>

    </div>
    <div id="content">
