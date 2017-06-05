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
    header("location:index.php");
}




if (($_GET["action"] == "deleteuser")) {
    $dbname = '../snippets.sqlite';
    $base = new SQLite3($dbname);
    // sql command to delete the user
    $username = $_GET["id"];
    $query_name = "DELETE FROM user WHERE username=\"".$username."\" ";
    $results_name = $base->query($query_name);
    header("location:action.php?action=users");
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
        <form method="post">
        <table>
        <tr><td>Title</td><td><input type="text" name="name" style="width:500px;" value="';
        echo $row['name'];
        echo'"/></td></tr>
        <tr><td width="200px">Language</td><td>
        <select id="language" name="language" >
        <option  ';
        if ($row['language'] == 'html')
            echo 'selected'; echo' value="html">html</option>
        <option ';
        if ($row['language'] == 'css')
            echo 'selected'; echo' value="css">css</option>
        <option ';
        if ($row['language'] == 'javascript')
            echo 'selected'; echo' value="javascript">javascript</option>
        <option ';
        if ($row['language'] == 'python')
            echo 'selected'; echo' value="python">python</option>
        <option ';
        if ($row['language'] == 'text')
            echo 'selected'; echo' value="text">text</option>
        <option ';
        if ($row['language'] == 'c')
            echo 'selected'; echo' value="c">c</option>
        <option ';
        if ($row['language'] == 'c++')
            echo 'selected'; echo' value="c++">c++</option>
        <option ';
        if ($row['language'] == 'ruby')
            echo 'selected'; echo' value="ruby">ruby</option>
        <option ';
        if ($row['language'] == 'sql')
            echo 'selected'; echo' value="sql">sql</option>
        <option ';
        if ($row['language'] == 'java')
            echo 'selected'; echo' value="java">java</option>
        <option ';
        if ($row['language'] == 'php')
            echo 'selected'; echo' value="php">php</option>

                </select>
                </td></tr>
                <tr><td>Description</td><td><textarea name="description" style="width:500px;height:100px;">';
        echo $row['description'];
        echo '</textarea></td></tr>

            <tr><td>Code</td><td><textarea name="code" style="width:500px;height:300px;">';
        echo $row['code'];
        echo '</textarea></td></tr>';

        echo '<tr><td>Tags (comma-separated)</td><td><input type="text" name="tags" style="width:500px;" value="';
        echo $row['tags'];
        echo '"/></td></tr>';

        echo '<input hidden="true" type="text" name="old_tags" style="width:500px;" value="';
        echo $row['tags'];
        echo '"/></td></tr>';


        if ($status == "admin"){
            echo '<tr><td>Private</td><td><input type="checkbox" name="private"';
            if ($row['private'] == "on") {
                echo "checked></td></tr>";
            }
        }
        else {
            
        }

        echo'</table>
            <input class="loginButton" type="submit" value="Update snippet"/>
        </form>
    </div> ';
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

        // update the list of tags
        $oldTagsList=explode(",",$old_tags);
        foreach($oldTagsList as $var){
            $lowtag = strtolower($var);
            $query = "UPDATE tags SET count=count-1 WHERE tag=\"".$lowtag."\" ";
            $results = $base->exec($query);
        }

        $tags = $_POST['tags'];
        $tagsList=explode(",",$tags);
        foreach($tagsList as $var){
            $lowtag = strtolower($var);
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


        $private = (isset($_POST['private']) && $_POST['private'] === "on")?"on":"off";
        $lines = (isset($_POST['lines']) && $_POST['lines'] === "on")?"on":"off";
        $id = $_GET['id'];
        $query_update = "UPDATE $mytable set name='$name', code='$code', language='$language', description='$description', private='$private', lines='$lines', highlight='$highlight', tags='$tags'  where ID='$id' ";
        $results = $base->exec($query_update);

        header("location:index.php");
    }
}



