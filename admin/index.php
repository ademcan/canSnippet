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

    // $count_query = "SELECT count(*) as count FROM $mytable";
    // $results_count = $base->query($count_query);
    // $row_count = $results_count->fetchArray();
    // $snippets_count = $row_count['count'];

    $view = $_GET['view'];
    if ($view == "all"){
        $count_query = "SELECT count(*) as count FROM $mytable WHERE username=\"".$username."\"";
        $results_count = $base->query($count_query);
    }
    elseif ($view == "public"){
        $count_query = "SELECT count(*) as count FROM $mytable WHERE username=\"".$username."\" AND private='off'";
        $results_count = $base->query($count_query);
    }
    else {
        $count_query = "SELECT count(*) as count FROM $mytable WHERE username=\"".$username."\" AND private='on' ";
        $results_count = $base->query($count_query);
    }

    // $count_query = "SELECT count(*) as count FROM $mytable WHERE username=\"".$username."\"";
    // $results_count = $base->query($count_query);
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


    // if ($status == "admin"){
    //     // If admin, show all the snippets
    //     $query_name = "SELECT * FROM $mytable ORDER BY date ASC LIMIT $start_count,$limit";
    //     $title = "All snippets";
    // }
    // else {
    //     // If not admin show only user's snippets
    //
    // }




    if ($view == "all"){
        $query_name = "SELECT * FROM $mytable WHERE username=\"".$username."\" ORDER BY date DESC LIMIT $start_count,$limit";
    }
    elseif ($view == "public"){
        $query_name = "SELECT * FROM $mytable WHERE username=\"".$username."\" AND private='off' ORDER BY date DESC LIMIT $start_count,$limit";
    }
    else {
        $query_name = "SELECT * FROM $mytable WHERE username=\"".$username."\" AND private='on' ORDER BY date DESC LIMIT $start_count,$limit";
    }

    // $query_name = "SELECT * FROM $mytable WHERE username=\"".$username."\" ORDER BY date DESC LIMIT $start_count,$limit";



    $title = "Mes snippets (".$snippets_count.")";

    $results_name = $base->query($query_name);

    echo '<div id="validationMessage" style="padding-left:10px;padding-top:5px;position:fixed; width:100%; height:30px;background-color:orange;display:none;">Merci. Votre nouveau snippet sera validé sous peu.</div>';

    if( isset($_SESSION['newSnippet'])){
        if ($_SESSION['newSnippet'] == 1){
            ?>
                <script>
                    document.getElementById("validationMessage").style.display = 'block';
                </script>
            <?php
            $_SESSION['newSnippet'] = 0;
        }
    }



    echo '<h1>'.$title.'</h1>';

    echo '<div style="margin-left:20px;"><b>Visualiser</b> : <a href="index.php?view=all" class="editButton" style="margin-right:10px !important; ">Tous</a>';
    echo '<a href="index.php?view=public" class="greenButton" style="margin-right:10px !important; ">Actifs</a>';
    echo '<a href="index.php?view=private" class="deleteButton">Inactifs</a></div><div id="newSnippet">';

    if ($snippets_count == 0){
        echo "Vous n'avez pas encore de snippets. Utilisez le menu 'Ajouter un snippet' pour commencer.";
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
        $tags = $row['tags'];

        $id = $row['ID'];

        if ($private=="on"){
            echo '<div style="background-color:#BDEDFF;margin-top:20px;"><h2><font color="#27ae60">'.$name.' ['.$language.']</font><a href="action.php?action=activate&id=' . $row['ID'] . '" onclick="return confirm(\'Souhaitez-vous vraiment activer ce snippet ?\');"><img src="../images/lockFlat.png" style="width:20px; height: 20px;padding-left:10px;" /></a></h2>';
        }
        else {
            echo '<div style="margin-top:20px;"><h2><font color="#27ae60">'.$name.' ['.$language.']</font><a href="action.php?action=deactivate&id=' . $row['ID'] . '" onclick="return confirm(\'Souhaitez-vous vraiment désactiver ce snippet ?\');"><img src="../images/lockFlat_open.png" style="width:20px; height: 20px;padding-left:10px;" /></a></h2>';
            // echo '<div style="margin-top:20px;"><h2><font color="#27ae60">'.$name.' ['.$language.']</font></h2>';
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
            echo '<img src="../images/author.png" style="width:13px; height:13px;padding-left:10px;padding-right:10px;"/><font size="4"><b>'.$username.'</b></font><br>';
        }

        $date=date_create($date);
        $formattedDate = date_format($date,"d.m.Y");
        echo '<img src="../images/calendar.png" style="width:13px; height:13px;padding-left:10px;padding-right:10px;"/>'.$formattedDate.'';
        echo '<br><img src="../images/comment.png" style="width:13px; vertical-align: middle; height:13px;padding-left:10px;padding-right:10px;"/>';
        echo ''.nl2br($description);

        echo '<br /><div style="word-wrap: break-word;"><img src="../images/tag.png" style="width:13px; height:13px;padding-left:10px;padding-right:10px;"/>';
        $tagsList=explode(",",$tags);
        foreach($tagsList as $var){
            // $lowtag = strtolower($var);
            // $lowtag = str_replace(' ', '', $var);

            $lowtag=$var;
            echo '<u>'.$lowtag.'</u>&nbsp;';
        }
        echo '</div>';
        echo '<section class="'.$languageClass.'"> <pre class="line-numbers"><code>'.$code.'</code></pre> </section>';
        echo '<a href="action.php?action=edit&id=' . $row['ID'] . '" class="editButton">Editer</a>';
        echo '<a href="action.php?action=delete&id=' . $row['ID'] . '" onclick="return confirm(\'Souhaitez-vous vraiment supprimer ce snippet ?\');" class="deleteButton">Supprimer</a>';
        echo '<hr><br></div>';
    }

    echo "<br><br>";

    // Pagination
    // First page
    if ($snippets_count > $limit & $page == 1) {
        echo '<center><a href= "index.php?view='.$view.'&page=2"> Anciens snippets >>> </a></center>';
    }
    // Last page
    if ($page > 1 & $snippets_count <= ($limit * $page) & $snippets_count > ($limit * ($page - 1))) {
        echo '<center><a href= "index.php?view='.$view.'&page=' . ($page - 1) . '"> <<< Snippets récents </a></center>';
    }
    // Middle page
    if ($page > 1 & $snippets_count > ($limit * $page)) {
        echo '<center><a href= "index.php?view='.$view.'&page=' . ($page - 1) . '"> <<< Snippets récents</a> -- <a href="index.php?page=' . ($page + 1) . '">Older snippets >>></a></center>';
    }
    ?>

    </center><br><br></div></body></html>

            <?php
        } else {
            header("location:login.php");
        }
    ?>
