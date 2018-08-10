<?php
/*
  @author: Ademcan (ademcan@ademcan.net)
  @name: action.php
  @description: action class for admin panel
*/


ini_set('display_errors', 'On');
error_reporting(E_ALL);


ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);



require_once('../config.php');
if (!isset($_SESSION))
    session_start();

// we block the acces to non-authenticated people
if (!isset($_SESSION['valid']) || !$_SESSION['valid']) {
    header("location:login.php");
}
else {

ob_start();
setlocale(LC_CTYPE, 'fr_FR.UTF-8');

// $dbname = '../snippets.sqlite';
// $base = new SQLite3($dbname);
// $username = $_SESSION['username'];
// $queryU = "SELECT * FROM user WHERE username=\"".$username."\" ";
// $resultsU = $base->query($queryU);
// $rowU = $resultsU->fetchArray();
// $status = $rowU["status"];


$search  = array('&'    , '"'     , "'"    , '<'   , '>'    );
$replace = array('&amp;', '&quot;', '&#39;', '&lt;', '&gt;' );
if(!isset($_GET["action"]))
	$_GET["action"] = "";

// Delete a snippet based on its identifier
if (($_GET["action"] == "delete")) {
    // connection to the database
    // "../".$config['dbname'] = '../snippets.sqlite';
    $mytable = "snippets";
    $base = new SQLite3("../".$config['dbname']);
    // sql command to delete the snippet
    $query_name = "DELETE FROM $mytable WHERE ID=" . $_GET["id"] . "";
    $results_name = $base->query($query_name);
    // returns to the main admin page
    header("location:index.php?view=all");
}

// Activate a snippet based on its identifier
if (($_GET["action"] == "activate")) {
    // connection to the database
    $mytable = "snippets";
    $base = new SQLite3("../".$config['dbname']);
    $query_name = "UPDATE $mytable SET private='off' WHERE ID=" . $_GET["id"] . "";
    $results_name = $base->query($query_name);
    // returns to the main admin page
    header("location:index.php?view=all");
}

// Deactivate a snippet based on its identifier
if (($_GET["action"] == "deactivate")) {
    // connection to the database
    $mytable = "snippets";
    $base = new SQLite3("../".$config['dbname']);
    $query_name = "UPDATE $mytable SET private='on' WHERE ID=" . $_GET["id"] . "";
    $results_name = $base->query($query_name);
    // returns to the main admin page
    header("location:index.php?view=all");
}

// Delete a user
if (($_GET["action"] == "deleteuser")) {
    $dbname = '../snippets.sqlite';
    $base = new SQLite3($dbname);
    $username = $_GET["id"];
    // sql command to delete the user
    $queryU = "SELECT * FROM user WHERE username=\"".$username."\" ";
    $resultsU = $base->query($queryU);
    $rowU = $resultsU->fetchArray();
    $status = $rowU["status"];

    // if user is admin, check that it is not the last admin
    if ($status == "admin"){
        // check that it is not the last admin user
        $count_query = "SELECT count(*) as count FROM user WHERE status='admin'";
        $results_count = $base->query($count_query);
        $row_count = $results_count->fetchArray();
        $user_count = $row_count['count'];
        if ($user_count > 1){
            $query_name = "DELETE FROM user WHERE username=\"".$username."\" ";
            $results_name = $base->query($query_name);
            header("location:action.php?action=users");
        }
    }
    // delete directly if user is not admin
    else {
        $query_name = "DELETE FROM user WHERE username=\"".$username."\" ";
        $results_name = $base->query($query_name);
        header("location:action.php?action=users");
    }
}

// Show all snippets
if (($_GET["action"] == "allsnippets")) {
    $dbname = '../snippets.sqlite';
    $mytable = "snippets";
    $base = new SQLite3($dbname);

    // $count_query = "SELECT count(*) as count FROM $mytable";
    // $results_count = $base->query($count_query);
    $view = $_GET['view'];

    if ($view == "all"){
        $count_query = "SELECT count(*) as count FROM $mytable";
        $results_count = $base->query($count_query);
    }
    elseif ($view == "public"){
        $count_query = "SELECT count(*) as count FROM $mytable  WHERE private='off'";
        $results_count = $base->query($count_query);
    }
    else {
        $count_query = "SELECT count(*) as count FROM $mytable  WHERE private='on' ";
        $results_count = $base->query($count_query);
    }


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
    include 'admin-menu.php';

    if ($view == "all"){
        $query_name = "SELECT * FROM $mytable ORDER BY date DESC LIMIT $start_count,$limit";
    }
    elseif ($view == "public"){
        $query_name = "SELECT * FROM $mytable WHERE private='off' ORDER BY date DESC LIMIT $start_count,$limit";
    }
    else {
        $query_name = "SELECT * FROM $mytable WHERE private='on' ORDER BY date DESC LIMIT $start_count,$limit";
    }

    // $query_name = "SELECT * FROM $mytable ORDER BY date DESC LIMIT $start_count,$limit";


    $title = $messages['allsnippets']." (".$snippets_count.")";

    $results_name = $base->query($query_name);

    echo '<h1>'.$title.'</h1>';
    echo '<div><b>'.$messages['show'].'</b>  <a href="index.php?view=all" class="editButton" style="margin-right:10px !important; ">'.$messages['all'].'</a>';
    echo '<a href="index.php?view=public" class="greenButton" style="margin-right:10px !important; ">'.$messages['published'].'</a>';
    echo '<a href="index.php?view=private" class="deleteButton">'.$messages['unpublished'].'</a></div><div id="newSnippet">';


    if ($snippets_count == 0){
        echo "".$messages['nosnippetyet']."";
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
            echo '<div style="background-color:#BDEDFF;margin-top:20px;"><h2><font color="#27ae60">'.$name.' ['.$language.']</font><a href="action.php?action=activate&id=' . $row['ID'] . '" onclick="return confirm(\'Do you really want to activate this snippet ?\');"><img src="../images/lockFlat.png" style="width:20px; height: 20px;padding-left:10px;" /></a></h2>';
        }
        else {
            echo '<div style="margin-top:20px;"><h2><font color="#27ae60">'.$name.' ['.$language.']</font><a href="action.php?action=deactivate&id=' . $row['ID'] . '" onclick="return confirm(\'Do you really want to deactivate this snippet ?\');"><img src="../images/lockFlat_open.png" style="width:20px; height: 20px;padding-left:10px;" /></a></h2>';
            // echo '<div style="margin-top:20px;"><h2><font color="#27ae60">'.$name.' ['.$language.']</font></h2>';
        }


        // if ($private=="on"){
        //     echo '<div style="background-color:#BDEDFF;margin-top:20px;"><h2><font color="#27ae60">'.$name.' ['.$language.']</font><img src="../images/lockFlat.png" style="width:20px; height:20px;padding-left:10px;" /></h2>';
        // }
        // else{
        //     echo '<div><h2><font color="#27ae60">'.$name.' ['.$language.']</font></h2>';
        // }

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
        echo '<a href="action.php?action=edit&id=' . $row['ID'] . '" class="editButton">'.$messages['edit'].'</a>';
        echo '<a href="action.php?action=delete&id=' . $row['ID'] . '" onclick="return confirm(\'',$messages['reallydeletesnippet'],'\');" class="deleteButton">'.$messages['delete'].'</a>';
        echo '<hr><br></div>';
    }

    echo "<br><br>";

    // Pagination
    // First page
    if ($snippets_count > $limit & $page == 1) {
        echo '<center><a href= "action.php?action=allsnippets&view='.$view.'&page=2"> ',$messages['oldersnippets'],' >>> </a></center>';
    }
    // Last page
    if ($page > 1 & $snippets_count <= ($limit * $page) & $snippets_count > ($limit * ($page - 1))) {
        echo '<center><a href= "action.php?action=allsnippets&view='.$view.'&page=' . ($page - 1) . '"> <<< ',$messages['newestsnippets'],' </a></center>';
    }
    // Middle page
    if ($page > 1 & $snippets_count > ($limit * $page)) {
        echo '<center><a href= "action.php?action=allsnippets&view='.$view.'&page=' . ($page - 1) . '"> <<< ',$messages['newestsnippets'],'</a> -- <a href="action.php?action=allsnippets&page=' . ($page + 1) . '">',$messages['oldersnippets'],'  >>></a></center>';
    }

}

