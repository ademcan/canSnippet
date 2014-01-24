<?php
/*
  @author: Ademcan (ademcan@ademcan.net)
  @name: index.php
  @description: index page for the admin panel
 */

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

    $query_name = "SELECT * FROM $mytable ORDER BY date DESC LIMIT $start_count,$limit";
    $results_name = $base->query($query_name);

    echo '<h1>My snippets</h1><div id="newSnippet">';

    if ($snippets_count == 0){
        echo "You don't have any snippet yet, you can add new snippets from 'New snippet' menu.";
    }

    //load conf for prism modification
    $conf_mod = conf_mod_prism_loader("../js");
    $pre_class = "";
    for ($i=0; $i < count($conf_mod); $i++)
        if(isset($conf_mod[$i]->pre))
            $pre_class = " ".$conf_mod[$i]->pre;
    
    // Loop and write all the recent snippets
    while($row = $results_name->fetchArray())
    {
        $name = $row['name'];
        $code = $row['code'];
        $language = $row['language'];
        $private = $row['private'];
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
        
        echo '<font size="1"><i>'.$language.'</i> - '.$date.'</font>';
        echo '<br><img src="../images/info.png" style="vertical-align: middle;"/>';
        echo '&nbsp;&nbsp;'.nl2br($description);
        echo '<section class="' . $languageClass . '"> <pre class="'.$pre_class.'"><code>' . $row['code'] . '</code></pre> </section>';
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

