<?php
/*
  @author: Ademcan (ademcan@ademcan.net)
  @name: action.php
  @description: action class for browse snippets page
*/
    include "config.php";

    session_start();
    $base=new SQLite3($config["dbname"]);


    if(isset($_POST["action"]) && $_POST['action']=="getcode"){
        $id = $_POST["id"];
        // get entry
        $query_language = "SELECT * FROM snippets where id = '$id' and private != 'on' ";
        $results_language = $base->query($query_language);
        while($row = $results_language->fetchArray())
        {
            // $code = iconv("ISO-8859-15", "UTF-8",$row['code']);
            $code = $row['code'];

            $language = $row['language'];
            $username = $row['username'];
            $date = $row['date'];
            // $name = iconv("ISO-8859-15", "UTF-8",$row['name']);
            $name = $row['name'];
            $private = $row['private'];


            // $description = iconv("ISO-8859-15", "UTF-8",$row['description']);
            $description = $row['description'];



            $rate_counter = $row['rate_counter'];
            $score = $row['score'];

            $query_scoring = "SELECT * FROM scoring WHERE username = '$username' ";
            $results_scoring = $base->query($query_scoring);
            $score_array = array();
            // echo $results_scoring;
            while($row2 = $results_scoring->fetchArray()){
                // echo "".$row['snippet']."";
                $score_array[] = $row2['snippet'];
            }

            echo '<div style="margin-bottom:5px;display:inline-flex;"><h2><a href="details.php?id='.$id.'">',$name ,'</a> ['.$language.'] </h2><button style="background-color:transparent; border-color:transparent;" class="btn" id="btn" data-clipboard-text="'.$code.'"><img src="images/copy.png" style="height:25px;" alt="Copy to clipboard"></button></div>';

            if ($language=="html"){
                $languageClass = "language-markup";
            }
            else if ($language=="text"){
                $languageClass = "language-markup";
            }
            else{
                $languageClass = "language-".$language;
            }

            echo '<div style="word-wrap: break-word;"><img src="images/calendar.png" style="padding-right:5px;"/>'.$date.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/author.png" style="padding-left:10px;padding-right:5px;"/>'.$username.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/tag.png" style="padding-left:10px;padding-right:5px;"/> ';

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
                    echo '<a href="tags.php?tag='.$lowtag.'" style="color:black;text-decoration:underline">'.$lowtag.'</a>&nbsp;';
                }
            }
            echo "</div><br>";

            echo '<img src="images/comment.png" style="vertical-align: middle;"/>';
            echo '&nbsp;&nbsp;'.nl2br($description);
            echo '<section class="'.$languageClass.'"> <pre class="line-numbers"'.(($highlight!="")?" data-line=\"$highlight\"":"").'><code>'.$code.'</code></pre> </section>';

            if(isset($_SESSION['valid']) && $_SESSION['valid']){
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
            echo '<br><br />';

        }

    }

    if(isset($_POST["action"]) && $_POST['action']=="getname" ){
        $language = SQLite3::escapeString($_POST["language"]);
        // get entry
        // if(isset($_SESSION['valid']) && $_SESSION['valid']){
        //     $query_language = "SELECT * FROM snippets where language = '$language' ";
        // }
        // else {
        //     $query_language = "SELECT * FROM snippets where language = '$language' AND private != 'on' ";
        // }
        $query_language = "SELECT * FROM snippets where language = '$language' AND private != 'on' ";
        $results_language = $base->query($query_language);

        echo '
            Choisissez un snippet<br><br>
            <center>
            <select name="selectCode" id="selectCode" class="box" >
            </center>
            <option selected >Choisissez un snippet </option>';
        while($row = $results_language->fetchArray())
        {
            // $name = iconv("ISO-8859-15", "UTF-8",$row['name']);
            $name=$row['name'];
            $id = $row['ID'];
            echo '<option value="'.$id.'">'.$name.'</option>';
        }
        echo '</select>';
    }


    // Scoring a snippet
    if(isset($_POST["action"]) && $_POST['action']=="updatescore" ){

        $rating = SQLite3::escapeString($_POST["rating"]);

        $id = $_POST["id"];
        $loggedin_username = $_POST["username"];
        $increment_score = $_POST["rating"];

        $query_language = "SELECT * FROM snippets where id = '$id' ";

        $results_language = $base->query($query_language);
        while($row = $results_language->fetchArray())
        {
            $score = $row['score'];
            if ($score == 0){ $new_score = $increment_score; }
            else {$new_score = ($score + $increment_score) / 2.0;}
            $rate_counter = $row['rate_counter'];
            $new_counter = $rate_counter + 1;
            if ($new_counter < 2){echo "<b>$new_score</b>/5 - [$new_counter rating]";}
            else {echo "<b>$new_score</b>/5 - [$new_counter ratings]";}

            $query = "UPDATE snippets SET score='$new_score', rate_counter='$new_counter'  WHERE ID='$id' ";
            $results = $base->exec($query);

            $update_scoring = "INSERT INTO scoring(username, snippet) VALUES('$loggedin_username', '$id')";
            $results_updating = $base->exec($update_scoring);

        }
    }


    // Search snippet
    if( isset($_POST["search"]) ){
        include 'includes/menu.php';
        $mytable ="snippets";
        $base=new SQLite3($config["dbname"]);
        $search = SQLite3::escapeString($_POST['search']);




        if(isset($_SESSION['valid']) && $_SESSION['valid']){
            $query_name = "SELECT * FROM $mytable WHERE name LIKE '%$search%' OR description LIKE '%$search%' OR language LIKE '%$search%' OR tags LIKE '%$search%'  ";
        }
        else {
            $query_name = "SELECT * FROM $mytable WHERE (name LIKE '%$search%' OR description LIKE '%$search%' OR language LIKE '%$search%' OR tags LIKE '%$search%') AND private != 'on' ";
        }
        $results_name = $base->query($query_name);

        echo '<h1> Search results for "'.$_POST['search'].'"</h1>';

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

            echo '<div style="margin-bottom:5px;"><h2><a href="details.php?id='.$id.'">',$name ,'</a> ['.$language.'] </h2></div>';

            if ($language=="html"){
                $languageClass = "language-markup";
            }
            else if ($language=="text"){
                $languageClass = "language-markup";
            }
            else{
                $languageClass = "language-".$language;
            }

            echo '<div style="word-wrap: break-word;"><img src="images/calendar.png" style="padding-right:5px;"/>'.$date.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/author.png" style="padding-left:10px;padding-right:5px;"/>'.$username.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/tag.png" style="padding-left:10px;padding-right:5px;"/> ';

            $tags = $row['tags'];
            $tagsList=explode(",",$tags);
            foreach($tagsList as $var){
                // $lowtag = strtolower($var);
                $lowtag = str_replace(' ', '', $var);
                echo '<a href="tags.php?tag='.$lowtag.'" style="color:black;text-decoration:underline">'.$lowtag.'</a>&nbsp;';
            }
            echo "</div><br>";

            echo '<img src="images/comment.png" style="vertical-align: middle;"/>';
            echo '&nbsp;&nbsp;'.nl2br($description);
            echo '<section class="'.$languageClass.'"> <pre class="line-numbers "'.(($highlight!="")?" data-line=\"$highlight\"":"").'><code>'.$code.'</code></pre> </section>';

            if (isLoggedIn()){

                $username =  $_SESSION['username'];

                $query_scoring = "SELECT * FROM scoring WHERE username = '$username' ";
                $results_scoring = $base->query($query_scoring);
                $score_array = array();
                // echo $results_scoring;
                while($row2 = $results_scoring->fetchArray()){
                    // echo "".$row['snippet']."";
                    $score_array[] = $row2['snippet'];
                }


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
        echo '</div></body>


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

            }
        </script>



        </html>';
    }

?>