// Edit a snippet based on its identifier
if (($_GET["action"] == "edit")) {
    $mytable = "snippets";
    $base = new SQLite3("../".$config['dbname']);

    $username = $_SESSION['username'];
    $queryU = "SELECT * FROM user WHERE username=\"".$username."\" ";
    $resultsU = $base->query($queryU);
    $rowU = $resultsU->fetchArray();
    $status = $rowU["status"];

    $query_name = "SELECT * FROM $mytable WHERE ID=" . $_GET["id"] . " ";
    $results_name = $base->query($query_name);
    // Edit page
    include 'admin-menu.php';
    echo'<h1>Edit snippet</h1><div id="newSnippet">';
    while ($row = $results_name->fetchArray()) {
        echo'
        <form id="form" method="post">
        <table>
        <tr><td>',$messages['title'],'</td><td><input type="text" name="name" id="name" style="width:500px;" value="';
        echo $row['name'];
        echo'"/></td></tr>
        <tr><td width="200px">',$messages['language'],'</td><td>
        <select id="language" name="language" >
        <option  ';
        if ($row['language'] == 'HTML')
            echo 'selected'; echo' value="HTML">HTML</option>
        <option ';
        if ($row['language'] == 'CSS')
            echo 'selected'; echo' value="CSS">CSS</option>
        <option ';
        if ($row['language'] == 'JavaScript')
            echo 'selected'; echo' value="JavaScript">JavaScript</option>
        <option ';
        if ($row['language'] == 'Python')
            echo 'selected'; echo' value="Python">Python</option>
        <option ';
        if ($row['language'] == 'Text')
            echo 'selected'; echo' value="Text">Text</option>
        <option ';
        if ($row['language'] == 'R')
            echo 'selected'; echo' value="R">R</option>
        <option ';
        if ($row['language'] == 'Perl')
            echo 'selected'; echo' value="Perl">Perl</option>
        <option ';
        if ($row['language'] == 'Rust')
            echo 'selected'; echo' value="Rust">Rust</option>
        <option ';
        if ($row['language'] == 'Bash')
            echo 'selected'; echo' value="Bash">Bash</option>
        <option ';
        if ($row['language'] == 'C')
            echo 'selected'; echo' value="C">C</option>
        <option ';
        if ($row['language'] == 'C++')
            echo 'selected'; echo' value="C++">C++</option>
        <option ';
        if ($row['language'] == 'Ruby')
            echo 'selected'; echo' value="Ruby">Ruby</option>
        <option ';
        if ($row['language'] == 'SQL')
            echo 'selected'; echo' value="SQL">SQL</option>
        <option ';
        if ($row['language'] == 'Java')
            echo 'selected'; echo' value="Java">Java</option>
        <option ';
        if ($row['language'] == 'objectivec')
            echo 'selected'; echo' value="objectivec">Objective-C</option>
        <option';
        if ($row['language'] == 'MATLAB')
            echo 'selected'; echo' value="MATLAB">MATLAB</option>
        <option ';
        if ($row['language'] == 'PHP')
            echo 'selected'; echo' value="PHP">PHP</option>

                </select>
                </td></tr>
                <tr><td>',$messages['description'],'</td><td><textarea name="description" id="description" style="width:500px;height:100px;">';
        echo $row['description'];
        echo '</textarea></td></tr>

            <tr><td>Code</td><td><textarea id="code" name="code" style="width:500px;height:300px;">';
        echo $row['code'];
        echo '</textarea></td></tr>';

        echo '<tr><td>',$messages['tagscommasep'],'</td><td><input type="text" name="tags" id="tags" style="width:500px;" value="';
        echo $row['tags'];
        echo '"/></td></tr>';

        echo '<input hidden="true" type="text" name="old_tags" style="width:500px;" value="';
        echo $row['tags'];
        echo '"/></td></tr>';
        echo"</table>
            <input class='loginButton' type='submit' value='",$messages['update'],"' />
        </form>
    </div>


    <script>
        $('#code').on('keydown', function(ev) {
            var keyCode = ev.keyCode || ev.which;

            if (keyCode == 9) {
                ev.preventDefault();
                var start = this.selectionStart;
                var end = this.selectionEnd;
                var val = this.value;
                var selected = val.substring(start, end);
                var re, count;

                if(ev.shiftKey) {
                    re = /^\t/gm;
                    count = -selected.match(re).length;
                    this.value = val.substring(0, start) + selected.replace(re, '') + val.substring(end);
                    // todo: add support for shift-tabbing without a selection
                } else {
                    re = /^/gm;
                    count = selected.match(re).length;
                    this.value = val.substring(0, start) + selected.replace(re, '\t') + val.substring(end);
                }

                if(start === end) {
                    this.selectionStart = end + count;
                } else {
                    this.selectionStart = start;
                }

                this.selectionEnd = end + count;
            }
        });

        $(function() {
            $('#form').on('submit', function() {
                allowed_languages  = ['C','C++','CSS','HTML','Java','JavaScript','PHP','Python','Ruby','SQL','R','Bash','Perl','Rust','Text','MATLAB','objectivec'];
                if( !$('#name').val() ) {
                    alert('",$messages['providetitle'],"');
                    return false;
                }
                if( !$('#description').val() ) {
                    alert('",$messages['providedescription'],"');
                    return false;
                }
                if( !$('#code').val() ) {
                    alert('",$messages['providecode'],"');
                    return false;
                }
                if( !$('#tags').val() ) {
                    alert('",$messages['providetag'],"');
                    return false;
                }
                if ( allowed_languages.indexOf( $('#language').val() ) < 0 ){
                    alert('",$messages['choosecorrectlng'],"');
                    return false;
                }
            });
        });
    </script>";
    }

    // Saving edited information to the database
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$name = $_POST['name'];
        $highlight = $_POST['highlight'];
        $language = $_POST['language'];
		$code = $_POST['code'];
        $name = str_replace($search, $replace, $name);
        $description = str_replace($search, $replace, $_POST['description']);
        $code = str_replace($search, $replace, $code);
        $old_tags = $_POST['old_tags'];

        // $old_tags = str_replace($search, $replace, $old_tags);

        // update the list of tags
        $oldTagsList=explode(",",$old_tags);
        foreach($oldTagsList as $var){
            // $lowtag = strtolower($var);
            // $lowtag = str_replace(' ', '', $var);
            $lowtag = $var;

            $query = "UPDATE tags SET count=count-1 WHERE tag=\"".$lowtag."\" ";
            $results = $base->exec($query);
        }

        $tags = $_POST['tags'];
        $tags = str_replace($search, $replace, $tags);
        $tagsList=explode(",",$tags);
        foreach($tagsList as $var){
            // $lowtag = strtolower($var);
            // $lowtag = str_replace(' ', '', $var);
            $lowtag = trim($var);
            $count_query = "SELECT count(*) as count FROM tags WHERE tag=\"".$lowtag."\" ";
            $results_count = $base->query($count_query);
            $row_count = $results_count->fetchArray();
            $tags_count = $row_count['count'];

            if ($tags_count == 0){
                $query = "INSERT INTO tags(tag, count) VALUES ('$lowtag', 1)";
            }
            else{
                $query = "UPDATE tags SET count=count+1 WHERE tag=\"".$lowtag."\" ";
            }
            $results = $base->exec($query);
        }


        // $private = (isset($_POST['private']) && $_POST['private'] === "on")?"on":"off";
        $lines = (isset($_POST['lines']) && $_POST['lines'] === "on")?"on":"off";
        $id = $_GET['id'];
        $query_update = "UPDATE $mytable set name='$name', code='$code', language='$language', description='$description', lines='$lines', highlight='$highlight', tags='$tags'  where ID='$id' ";
        $results = $base->exec($query_update);

        header("location:index.php?view=all");
    }
}






