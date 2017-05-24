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


    $query_name = "SELECT * FROM $mytable WHERE private != 'on' ORDER BY date DESC LIMIT $start_count,$limit";
    $results_name = $base->query($query_name);
    $settingsQuery = "SELECT * FROM settings";
    $settingsInfo = $base->query($settingsQuery);
    $settings = $settingsInfo->fetchArray();
    $title = $settings["title"];

    echo '<h1>{ '.$title.' }</h1>';

    if($snippets_count == 0){
        echo "You don't have any snippet yet, you can add new snippets from the Admin interface.";
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
        if ($private=="on"){
            echo '<h2><a href="details.php?id='.$id.'">',$name ,'</a><img src="images/lockFlat.png" style="width:20px; height: 20px;padding-left:10px;" /></h2>';
        }
        else{
            echo '<div style="margin-bottom:5px;"><h2><a href="details.php?id='.$id.'">',$name ,'</a> ['.$language.'] </h2></div>';
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

        echo '<img src="images/calendar.png" style="padding-right:5px;"/>'.$date.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/author.png" style="padding-left:10px;padding-right:5px;"/>'.$username.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/tag.png" style="padding-left:10px;padding-right:5px;"/> ';

        $tags = $row['tags'];
        $tagsList=explode(",",$tags);
        foreach($tagsList as $var){
            $lowtag = strtolower($var);
            $lowtag = str_replace(' ', '', $lowtag);
            echo '<a href="tags.php?tag='.$lowtag.'" style="color:black;text-decoration:underline">'.$lowtag.'</a>&nbsp;';
        }
        echo "<br>";

        echo '<img src="images/comment.png" style="vertical-align: middle;"/>';
        echo '&nbsp;&nbsp;'.nl2br($description);
        echo '<section class="'.$languageClass.'"> <pre class="'.(($lines=="on")?"line-numbers":"").'"'.(($highlight!="")?" data-line=\"$highlight\"":"").'><code>'.$code.'</code></pre> </section>';

        if (isLoggedIn()){
            if (!in_array($id, $score_array)){
                echo '<div class="rating" id="rating_div">
                <input type="radio" id="star5" name="rating" value="5" onclick="javascript:rating('.$id.') "/><label for="star5" title="Rocks!">5 stars</label>
                <input type="radio" id="star4" name="rating" value="4" onclick="javascript:rating('.$id.')"/><label for="star4" title="Pretty good">4 stars</label>
                <input type="radio" id="star3" name="rating" value="3" onclick="javascript:rating('.$id.')"/><label for="star3" title="Meh">3 stars</label>
                <input type="radio" id="star2" name="rating" value="2" onclick="javascript:rating('.$id.')"/><label for="star2" title="Kinda bad">2 stars</label>
                <input type="radio" id="star1" name="rating" value="1" onclick="javascript:rating('.$id.')"/><label for="star1" title="Sucks big time">1 star</label>
                </div>';
            }
        }
        echo '<div id="scores"><b> '.$score.'</b>/5 - ['.$rate_counter.' ';

        if ($rate_counter < 2){
            echo ' rating] </div>';
        }
        else {
            echo ' ratings] </div>';
        }
        echo '<hr ><br>';
    }

    echo "<br><br>";

    // Pagination
    // First page
    if($snippets_count > $limit & $page == 1){
        echo '<center><a href= "index.php?page=2"> Older snippets >>> </a></center>';
    }
    // Last page
    if($page > 1 & $snippets_count <= ($limit*$page) & $snippets_count > ($limit*($page-1)) ){
        echo '<center><a href= "index.php?page='.($page-1).'"> <<< Newer snippets </a></center>';
    }
    // Middle page
    if($page > 1 & $snippets_count > ($limit*$page)){
        echo '<center><a href= "index.php?page='.($page-1).'"> <<< Newer snippets</a> -- <a href="index.php?page='.($page+1).'">Older snippets >>></a></center>';
    }
    echo '<center><font size="1">Powered by <a href="http://www.ademcan.net/index.php?d=2014/01/16/08/47/25-save-and-share-your-snippets-with-cansnippet">canSnippet</a> v1.0 beta - by ademcan<font></center>';
    echo '<br></div> </body></html>';
    }
?>

<script>

function rating(id){
    // alert(loggedin_username)
    var loggedin_username = "<?php echo $loggedin_username; ?>";
    var radios = document.getElementsByName("rating");

    for (var i = 0, length = radios.length; i < length; i++) {
        if (radios[i].checked) {
            // do whatever you want with the checked radio

            var xhr2 = new XMLHttpRequest();
            xhr2.onreadystatechange = function() {
                if (xhr2.readyState == 4 && (xhr2.status == 200 || xhr2.status == 0)) {
                    document.getElementById("rating_div").innerHTML = "";
                    document.getElementById("scores").innerHTML = "";
                    document.getElementById("scores").innerHTML = xhr2.responseText;
                    Prism.highlightAll();
                }
            };
            xhr2.open("POST", "action.php", true);
            xhr2.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr2.send("rating="+radios[i].value+"&action=updatescore&id="+id+"&username="+loggedin_username+"");

            // only one radio can be logically checked, don't check the rest
            break;
        }
    }
}


</script>
