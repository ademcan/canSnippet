<?php
/*
  @author: Ademcan (ademcan@ademcan.net)
  @name: browse.php
  @description: browse page allow users to choose a language and a corresponding snippet
 */
    session_start();
    include "config.php";
    include 'includes/menu.php';
    $mytable ="snippets";
    $base=new SQLite3($config["dbname"]);
?>

<h1>Browse snippets</h1>
        <div id="browseLeftPane">

        <div id="languagePanel">
            Choose a language<br><br>
            
        <select id="selectLanguage" name="selectLanguage" class="box" >
            <?php
                // get list of languages
                if(isset($_SESSION['valid']) && $_SESSION['valid']){
                    $query_language = "SELECT DISTINCT language FROM $mytable";
                }
                else {
                    $query_language = "SELECT DISTINCT language FROM $mytable WHERE private != 'on' ";
                }
                $results_language = $base->query($query_language);
                echo '<option value="default" selected> Choose a language </option>';   
                while($row = $results_language->fetchArray())
                {
                    $language = $row['language'];
                    echo '<option value="'.$language.'" >'.$language;
                }
            ?>
        </select>
        </div>

<!--returns the selected language-->
<script>
//var selectCode = document.getElementById("selectCode");
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
    if(selectedLanguage=="c++")
        selectedLanguage="c%2b%2b";
    xhr.send("language="+selectedLanguage+"&action=getname");
};
</script>  
    <div id="namePanel">
    </div>
</div>

<script>
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



