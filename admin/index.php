<?php
/*
  @author: Ademcan (ademcan@ademcan.net)
  @name: index.php
  @description: index page for the admin panel
 */

 ini_set('display_errors', 'On');
 error_reporting(E_ALL);

session_start();

if (isset($_SESSION['valid']) && $_SESSION['valid']) {
    // CONNECTION TO THE DATABASE
    include 'admin-menu.php';
    $dbname = '../snippets.sqlite';
    $mytable = "snippets";
    $base = new SQLite3($dbname);

    $count_query = "SELECT count(*) as count FROM $mytable";
    $results_count = $base->query($count_query);
    $row_count = $results_count->fetchArray();
    $snippets_count = $row_count['count'];
    $limit = 10;
    $page = 1;

    if (isset($_GET['page'])) {
        $page = $_GET['page'];
    }
    $start_count = $limit * ($page - 1);

    $username = $_SESSION['username'];


    $queryU = "SELECT * FROM user WHERE username=\"".$username."\" ";
    $resultsU = $base->query($queryU);
    $rowU = $resultsU->fetchArray();
    $status = $rowU["status"];


    if ($status == "admin"){
        // If admin, show all the snippets
        $query_name = "SELECT * FROM $mytable ORDER BY date DESC LIMIT $start_count,$limit";
        $title = "All snippets";
    }
    else {
        // If not admin show only user's snippets
        $query_name = "SELECT * FROM $mytable WHERE username=\"".$username."\" ORDER BY date DESC LIMIT $start_count,$limit";
        $title = "My snippets";
    }




    $results_name = $base->query($query_name);

    echo '<h1>'.$title.'</h1><div id="newSnippet">';

    if ($snippets_count == 0){
        echo "You don't have any snippet yet, you can add new snippets from 'New snippet' menu.";
    }

    // Loop and write all the recent snippets
    while($row = $results_name->fetchArray())
    {
        $username = $row['username'];
        $name = $row['name'];
        $code = $row['code'];
        $language = $row['language'];
        $private = $row['private'];
        $lines = $row['lines'];
        $highlight = $row['highlight'];
        $date = $row['date'];
        $description = $row['description'];
        $id = $row['ID'];
        if ($private=="on"){
            echo '<h2><font color="#27ae60">'.$name.'</font><img src="../images/lockFlat.png" style="width:20px; height: 20px;padding-left:10px;" /></h2>';
        }
        else{
            echo '<h2><font color="#27ae60">'.$name.'</font></h2>';
        }

        if ($language=="html"){
            $languageClass = "language-markup";
        }
        else if ($language=="text"){
            $languageClass = "language-markup";
        }
        else{
            $languageClass = "language-".$language;
        }

        // If admin is connected, show the snippet's creator
        if ($status == "admin"){
            echo '<font size="4">Author: <b>'.$username.'</b><br>';
        }

        echo '<font size="4"><i>'.$language.'</i> - '.$date.'</font>';
        echo '<br><img src="../images/info.png" style="vertical-align: middle;"/>';
        echo '&nbsp;&nbsp;'.nl2br($description);
        echo '<section class="'.$languageClass.'"> <pre class="line-numbers"><code>'.$code.'</code></pre> </section>';
        echo '<a href="action.php?action=edit&id=' . $row['ID'] . '" class="editButton">Edit</a>';
        echo '<a href="action.php?action=delete&id=' . $row['ID'] . '" onclick="return confirm(\'Do you really want to delete this snippet ?\');" class="deleteButton">Delete</a>';
        echo '<hr><br>';
    }

    echo "<br><br>";

    // Pagination
    // First page
    if ($snippets_count > $limit & $page == 1) {
        echo '<center><a href= "index.php?page=2"> Older snippets >>> </a></center>';
    }
    // Last page
    if ($page > 1 & $snippets_count <= ($limit * $page) & $snippets_count > ($limit * ($page - 1))) {
        echo '<center><a href= "index.php?page=' . ($page - 1) . '"> <<< Newer snippets </a></center>';
    }
    // Middle page
    if ($page > 1 & $snippets_count > ($limit * $page)) {
        echo '<center><a href= "index.php?page=' . ($page - 1) . '"> <<< Newer snippets</a> -- <a href="index.php?page=' . ($page + 1) . '">Older snippets >>></a></center>';
    }
    ?>

    </center><br><br></div></body></html>

            <?php
        } else {
            header("location:login.php");
        }
        ?>
