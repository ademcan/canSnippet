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
    if(isset($_SESSION['valid']) && $_SESSION['valid']){
        $query_name = "SELECT * FROM $mytable ORDER BY date DESC LIMIT $start_count,$limit";
    }
    // if not logged in, select only public snippets
    else {
        $query_name = "SELECT * FROM $mytable WHERE private != 'on' ORDER BY date DESC LIMIT $start_count,$limit";
    }
    $results_name = $base->query($query_name);
    
    $settingsQuery = "SELECT * FROM settings";
    $settingsInfo = $base->query($settingsQuery);
    $settings = $settingsInfo->fetchArray();
    $title = $settings["title"];
    
    echo '<h1>{ '.$title.' }</h1>';
    
    if($snippets_count == 0){
        echo "You don't have any snippet yet, you can add new snippets from the Admin interface.";
    }

	//load conf for prism modification
	$conf_mod = conf_mod_prism_loader("./js");
	$pre_class = "";
	for ($i=0; $i < count($conf_mod); $i++)
		if(isset($conf_mod[$i]->pre))
			$pre_class = " ".$conf_mod[$i]->pre;
    // Loop and write all the recent snippets
    while($row = $results_name->fetchArray())
    {
        $name = $row['name'];
        $code = $row['code'];
        $language = $row['language'];
        $private = $row['private'];
        $date = $row['date'];
        $id = $row['ID'];
        $description = $row['description'];
        if ($private=="on"){
            echo '<h2><a href="details.php?id='.$id.'">',$name ,'</a><img src="images/lockFlat.png" style="width:20px; height: 20px;padding-left:10px;" /></h2>';
        }
        else{
            echo '<h2><a href="details.php?id='.$id.'">',$name ,'</a></h2>';
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
        echo '<font size="1"><i>'.$language.'</i> - '.$date.'</font><br>';
        echo '<img src="images/info.png" style="vertical-align: middle;"/>';
        echo '&nbsp;&nbsp;'.nl2br($description);
        echo '<section class="'.$languageClass.'"> <pre class="'.$pre_class.'"><code>'.$code.'</code></pre> </section>' ;
        echo '<hr><br>';
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