// Save new snippet to the database
if (isset($_POST["add"])) {
    // get values

    $language = $_POST['language'];

    // $name = iconv('UTF-8', 'ISO-8859-15', $_POST['name']);
    // $name = str_replace($search, $replace, $name);
    $name= $_POST['name'];

    // $description = iconv('UTF-8', 'ISO-8859-15', $_POST['description']);
    $description = $_POST['description'];

    $description = str_replace($search, $replace, $description);

    $code = $_POST['code'];
    // Force code to be private at first save, needs to be validated by one admin user
    // $private = $_POST['private'];
    // $private = (isset($_POST['private']) && $_POST['private'] = "on")?"on":"off";
    $private = "on";
    // $lines = (isset($_POST['lines']) && $_POST['lines'] == "on")?"on":"off";
    // Force adding the line numbers in all the snippets
    $lines = "on";
    $highlight = $_POST['highlight'];
    // $date = date("F j, Y - H:i");
    $date = date("Y-m-d");
    //$date = date("Y-m-d-h-i-s");
    $dateDb = date("Y-m-d h:i:s");
    // connect to the database
    $mytable = "snippets";
    $base = new SQLite3("../".$config['dbname']);

    // $code = iconv('UTF-8', 'ISO-8859-15', htmlspecialchars($_POST['code'], ENT_QUOTES));
    $tags = $_POST['tags'];
    $username = $_SESSION['username'];


    $name = str_replace($search, $replace, $name);
    $description = str_replace($search, $replace, $_POST['description']);
    $code = str_replace($search, $replace, $code);
    $tags = str_replace($search, $replace, $tags);

    $query = "INSERT INTO $mytable(username, language, name, description, code, private, lines, highlight, date, tags, score, rate_counter)
                    VALUES ('$username', '$language', '$name', '$description', '$code', '$private', '$lines','$highlight' , '$dateDb', '$tags', 0,0 )";
    $results = $base->exec($query);

    // save the tags to the tags database
    // loop through the comma separated text and save everithing as small letter (?)
    $tagsList=explode(",",$tags);
    foreach($tagsList as $var){
        // $lowtag = strtolower($var);
        // $lowtag = str_replace(' ', '', $var);

        $lowtag=trim($var);


        $count_query = "SELECT count(*) as count FROM tags WHERE tag=\"".$lowtag."\" ";
        $results_count = $base->query($count_query);
        $row_count = $results_count->fetchArray();
        $tags_count = $row_count['count'];

        if ($tags_count == 0){
            $query2 = "INSERT INTO tags(tag, count) VALUES ('$lowtag', 1)";
        }
        else{
            $query2 = "UPDATE tags SET count=count+1 WHERE tag=\"".$lowtag."\" ";
        }
        $results = $base->exec($query2);
    }

    // returns to the main admin page

    $_SESSION['newSnippet'] = 1;
    header("location:index.php?view=all");
}



