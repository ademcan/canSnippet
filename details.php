

<?php
/*
  @author: Ademcan (ademcan@ademcan.net)
  @name: details.php
  @description: show a single snippet in one page with the related information, helpful for snippet sharing
 */

    session_start();

    include "config.php";
    include 'includes/menu.php';

    $mytable ="snippets";
    $base=new SQLite3($config["dbname"]);

    // get the scoring of the snippet
    $loggedin_username =  $row['username'];
    $query_scoring = "SELECT * FROM scoring WHERE username = '$loggedin_username' ";
    $results_scoring = $base->query($query_scoring);
    $score_array = array();
    // echo $results_scoring;
    while($row2 = $results_scoring->fetchArray()){
        // echo "".$row['snippet']."";
        $score_array[] = $row2['snippet'];
    }


    $query_name = "SELECT * FROM $mytable WHERE ID=".intval($_GET["id"])." ";
    $results_name = $base->query($query_name);
    $id = $_GET["id"];
    $row = $results_name->fetchArray();
    $language = $row['language'];
    $name = $row['name'];
    $code = $row['code'];
    echo '<h1>Détails de snippet</h1>';


    $private = $row['private'];
    $lines = $row['lines'];

    $date = $row['date'];
    $description = $row['description'];
    $highlight = $row['highlight'];

    if ($language=="html"){
        $languageClass = "language-markup";
    }
    else if ($language=="text"){
        $languageClass = "language-markup";
    }
    else{
        $languageClass = "language-".$language;
    }
    $username = $row['username'];

    echo '<div style="padding-top:40px;margin-bottom:5px;display:inline-flex;"><h2> '.$name.'['.$language.'] </h2><button style="background-color:transparent; border-color:transparent;" class="btn" id="btn" data-clipboard-text="'.$code.'"><img src="images/copy.png" style="height:25px;" alt="Copy to clipboard"/></button></div>';
    echo '<div style="word-wrap: break-word;"><img src="images/calendar.png" style="padding-right:5px;"/>'.$date.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/author.png" style="padding-left:10px;padding-right:5px;"/>'.$username.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/tag.png" style="padding-left:10px;padding-right:5px;"/> ';

    $tags = $row['tags'];
    $tagsList=explode(",",$tags);
    foreach($tagsList as $var){
        // $lowtag = strtolower($var);
        $lowtag = str_replace(' ', '', $var);
        if ($lowtag == ""){
            echo 'Aucun tag&nbsp;';
        }
        else{
            echo '<a href="tags.php?tag='.$lowtag.'" style="color:black;text-decoration:underline">'.$lowtag.'</a>&nbsp;';
        }
    }
    echo "</div><br>";

    echo '<img src="images/comment.png" style="vertical-align: middle;"/>';
    echo '&nbsp;&nbsp;'.nl2br($description);
    echo '<section class="'.$languageClass.'"> <pre class="line-numbers "'.(($highlight!="")?" data-line=\"$highlight\"":"").'><code>'.$code.'</code></pre> </section>';

    $score = $row['score'];


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


    // if (isLoggedIn()){
    //     if (!in_array($id, $score_array)){
    //         echo '<div class="rating" id="rating_div">
    //         <input type="radio" id="star5" name="rating" value="5" onclick="javascript:rating('.$id.') "/><label for="star5" title="THE snippet">5 stars</label>
    //         <input type="radio" id="star4" name="rating" value="4" onclick="javascript:rating('.$id.')"/><label for="star4" title="Bien cool">4 stars</label>
    //         <input type="radio" id="star3" name="rating" value="3" onclick="javascript:rating('.$id.')"/><label for="star3" title="Pas mal">3 stars</label>
    //         <input type="radio" id="star2" name="rating" value="2" onclick="javascript:rating('.$id.')"/><label for="star2" title="Bof">2 stars</label>
    //         <input type="radio" id="star1" name="rating" value="1" onclick="javascript:rating('.$id.')"/><label for="star1" title="Looser">1 star</label>
    //         </div>';
    //     }
    // }



    echo '<div id="scores'.$id.'"><b> '.$score.'</b>/5 - ['.$rate_counter.' ';

    if ($rate_counter < 2){
        echo ' rating] </div>';
    }
    else {
        echo ' ratings] </div>';
    }
    echo '<br><hr ><br>';


    // echo '<div id="scores"><b> '.$score.'</b>/5 - ['.$rate_counter.' ';
    //
    // if ($rate_counter < 2){
    //     echo ' rating] </div>';
    // }
    // else {
    //     echo ' ratings] </div>';
    // }
    // echo '<hr ><br>';

    // echo '<font size="1"><i>'.$language.'</i> - '.$date.'</font><br>';
    // echo '<img src="images/info.png" style="vertical-align: middle;"/>';
    // echo '&nbsp;&nbsp;'.nl2br($description);
    // if ($lines=="on"){
    //     echo '<section class="'.$languageClass.'"> <pre class="line-numbers"><code>'.$code.'</code></pre> </section>';
    // }
    // else if ($highlight!=""){
    //     echo '<section class="'.$languageClass.'"> <pre data-line='.$highlight.'><code>'.$code.'</code></pre> </section>';
    // }
    // else {
    //     echo '<section class="'.$languageClass.'"> <pre><code>'.$code.'</code></pre> </section>' ;
    // }
    // echo '<hr style="width:95%; height:5px;background-color:#2ecc71;border-radius:20px;border-color:#2ecc71;border-style:none;">';
    echo '</div>';
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

        // only one radio can be logically checked, don't check the rest

    }
</script>

<?php
    echo '</body></html>';
?>