// Save new snippet to the database
if (isset($_POST["add"])) {
    // get values
    $language = $_POST['language'];

    // $name = iconv('UTF-8', 'ISO-8859-15', $_POST['name']);
    // $name = str_replace($search, $replace, $name);
    $name= $_POST['name'];

    $description = iconv('UTF-8', 'ISO-8859-15', $_POST['description']);

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
    $date = date("F j, Y - H:i");
    // connect to the database
    $mytable = "snippets";
    $base = new SQLite3("../".$config['dbname']);

    $code = iconv('UTF-8', 'ISO-8859-15', htmlspecialchars($_POST['code'], ENT_QUOTES));
    $tags = $_POST['tags'];
    $username = $_SESSION['username'];
    $query = "INSERT INTO $mytable(username, language, name, description, code, private, lines, highlight, date, tags, score, rate_counter)
                    VALUES ('$username', '$language', '$name', '$description', '$code', '$private', '$lines','$highlight' , '$date', '$tags', 0,0 )";
    $results = $base->exec($query);

    // save the tags to the tags database
    // loop through the comma separated text and save everithing as small letter (?)
    $tagsList=explode(",",$tags);
    foreach($tagsList as $var){
        $lowtag = strtolower($var);
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
    header("location:index.php");
}



// Page for adding new snippet
if (($_GET["action"] == "add")) {
    include 'admin-menu.php';
    ?>

    <h1>Add a new snippet</h1>

    <div id="newSnippet">

        <form id="form" name="newSnippet" method="post" action="action.php" accept-charset="UTF-8">
            <table>

                <tr><td>Title</td><td><input type="text" name="name" style="width:500px;"/></td></tr>
                <tr><td width="200px">Language</td><td>
                        <select id="language" name="language" style="max-width:40%;">
                            <option value="c">c</option>
                            <option value="c++">c++</option>
                            <option value="css">css</option>
                            <option value="html">html</option>
                            <option value="java">java</option>
                            <option value="javascript">javascript</option>
                            <option value="php">php</option>
                            <option value="python">python</option>
                            <option value="ruby">ruby</option>
                            <option value="sql">sql</option>
                            <option value="text">text</option>
                        </select>

                        <select id="version" name="version" style="max-width:40%;">

                    </td></tr>
                <tr><td>Description</td><td><textarea name="description" style="width:500px;height:100px;"></textarea></td></tr>
                <tr><td>Code</td><td><textarea name="code" style="width:500px;height:300px;"></textarea></td></tr>
                <tr><td>Tags (comma-separated)</td><td><input type="text" name="tags" style="width:500px;"/></td></tr>

            </table>
            <input name="add" type="hidden" />
            <input class="loginButton" type="submit" value="Add snippet" class="submit"/>

        </form>
    </div>
    </div>
    </body>

    <script>
    daySelect = document.getElementById('version');
    document.getElementById("language").onchange = function(e) {
        var index = this.selectedIndex;
        var inputText = this.children[index].innerHTML.trim();
        // alert(inputText);
        if (inputText == "python"){
            // add python version
            daySelect.options[daySelect.options.length] = new Option('Text 1', 'Value1');
            daySelect.options[daySelect.options.length] = new Option('Text 2', 'Value2');
        }
    }
    </script>

    </html>
    <?php
}





// Page for users configuration
if (($_GET["action"] == "users")) {

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

        echo '<h1>Registered users</h1><div id="newSnippet">';

        echo '<table>';
        // Loop and write all the recent snippets
        while($row = $results_name->fetchArray())
        {
            $name = $row['username'];
            $status = $row['status'];

            echo '<tr>';
            echo '<td>'.$name.'</td><td>'.$status.'</td><td>';
            echo '<a href="action.php?action=edituser&id=' . $name . '" class="editButton">Edit</a>';
            echo '<a href="action.php?action=deleteuser&id=' . $name . '" onclick="return confirm(\'Do you really want to delete this user ?\');" class="deleteButton">Delete</a></td></tr>';

        }
        echo "</table><br><br>";
        echo '<a href="action.php?action=adduser" class="addButton">Add new user</a>';

        // Pagination
        // First page
        if ($snippets_count > $limit & $page == 1) {
            echo '<center><a href= "index.php?page=2"> Older snippets >>> </a></center>';
        }
        // Last page
        if ($page > 1 & $snippets_count <= ($limit * $page) & $snippets_count > ($limit * ($page - 1))) {
            echo '<center><a href= "index.php?page=' . ($page - 1) . '"> <<< Newer snippets </a></center>';
        }
        // Middle page
        if ($page > 1 & $snippets_count > ($limit * $page)) {
            echo '<center><a href= "index.php?page=' . ($page - 1) . '"> <<< Newer snippets</a> -- <a href="index.php?page=' . ($page + 1) . '">Older snippets >>></a></center>';
        }
        ?>

        </center><br><br></div></body></html>

    </div>
    </div>
    </body>
    </html>
    <?php
}





// Page for editing user
if (($_GET["action"] == "edituser")) {
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

    <h1>Edit user</h1>

    <div id="newSnippet">

        <form action="action.php" method="post">
            <table>
            <tr><td width="150px">Username :</td><td> <input type="text" name="username" maxlength="30" value=<?php echo $username ?> /></td></tr>
            <tr><td width="150px">Email :</td><td> <input type="text" name="email" maxlength="40" value=<?php echo $email ?> /></td></tr>
            <tr><td> Role </td><td>
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
                    echo '<tr><td>Active :</td><td> <input type="checkbox" name="active" checked /></td></tr>';
                }
                else {
                    echo '<tr><td>Active :</td><td> <input type="checkbox" name="active" /></td></tr>';
                }
                ?>
            </table>
            </center>
            <input name="edituser" type="hidden" />
            <input type="submit" value="Edit user" class="installButton"/>
        </form>
        </div>
        </div>
        </body>
        </html>

        <?php


}