// Page for adding new snippet
if (($_GET["action"] == "add")) {
    include 'admin-menu.php';
    ?>

    <h1>Ajouter un snippet</h1>

    <div id="newSnippet">

        <form id="form" name="newSnippet" method="post" action="action.php" accept-charset="UTF-8">
            <table>

                <tr><td><?php echo $messages['title']; ?></td><td><input type="text" name="name" id="name" style="width:500px;"/></td></tr>
                <tr><td width="200px"><?php echo $messages['language']; ?></td><td>
                        <select id="language" name="language" style="max-width:40%;">
                            <option value="Bash">Bash</option>
                            <option value="C">C</option>
                            <option value="C++">C++</option>
                            <option value="CSS">CSS</option>
                            <option value="HTML">HTML</option>
                            <option value="Java">Java</option>
                            <option value="JavaScript">JavaScript</option>
                            <option value="MATLAB">MATLAB</option>
                            <option value="objectivec">Objective-C</option>
                            <option value="Perl">Perl</option>
                            <option value="PHP">PHP</option>
                            <option value="Python">Python</option>
                            <option value="Ruby">Ruby</option>
                            <option value="SQL">SQL</option>
                            <option value="R">R</option>
                            <option value="Rust">Rust</option>
                            <option value="Text">Text</option>
                        </select>
                    </td></tr>
                <tr><td><?php echo $messages['description']; ?></td><td><textarea name="description" id="description" style="width:500px;height:100px;"></textarea></td></tr>
                <tr><td>Code</td><td><textarea name="code" id="code" style="width:500px;height:300px;"></textarea></td></tr>
                <tr><td><?php echo $messages['tagscommasep']; ?></td><td><input type="text" id="tags" name="tags" style="width:500px;"/></td></tr>

            </table>
            <input name="add" type="hidden" />
            <input class="loginButton" type="submit" value="<?php echo $messages['addsnippet']; ?>" class="submit"/>

        </form>
    </div>
    </div>
    </body>

    <script>
        $('#code').on('keydown', function(ev) {
            var keyCode = ev.keyCode || ev.which;

            if (keyCode == 9) {
                ev.preventDefault();
                var start = this.selectionStart;
                var end = this.selectionEnd;
                var val = this.value;
                var selected = val.substring(start, end);
                var re, count;

                if(ev.shiftKey) {
                    re = /^\t/gm;
                    count = -selected.match(re).length;
                    this.value = val.substring(0, start) + selected.replace(re, '') + val.substring(end);
                    // todo: add support for shift-tabbing without a selection
                } else {
                    re = /^/gm;
                    count = selected.match(re).length;
                    this.value = val.substring(0, start) + selected.replace(re, '\t') + val.substring(end);
                }

                if(start === end) {
                    this.selectionStart = end + count;
                } else {
                    this.selectionStart = start;
                }

                this.selectionEnd = end + count;
            }
        });

        $(function() {

            $('#form').on('submit', function() {
                allowed_languages  = ['C','C++','CSS','HTML','Java','JavaScript','PHP','Python','Ruby','SQL','R','Bash','Perl','Rust','Text','MATLAB','objectivec'];

                if( !$("#name").val() | $("#name").val().trim().length == 0 ) {
                    alert("<?php echo($messages['providetitle']); ?>")
                    return false;
                }
                if( !$("#description").val() | $("#description").val().trim().length == 0) {
                    alert("<?php echo($messages['providedescription']); ?>")
                    return false;
                }
                if( !$("#code").val() | $("#code").val().trim().length == 0 ) {
                    alert("<?php echo($messages['providecode']); ?>")
                    return false;
                }
                if( !$("#tags").val() | $("#tags").val().trim().length == 0 ) {
                    alert("<?php echo($messages['providetag']); ?>")
                    return false;
                }
                if ( allowed_languages.indexOf( $("#language").val() ) < 0 ){
                    alert("<?php echo($messages['choosecorrectlng']); ?>")
                    return false;
                }
                if (/[~`!@#$%\^&*+=\-\[\]\\';/{}|\\":.<>\?]/g.test($("#tags").val()) ){
                    alert("<?php echo($messages['nospecialchartag']); ?>")
                    return false;
                }
            });
        });

    </script>
    </html>
    <?php
}


// Page for users configuration
if (($_GET["action"] == "users")) {

    if($_SESSION['admin']=="admin"){
        include 'admin-menu.php';
        $dbname = '../snippets.sqlite';
        $mytable = "user";
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

        $query_name = "SELECT * FROM $mytable ";
        $results_name = $base->query($query_name);


        echo '<h1>'.$messages['users'].'</h1><div id="newSnippet">';

        echo '<table>';
        // Loop and write all the recent snippets
        while($row = $results_name->fetchArray())
        {
            $name = $row['username'];
            $status = $row['status'];

            echo '<tr>';
            echo '<td>'.$name.'</td><td>'.$status.'</td><td>';
            echo '<a href="action.php?action=edituser&id=' . $name . '" class="editButton">'.$messages['edit'].'</a>';
            echo '<a href="action.php?action=deleteuser&id=' . $name . '" onclick="return confirm(\'',$messages['reallydeleteuser'],'\');" class="deleteButton">'.$messages['delete'].'</a></td></tr>';

        }
        echo "</table><br><br>";
        echo '<a href="action.php?action=adduser" class="addButton" style="width:150px !important;">'.$messages['adduser'].'</a>';

        // Implement pagination for users list
        // Implement lock to activate/deactivate user with one click
        ?>

        </center><br><br></div></body></html>

        </div>
        </div>
        </body>
        </html>
        <?php
    }
    else{
        header("location:index.php?view=all");
    }
}

// Page for editing user
if (($_GET["action"] == "edituser")) {

    if($_SESSION['admin']=="admin"){
        include 'admin-menu.php';

        $dbname = '../snippets.sqlite';
        $mytable = "user";
        $base = new SQLite3($dbname);
        $id=$_GET["id"];
        $user_query = "SELECT * FROM $mytable WHERE username=\"".$id."\" ";

        $resultsU = $base->query($user_query);
        $rowU = $resultsU->fetchArray();
        $status = $rowU["status"];
        $username = $rowU["username"];
        $active = $rowU["active"];
        $email = $rowU["email"];
        ?>

        <h1><?php echo($messages['edituser']); ?></h1>

        <div id="newSnippet">

            <form action="action.php" method="post">
                <table>
                <tr><td width="150px"><?php echo($messages['username']); ?> </td><td> <input type="text" name="username" maxlength="30" value=<?php echo $username ?> /></td></tr>
                <tr><td width="150px">Email </td><td> <input type="text" name="email" maxlength="40" value=<?php echo $email ?> /></td></tr>
                <tr><td> <?php echo($messages['status']); ?> </td><td>
                <select id="status" name="status">
                    <?php
                    if($status == "admin"){
                        echo '<option name="isadmin" value="admin" selected>admin</option>';
                        echo '<option name="isadmin" value="noadmin">no-admin</option>';
                    }
                    else{
                        echo '<option name="isadmin" value="admin">admin</option>';
                        echo '<option name="isadmin" value="noadmin" selected>no-admin</option>';
                    }
                    ?>
                </select>
                </td></tr>
                    <?php
                    if ($active == 1){
                        echo '<tr><td>'.$messages['actif'].' </td><td> <input type="checkbox" name="active" checked /></td></tr>';
                    }
                    else {
                        echo '<tr><td>'.$messages['actif'].' </td><td> <input type="checkbox" name="active" /></td></tr>';
                    }
                    ?>
                </table>
                </center>
                <input name="edituser" type="hidden" />
                <input type="submit" value="<?php echo($messages['update']); ?>" class="installButton"/>
            </form>
            </div>
            </div>
            </body>
            </html>

            <?php

    }
    else{
        header("location:index.php?view=all");
    }
}

// Edit user details
if (isset($_POST["edituser"])) {
    if($_SESSION['admin']=="admin"){
        $dbname = '../snippets.sqlite';
        $base = new SQLite3($dbname);
        $id=$_POST["username"];
        $isadmin=$_POST["status"];
        $email=$_POST["email"];

        if ($_POST['active'] == "on"){
            $query_update = "UPDATE user set active=1, status=\"".$isadmin."\", email=\"".$email."\" where username=\"".$id."\" ";
            $results = $base->exec($query_update);
            header("location:action.php?action=users");

            // send email to inform the user that accounts was activated
            // Send email to user
            $from = "canSnippet"; // this is the sender's Email address
            $subject = "Votre compte canSnippet est actif";
            $message = ''.$messages['bonjour'].''.$id.''.$messages['useractivatedemail'].'';

            $header_array = [
                "MIME-Version: 1.0",
                "Content-type: text/plain; charset=UTF-8",
                "From: ".$from."",
            ];
            $headers = implode("\r\n", $header_array);

            mail($email,$subject,$message,$headers);
        }
        else {
            $query_update = "UPDATE user set active=0, status=\"".$isadmin."\", email=\"".$email."\"  where username=\"".$id."\" ";
            $results = $base->exec($query_update);
            header("location:action.php?action=users");
        }
    }
    else{
        header("location:index.php?view=all");
    }
}

// Page for adding new user
if (($_GET["action"] == "adduser")) {
    if($_SESSION['admin']=="admin"){
        include 'admin-menu.php';
        ?>
        <h1><?php echo $messages['adduser']; ?></h1>
        <div id="newSnippet">

            <form action="action.php" method="post">
                <table>
                <tr><td width="150px"> <?php echo($messages['username']); ?></td><td> <input type="text" name="username" maxlength="30" /></td></tr>
                <tr><td width="150px">Email </td><td> <input type="text" name="email" maxlength="40" /></td></tr>
                <tr><td> Role </td><td>
                <select id="status" name="status">
                    <option value="admin">admin</option>
                    <option value="noadmin">no-admin</option>
                </select>
                </td></tr>
                <tr><td><?php echo($messages['password']); ?> </td><td> <input type="password" name="pass1" /></td></tr>
                </table>
                </center>
                <input name="adduser" type="hidden" />
                <input type="submit" value="<?php echo($messages['add']); ?>" class="installButton"/>
            </form>


        </div>
        </div>
        </body>
        </html>
        <?php

    }
    else{
        header("location:index.php?view=all");
    }
}

// Save new user to the database
if (isset($_POST["adduser"])) {

    if($_SESSION['admin']=="admin"){

        $dbname = '../snippets.sqlite';
        $base = new SQLite3($dbname);

        // get values
        $username = $_POST['username'];
        $pwd = $_POST['pass1'];
        $status = $_POST['status'];
        $email = $_POST['email'];

        $hash = hash('sha256', $pwd);
        //creates a 3 character sequence for salt
        function createSalt() {
            $string = md5(uniqid(rand(), true));
            return substr($string, 0, 3);
        }
        // generate salt and hash for pwd
        $salt = createSalt();
        $hash = hash('sha256', $salt . $hash);
        // Add the user to the database
        $addUser = "INSERT INTO user(username, status, password, salt, active, email)
            VALUES ('$username', '$status' , '$hash' ,'$salt', 1, '$email')";
        $base->exec($addUser);
        // returns to the main admin page
        header("location:action.php?action=users");
    }
    else{
        header("location:index.php?view=all");
    }
}

// Preferences page with general options
if (($_GET["action"] == "preferences")) {

    include 'admin-menu.php';
    $query_name = "SELECT * FROM settings ";
    $results_name = $base->query($query_name);
    $row = $results_name->fetchArray();

    $username = $_SESSION['username'];
    $queryU = "SELECT * FROM user WHERE username=\"".$username."\" ";
    $resultsU = $base->query($queryU);
    $rowU = $resultsU->fetchArray();
    $status = $rowU["status"];
    echo "<h1>".$messages['preferences']."</h1>";
    echo "<div id='newSnippet'>";

    // show css settings only to admins
    if ($status == "admin"){
    ?>

    <h2><?php echo($messages['changetheme']); ?></h2>
    <br>
        <form id="form" name="prism_theme" method="post" >
            <select name="prism_theme">
                <option value="<?=$config["defaultPrismCSS"]?>">Default</option>
                <?php
                $files = scandir("../".dirname($config["defaultPrismCSS"]).'/prism_theme/');
                for ($i=0;$i<count($files);$i++)
                    if(preg_match("/.+\.css/",$files[$i]))
                        echo '<option value="'.dirname($config["defaultPrismCSS"]).'/prism_theme/'.$files[$i].'">'.$files[$i].'</option>';
                ?>
            </select>
            <br>
            <input class="loginButton" type="submit" value="<?php echo($messages['chargecss']); ?>" class="submit" />
        </form>
        <br>
    <hr>
    <h2><?php echo($messages['changelng']); ?></h2>

    <form id="form-pwd" name="settings2" method="post" >
        <br />
        <select name="language">
            <option value="fr"><?php echo($messages['french']); ?></option>
            <option value="en"><?php echo($messages['english']); ?></option>
        </select>
        <br />
        <input class="loginButton" type="submit" value="<?php echo($messages['changelngbutton']); ?>" class="submit" />
    </form>
    <br>
    <hr />



    <?php
    }
    // get user pasword information
    $query_pwd = "SELECT * FROM user ";
    $results_pwd = $base->query($query_pwd);
    $row_pwd = $results_pwd->fetchArray()
    ?>
        <br>
        <h2><?php echo($messages['changepwd']); ?></h2>

        <form id="form-pwd" name="settings2" method="post" >
            <br>
            <table>
                <tr><td width="200px">
                        <?php echo($messages['newpwd']); ?></td><td> <input class="login" type="password" name="password1" ></td></tr>
                        <tr><td><?php echo($messages['repeatpassword']); ?></td><td> <input class="login" type="password" name="password2" ></td></tr>
            </table>
            <input name="username" type="hidden" value=" <?php echo $row_pwd['username']; ?> "/>
            <input class="loginButton" type="submit" value="<?php echo($messages['changepwdbutton']); ?>" class="submit"/>
        </form>

        <br />
    </div>
    </body></html>

    <?php
    // Update the new settings
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        if (isset($_POST["language"])) {
            // $base = new SQLite3("../".$config['dbname']);
            $query = "UPDATE settings set lng='".$_POST['language']."' ";
            $base->exec($query);
            // returns to the main admin page
            header("location:index.php?view=all");
        }

        if (isset($_POST["prism_theme"])) {
            // $base = new SQLite3("../".$config['dbname']);
            $query = "UPDATE settings set prismtheme='".$_POST['prism_theme']."' ";
            $base->exec($query);
            // returns to the main admin page
            header("location:index.php?view=all");
        }
        $pass1 = $_POST['password1'];
        if (preg_match('/[A-Z]/', $pass1) && preg_match('/[0-9]/', $pass1)){
            if ($_POST['password1'] == $_POST['password2']) {
                $password1 = $_POST['password1'];
                $hash = hash('sha256', $password1);

                //creates a 3 character sequence
                function createSalt() {
                    $string = md5(uniqid(rand(), true));
                    return substr($string, 0, 3);
                }

                $salt = createSalt();
                $hash = hash('sha256', $salt . $hash);
                $queryUpdatePwd = "UPDATE user SET password='$hash', salt='$salt' WHERE username='$username' ";
                $base->exec($queryUpdatePwd);
                echo "<script>alert('Nouveau mot de passe sauvegardé!'); location.href='action.php?action=preferences';</script>";
            }
            else {
                ?>
                <script>
                    alert("<?php echo $messages['repeatsamepwd'];  ?>");
                </script>
                <?php
            }
        }
         else {
            ?>
            <script>
                alert("<?php echo $messages['onecapitalonenumber'];  ?>");
            </script>
            <?php
        }
    }
}

ob_flush();

}
?>
