<?php
/*
  @author: Ademcan (ademcan@ademcan.net)
  @name: details.php
  @description: show a single snippet in one page with the related information, helpful for snippet sharing
 */

    session_start();

    include "config.php";
    include 'includes/menu.php';

    $mytable ="tags";
    $tag=$_GET["tag"];
    $tag= SQLite3::escapeString($tag);
    $base=new SQLite3($config["dbname"]);

    // $query_name = "SELECT * FROM snippets WHERE tags = '$tag' ";
    $query_name = "SELECT * FROM snippets";

    $results_name = $base->query($query_name);

    while($row = $results_name->fetchArray())
    {
        $tags= $row['tags'];
        $tagsList=explode(",",$tags);

        if (in_array($tag, $tagsList)) {

            $name = $row['name'];
            echo '<h1 style="padding-top:50px;"> '.$name;

            $private = $row['private'];
            $lines = $row['lines'];
            if ($private=="on"){
                echo '<img src="images/lockFlat.png" style="width:20px; height: 20px;padding-left:10px;" />';
            }
            echo '</h1>';

            $code = $row['code'];
            $language = $row['language'];
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

            echo '<font size="1"><i>'.$language.'</i> - '.$date.'</font><br>';
            echo '<img src="images/info.png" style="vertical-align: middle;"/>';
            echo '&nbsp;&nbsp;'.nl2br($description);
            if ($lines=="on"){
                echo '<section class="'.$languageClass.'"> <pre class="line-numbers"><code>'.$code.'</code></pre> </section>';
            }
            else if ($highlight!=""){
                echo '<section class="'.$languageClass.'"> <pre data-line='.$highlight.'><code>'.$code.'</code></pre> </section>';
            }
            else {
                echo '<section class="'.$languageClass.'"> <pre><code>'.$code.'</code></pre> </section>' ;
            }
            echo '<hr style="width:95%; height:5px;background-color:#2ecc71;border-radius:20px;border-color:#2ecc71;border-style:none;">';

        }
    }

    echo '</div></body></html>';
?>