// Edit user details
if (isset($_POST["edituser"])) {

    $dbname = '../snippets.sqlite';
    $base = new SQLite3($dbname);
    $id=$_POST["username"];
    $isadmin=$_POST["status"];
    $email=$_POST["email"];
    if ($_POST['active'] == "on"){
        $query_update = "UPDATE user set active=1, status=\"".$isadmin."\", email=\"".$email."\" where username=\"".$id."\" ";
        $results = $base->exec($query_update);
        header("location:action.php?action=users");
    }
    else {
        $query_update = "UPDATE user set active=0, status=\"".$isadmin."\", email=\"".$email."\"  where username=\"".$id."\" ";
        $results = $base->exec($query_update);
        header("location:action.php?action=users");
    }

}




// Page for adding new snippet
if (($_GET["action"] == "adduser")) {
    include 'admin-menu.php';
    ?>

    <h1>Add a new user</h1>

    <div id="newSnippet">

        <form action="action.php" method="post">
            <table>
            <tr><td width="150px">Username :</td><td> <input type="text" name="username" maxlength="30" /></td></tr>
            <tr><td width="150px">Email :</td><td> <input type="text" name="email" maxlength="40" /></td></tr>
            <tr><td> Role </td><td>
            <select id="status" name="status">
                <option value="admin">admin</option>
                <option value="noadmin">no-admin</option>
            </select>
            </td></tr>
            <tr><td>Password :</td><td> <input type="text" name="pass1" /></td></tr>
            </table>
            </center>
            <input name="adduser" type="hidden" />
            <input type="submit" value="Add user" class="installButton"/>
        </form>


    </div>
    </div>
    </body>
    </html>
    <?php
}






// Save new user to the database
if (isset($_POST["adduser"])) {

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

    $salt = createSalt();
    $hash = hash('sha256', $salt . $hash);


    // Add the user to the database
    $addUser = "INSERT INTO user(username, status, password, salt, active, email)
        VALUES ('$username', '$status' , '$hash' ,'$salt', 1, '$email')";
    $base->exec($addUser);

    // returns to the main admin page
    header("location:action.php?action=users");
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



    echo "<h1>Preferences</h1>";
    echo "<div id='newSnippet'>";

    // show css settings only to admins
    if ($status == "admin"){
    ?>

    <h2>Change prism theme</h2>
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
            <input class="loginButton" type="submit" value="Change CSS" class="submit" />
        </form>
        <br>
    <hr>



    <?php
    }
    // get user pasword information
    $query_pwd = "SELECT * FROM user ";
    $results_pwd = $base->query($query_pwd);
    $row_pwd = $results_pwd->fetchArray()
    ?>
        <br>
        <h2>Change password</h2>

        <form id="form-pwd" name="settings2" method="post" >
            <br>
            <table>
                <tr><td width="200px">
                        New password</td><td> <input class="login" type="password" name="password1" ></td></tr>
                <tr><td>Re-enter new password</td><td> <input class="login" type="password" name="password2" ></td></tr>
            </table>
            <input name="username" type="hidden" value=" <?php echo $row_pwd['username']; ?> "/>
            <input class="loginButton" type="submit" value="Change password" class="submit"/>
        </form>
    </div>
    </body></html>

    <?php
    // Update the new settings
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        // if (isset($_POST['theme'])) {
        //     $theme = $_POST['theme'];
        //     $queryUpdateSettings = "UPDATE settings set theme= '$theme' ";
        //     $base->exec($queryUpdateSettings);
        //     echo "<script>location.href='action.php?action=preferences';</script>";
        // }

        if (isset($_POST["prism_theme"])) {
            // $base = new SQLite3("../".$config['dbname']);
            $query = "UPDATE settings set prismtheme='".$_POST['prism_theme']."' ";
            $base->exec($query);
            // returns to the main admin page
            header("location:index.php");
        }

        if (isset($_POST['password1']) && isset($_POST['password2']) && $_POST['password1'] == $_POST['password2']) {
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
            echo "<script>alert('Password successfuly changed!'); location.href='action.php?action=preferences';</script>";
        } else {
            ?>
            <script>
                alert("Hmm, something is wrong ! Could you try again ?");
            </script>
            <?php
        }
    }
}

ob_flush();

}
?>
