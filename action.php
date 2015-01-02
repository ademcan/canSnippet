<?php
/*
  @author: Ademcan (ademcan@ademcan.net)
  @name: action.php
  @description: action class for browse snippets page
*/
    include "config.php";

    session_start();
    $mytable ="snippets";
    $base=new SQLite3($config["dbname"]);

    if(isset($_POST["action"]) && $_POST['action']=="getcode"){
        $id = $_POST["id"];
        // get entry
        $query_language = "SELECT * FROM $mytable where id = '$id' ";
        $results_language = $base->query($query_language);
        while($row = $results_language->fetchArray())
        {
            $code = iconv("ISO-8859-15", "UTF-8",$row['code']);
            $language = $row['language'];
            $date = $row['date'];
            $name = iconv("ISO-8859-15", "UTF-8",$row['name']);
            $private = $row['private'];
            $description = iconv("ISO-8859-15", "UTF-8",$row['description']);
            if ($private=="on"){
                echo '<h2><a href="details.php?id='.$id.'">',$name ,'</a><img src="images/lockFlat.png" style="width:20px; height: 20px;padding-left:10px;" /></h2>';
            }
            else{
                echo '<h2><a href="details.php?id='.$id.'">',$name ,'</a></h2>';
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
            echo '<font size="1"><i>'.$language.'</i> - '.$date.'</font><br>';
            echo '<img src="images/info.png" style="vertical-align: middle;"/>';
            echo '&nbsp;&nbsp;'.nl2br($description);
            echo '<section class="'.$languageClass.'"><pre><code>'.$code.'</code></pre></section>' ;
        }

    }

    if(isset($_POST["action"]) && $_POST['action']=="getname" ){
        $language = SQLite3::escapeString($_POST["language"]);
        // get entry
        if(isset($_SESSION['valid']) && $_SESSION['valid']){
            $query_language = "SELECT * FROM $mytable where language = '$language' ";
        }
        else {
            $query_language = "SELECT * FROM $mytable where language = '$language' AND private != 'on' ";
        }
        $results_language = $base->query($query_language);

        echo '
            Choose a snippet<br><br>
            <center>
            <select name="selectCode" id="selectCode" class="box" ">
            </center>
            <option selected >Choose a snippet </option>';
        while($row = $results_language->fetchArray())
        {
            $name = iconv("ISO-8859-15", "UTF-8",$row['name']);
            $id = $row['ID'];
            echo '<option value="'.$id.'">'.$name.'</option>';
        }
        echo '</select>';
    }

    // Search snippet
    if( isset($_POST["search"]) ){
        include 'includes/menu.php';
        $mytable ="snippets";
        $base=new SQLite3($config["dbname"]);
        $search = SQLite3::escapeString($_POST['search']);

        if(isset($_SESSION['valid']) && $_SESSION['valid']){
            $query_name = "SELECT * FROM $mytable WHERE name LIKE '%$search%' OR description LIKE '%$search%' OR language LIKE '%$search%'";
        }
        else {
            $query_name = "SELECT * FROM $mytable WHERE (name LIKE '%$search%' OR description LIKE '%$search%' OR language LIKE '%$search%') AND private != 'on' ";
        }
        $results_name = $base->query($query_name);

        echo '<h1> Search results for "'.$_POST['search'].'"</h1>';

        // Loop and write all the recent snippets
        while($row = $results_name->fetchArray())
        {
            $private = $row['private'];
            $name = $row['name'];
            $code = $row['code'];
            $language = $row['language'];
            $date = $row['date'];
            $id = $row['ID'];
            $description = $row['description'];
            if ($private=="on"){
                echo '<h2><a href="details.php?id='.$id.'">',$name ,'</a><img src="images/lockFlat.png" style="width:20px; height: 20px;padding-left:10px;" /></h2>';
            }
            else{
                echo '<h2><a href="details.php?id='.$id.'">',$name ,'</a></h2>';
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

            echo '<font size="1"><i>'.$language.'</i> - '.$date.'</font><br>';
            echo '<img src="images/info.png" style="vertical-align: middle;"/>';
            echo '&nbsp;&nbsp;'.nl2br($description);
            echo '<section class="'.$languageClass.'"> <pre><code>'.$code.'</code></pre> </section>' ;
            echo '<hr style="width:95%; height:5px;background-color:#2ecc71;border-radius:20px;border-color:#2ecc71;border-style:none;">';
        }
        echo '</div></body></html>';
    }
?>
