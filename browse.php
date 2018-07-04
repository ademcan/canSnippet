<?php
/*
  @author: Ademcan (ademcan@ademcan.net)
  @name: browse.php
  @description: browse page allows users to choose a language and a corresponding snippet
 */
    session_start();
    $loggedin_username =  $_SESSION['username'];
    include "config.php";
    include 'includes/menu.php';
    $mytable ="snippets";
    $base=new SQLite3($config["dbname"]);
    // set language
    $lng = language();
    switch ($lng) {
        case "en":
            require("./en.php");
            break;
        case "fr":
            require("./fr.php");
            break;
    }
?>

<h1><?php echo $messages['snippetssearch'];?></h1>
        <div id="browseLeftPane">

        <div id="languagePanel">
            <?php
            echo $messages['chooselanguage'];
             ?>
            <br><br>

        <select id="selectLanguage" name="selectLanguage" class="box" >
            <?php
                $query_language = "SELECT DISTINCT language FROM $mytable WHERE private != 'on' ";
                $results_language = $base->query($query_language);
                echo '<option value="default" selected>',$messages['chooselanguage'],'</option>';
                while($row = $results_language->fetchArray())
                {
                    $language = $row['language'];
                    echo '<option value="'.$language.'" >'.$language;
                }
            ?>
        </select>
        </div>

<script>
// returns the snippets for a selected languages
document.getElementById("selectLanguage").onchange = function(){
    selectedLanguage = document.getElementById("selectLanguage").value ;
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
            document.getElementById("namePanel").innerHTML = xhr.responseText;
        }
    };
    xhr.open("POST", "action.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.send("language="+selectedLanguage+"&action=getname");
};
</script>
    <div id="namePanel">
    </div>
</div>


<script>
// returns the selected snippet code
document.getElementById("namePanel").onchange = function(){
    selectedName = document.getElementById("selectCode").value ;
    var xhr2 = new XMLHttpRequest();
    xhr2.onreadystatechange = function() {
        if (xhr2.readyState == 4 && (xhr2.status == 200 || xhr2.status == 0)) {
            document.getElementById("codePanel").innerHTML = xhr2.responseText;
            Prism.highlightAll();
        }
    };
    xhr2.open("POST", "action.php", true);
    xhr2.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr2.send("id="+selectedName+"&action=getcode");
};
</script>
    <div id="codePanel">
    </div>
</div>
</body></html>


<script type="text/javascript">
    // save to clipboard action
    var clipboard = new Clipboard('.btn');
    // updating the rating of the snippet
    function rating(event){
        var rating= event.getAttribute("score");
        var id= event.getAttribute("snippetId");
        var loggedin_username = "<?php echo $loggedin_username; ?>";
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
