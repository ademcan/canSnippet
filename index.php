<?php
    /*
    @author: Ademcan (ademcan@ademcan.net)
    @name: index.php
    @description: initial page
    */
    include "config.php";
    session_start();

    if (!file_exists("snippets.sqlite")) {
        echo "<script>location.href='install.php';</script>";
    } else {

    include 'includes/menu.php';

    $mytable ="snippets";
    $base=new SQLite3($config["dbname"]);

    $count_query = "SELECT count(*) as count FROM $mytable";
    $results_count = $base->query($count_query);
    $row_count = $results_count->fetchArray();
    $snippets_count = $row_count['count'];

    $limit = 10;
    $page = 1;

    if (isset($_GET['page'])){
        $page = intval($_GET['page']);
    }
    $start_count = $limit * ($page-1);

    // if logged in, the user can see the private snippets
    // if(isset($_SESSION['valid']) && $_SESSION['valid']){
    //     $query_name = "SELECT * FROM $mytable ORDER BY date DESC LIMIT $start_count,$limit";
    // }
    // // if not logged in, select only public snippets
    // else {
    //     $query_name = "SELECT * FROM $mytable WHERE private != 'on' ORDER BY date DESC LIMIT $start_count,$limit";
    // }

    // check the snippets that were already scored by the user
    $loggedin_username =  $_SESSION['username'];
    $query_scoring = "SELECT * FROM scoring WHERE username = '$loggedin_username' ";
    $results_scoring = $base->query($query_scoring);
    $score_array = array();
    // echo $results_scoring;
    while($row = $results_scoring->fetchArray()){
        // echo "".$row['snippet']."";
        $score_array[] = $row['snippet'];
    }


// $date = date("F j, Y - H:i");
// DATE_FORMAT(STR_TO_DATE(date, '%M %e, %Y - %H:%i'),'%M %e, %Y - %H:%i' )

    // cast([date] as datetime)

    $query_name = "SELECT * FROM $mytable WHERE private != 'on' ORDER BY date DESC LIMIT $start_count,$limit";
    $results_name = $base->query($query_name);
    $settingsQuery = "SELECT * FROM settings";
    $settingsInfo = $base->query($settingsQuery);
    $settings = $settingsInfo->fetchArray();
    $title = $settings["title"];

    echo '<h1>{ '.$title.' }</h1>';

    if($snippets_count == 0){
        echo "Il n'y a pas de snippets pour le moment. Vous pouvez ajouter un nouveau snippet depuis votre compte.";
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
        $id = $row['ID'];
        $description = $row['description'];
        $rate_counter = $row['rate_counter'];
        $score = $row['score'];

        echo '<br><div style="margin-bottom:5px;display:inline-flex;"><h2><a href="details.php?id='.$id.'">'.$name.'</a> ['.$language.'] </h2><button style="background-color:transparent; border-color:transparent;" class="btn" id="btn" data-clipboard-text="'.$code.'"><img src="images/copy.png" style="height:25px;" alt="Copy to clipboard"></button></div>';

        if ($language=="html"){
            $languageClass = "language-markup";
        }
        else if ($language=="text"){
            $languageClass = "language-markup";
        }
        else{
            $languageClass = "language-".$language;
        }


        $date=date_create($date);
        $formattedDate = date_format($date,"d.m.Y");
        echo '<div style="word-wrap: break-word;"><img src="images/calendar.png" style="padding-right:5px;"/>'.$formattedDate.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/author.png" style="padding-left:10px;padding-right:5px;"/>'.$username.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/tag.png" style="padding-left:10px;padding-right:5px;"/> ';


        $tags = $row['tags'];
        $tagsList=explode(",",$tags);
        foreach($tagsList as $var){
            // $lowtag = strtolower($var);
            // $lowtag = str_replace(' ', '', $var);

            $lowtag=trim($var);
            if ($lowtag == ""){
                echo 'Aucun tag&nbsp;';
            }
            else{
                echo '<a href="tags.php?tag='.rawurlencode($lowtag).'" style="color:black;text-decoration:underline">'.$lowtag.'</a>&nbsp;';
            }

        }

        echo "</div><br>";

        echo '<img src="images/comment.png" style="vertical-align: middle;"/>';
        echo '&nbsp;&nbsp;'.nl2br($description);



        echo '<section class="'.$languageClass.'"> <pre class="line-numbers "'.(($highlight!="")?" data-line=\"$highlight\"":"").'><code>'.$code.'</code></pre> </section>';




        // if(isset($_SESSION['valid']) && $_SESSION['valid']){
        if (isLoggedIn()){
            if (!in_array($id, $score_array)){
                echo '<span class="rating" id="rating_div'.$id.'">
                    <input type="radio" class="rating-input" id="rating-input-1-5" name="rating-input-1">
                    <label for="rating-input-1-5" class="rating-star" score=5 snippetid="'.$id.'" onclick="rating(this);"></label>

                    <input type="radio" class="rating-input" id="rating-input-1-4" name="rating-input-1">
                    <label for="rating-input-1-4" class="rating-star" score=4 snippetid="'.$id.'" onclick="rating(this);"></label>

                    <input type="radio" class="rating-input" id="rating-input-1-3" name="rating-input-1">
                    <label for="rating-input-1-3" class="rating-star" score=3 snippetid="'.$id.'" onclick="rating(this);"></label>

                    <input type="radio" class="rating-input" id="rating-input-1-2" name="rating-input-1">
                    <label for="rating-input-1-2" class="rating-star" score=2 snippetid="'.$id.'" onclick="rating(this);"></label>

                    <input type="radio" class="rating-input" id="rating-input-1-1" name="rating-input-1">
                    <label for="rating-input-1-1" class="rating-star" score=1 snippetid="'.$id.'" onclick="rating(this);"></label>
                </span>';
            }
        }

        echo '<div id="scores'.$id.'"><b> '.$score.'</b>/5 - ['.$rate_counter.' ';

        if ($rate_counter < 2){
            echo ' rating] </div>';
        }
        else {
            echo ' ratings] </div>';
        }
        echo '<br><hr ><br>';
    }

    echo "<br><br>";

    // Pagination
    // First page
    if($snippets_count > $limit & $page == 1){
        echo '<center><a href= "index.php?page=2"> Ancients snippets >>> </a></center>';
    }
    // Last page
    if($page > 1 & $snippets_count <= ($limit*$page) & $snippets_count > ($limit*($page-1)) ){
        echo '<center><a href= "index.php?page='.($page-1).'"> <<< Snippets récents </a></center>';
    }
    // Middle page
    if($page > 1 & $snippets_count > ($limit*$page)){
        echo '<center><a href= "index.php?page='.($page-1).'"> <<< Snippets récents</a> -- <a href="index.php?page='.($page+1).'">Ancients snippets >>></a></center>';
    }
    echo '<center><font size="1">Powered by <a href="http://www.ademcan.net/index.php?d=2014/01/16/08/47/25-save-and-share-your-snippets-with-cansnippet">canSnippet</a> v1.0 beta - by ademcan<font></center>';
    echo '<br></div> </body></html>';
    }
    echo '';

?>

<script>
    var clipboard = new Clipboard('.btn');
</script>

<script type="text/javascript">

    function rating(event){
        var rating= event.getAttribute("score");
        var id= event.getAttribute("snippetId");
        var loggedin_username = "<?php echo $loggedin_username; ?>";

        // do whatever you want with the checked radio
        var xhr2 = new XMLHttpRequest();
        xhr2.onreadystatechange = function() {
            if (xhr2.readyState == 4 && (xhr2.status == 200 || xhr2.status == 0)) {
                document.getElementById("rating_div"+id.toString()+"").innerHTML = "";
                document.getElementById("scores"+id.toString()+"").innerHTML = "";
                document.getElementById("scores"+id.toString()+"").innerHTML = xhr2.responseText;
                Prism.highlightAll();
            }
        };
        xhr2.open("POST", "action.php", true);
        xhr2.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr2.send("rating="+rating+"&action=updatescore&id="+id+"&username="+loggedin_username+"");

        // only one radio can be logically checked, don't check the rest

    }
</script>
