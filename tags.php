<?php
/*
  @author: Ademcan (ademcan@ademcan.net)
  @name: tags.php
  @description: shows the results for snippet search by tag
 */

    session_start();
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

    include 'includes/menu.php';

    $tag=$_GET["tag"];
    $gettag = $_GET["tag"];

    $base=new SQLite3($config["dbname"]);
    $query_name = "SELECT * FROM snippets WHERE tags LIKE '%".$_GET['tag']."%' ";
    $results_name = $base->query($query_name);


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

    echo '<h1>'.$messages['tagresults'].': '.$tag.'</h1>';

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

        $tags= $row['tags'];
        // $tags = str_replace(' ', '', $tags);
        // $tagsList=explode(",",$tags);

        $tagsList = array_map('trim', explode(',', $tag));

        if (in_array($tag, $tagsList)) {

            echo '<br><div style="margin-bottom:5px;display:inline-flex;"><h2><a href="details.php?id='.$id.'">',$name ,'</a> ['.$language.'] </h2><button style="background-color:transparent; border-color:transparent;" class="btn" id="btn" data-clipboard-text="'.$code.'"><img src="images/copy.png" style="height:25px;" alt="Copy to clipboard"></button></div>';

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
                    echo $messages['notag'].'&nbsp;';
                }
                else{
                    echo '<a href="tags.php?tag='.rawurlencode($lowtag).'" style="color:black;text-decoration:underline">'.$lowtag.'</a>&nbsp;';
                }
            }
            echo "</div><br>";

            echo '<img src="images/comment.png" style="vertical-align: middle;"/>';
            echo '&nbsp;&nbsp;'.nl2br($description);
            echo '<section class="'.$languageClass.'"> <pre class="'.(($lines=="on")?"line-numbers":"").'"'.(($highlight!="")?" data-line=\"$highlight\"":"").'><code>'.$code.'</code></pre> </section>';


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
                echo ' ',$messages['rating'],'] </div>';
            }
            else {
                echo ' ',$messages['ratings'],'] </div>';
            }
            echo '<br><hr ><br>';
        }
    }
    echo '</div></body></html>';
?>


<script type="text/javascript">
    var clipboard = new Clipboard('.btn');
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
    }
</script>
