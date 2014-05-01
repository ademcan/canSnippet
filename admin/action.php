<?php
/*
  @author: Ademcan (ademcan@ademcan.net)
  @name: action.php
  @description: action class for admin panel
*/

if (!isset($_SESSION))
    session_start();

// we block the acces to no authenticated people
if (!isset($_SESSION['valid']) || !$_SESSION['valid']) {
    header("location:login.php");
}
else {
    
ob_start();
setlocale(LC_CTYPE, 'fr_FR.UTF-8');

$search  = array('&'    , '"'     , "'"    , '<'   , '>'    );
$replace = array('&amp;', '&quot;', '&#39;', '&lt;', '&gt;' );
if(!isset($_GET["action"]))
	$_GET["action"] = "";
// Delete a snippet based on its identifier
if (($_GET["action"] == "delete")) {
    // connection to the database
    $dbname = '../snippets.sqlite';
    $mytable = "snippets";
    $base = new SQLite3($dbname);
    // sql command to delete the snippet
    $query_name = "DELETE FROM $mytable WHERE ID=" . $_GET["id"] . "";
    $results_name = $base->query($query_name);
    // returns to the main admin page
    header("location:index.php");
}

// Edit a snippet based on its identifier
if (($_GET["action"] == "edit")) {
    $dbname = '../snippets.sqlite';
    $mytable = "snippets";
    $base = new SQLite3($dbname);
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
        echo '</textarea></td></tr>
            <tr><td>Private</td><td><input type="checkbox" name="private" value="on" ';
        if ($row['private'] == "on") {
            echo "checked";
        } echo'></td></tr>
            </table>
            <input class="loginButton" type="submit" value="Update snippet"/>
        </form>
    </div> ';
    }
    // Saving edited information to the database
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$name = $_POST['name'];
        $language = $_POST['language'];
		$code = $_POST['code'];
        $name = str_replace($search, $replace, $name);
        $description = str_replace($search, $replace, $_POST['description']);
        $code = str_replace($search, $replace, $code);
        
        $private = (isset($_POST['private']) && $_POST['private'] === "on")?"on":"off";
        $id = $_GET['id']; 
        $query_update = "UPDATE $mytable set name='$name', code='$code', language='$language', description='$description', private='$private' where ID='$id' ";
        $results = $base->exec($query_update);
        header("location:index.php");
    }
}

// Save new snippet to the database
if (isset($_POST["add"])) {
    // get values
    $language = $_POST['language'];
    $name = iconv('UTF-8', 'ISO-8859-15', $_POST['name']);
    $description = iconv('UTF-8', 'ISO-8859-15', $_POST['description']);

    $name = str_replace($search, $replace, $name);
    $description = str_replace($search, $replace, $description);

    $code = $_POST['code'];
    $private = (isset($_POST['private']) && $_POST['private'] = "on")?"on":"off";
    $date = date("Y-m-d H:i:s");
    // connect to the database
    $dbname = '../snippets.sqlite';
    $mytable = "snippets";
    $base = new SQLite3($dbname);

    $code = iconv('UTF-8', 'ISO-8859-15', htmlspecialchars($_POST['code'], ENT_QUOTES));
    
    $query = "INSERT INTO $mytable(language, name, description, code, private, date)
                    VALUES ( '$language', '$name', '$description', '$code', '$private', '$date')";
    $results = $base->exec($query);
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
                        <select id="language" name="language">
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

                    </td></tr>
                <tr><td>Description</td><td><textarea name="description" style="width:500px;height:100px;"></textarea></td></tr>
                <tr><td>Code</td><td><textarea name="code" style="width:500px;height:300px;"></textarea></td></tr>
                <tr><td>Private</td><td><input type="checkbox" name="private" value="on"></td></tr>
            </table>
            <input name="add" type="hidden" />
            <input class="loginButton" type="submit" value="Add snippet" class="submit"/>

        </form>
    </div>
    </div>
    </body>
    </html>
    <?php
}

// Preferences page with general options
if (($_GET["action"] == "preferences")) {

    include 'admin-menu.php';
    $query_name = "SELECT * FROM settings ";
    $results_name = $base->query($query_name);
    $row = $results_name->fetchArray();
    ?>

    <h1>Preferences</h1>
    <div id="newSnippet">

    <?php
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

        if (isset($_POST['theme'])) {
            $theme = $_POST['theme'];
            $queryUpdateSettings = "UPDATE settings set theme= '$theme' ";
            $base->exec($queryUpdateSettings);
            echo "<script>location.href='action.php?action=preferences';</script>";
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
            $queryUpdatePwd = "UPDATE user SET password='$hash', salt='$salt' ";
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
