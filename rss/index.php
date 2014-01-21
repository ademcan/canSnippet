<?php
    /*
    @author: Ademcan (ademcan@ademcan.net)
    @name: index.php
    @description: creation of the rss feed
    */

    include "../autoload.php";

    header("Content-Type: application/rss+xml; charset=ISO-8859-1");
    $rss = '<?xml version="1.0" encoding="ISO-8859-1"?>';
    $rss .= '<rss version="2.0">';
    $rss .= '<channel>';
    $rss .= '<title>snippets RSS feed</title>';
    $rss .= '<link>http://ademcan.net/</link>';
    $rss .= '<description>This is an example RSS feed</description>';
    $rss .= '<language>en-us</language>';

    $mytable = "snippets";
    $base = Factory::database($parameters);
    $query_name = "SELECT * FROM ".$mytable;
    $row = $base->execute($query_name)->fetchArray();
       
    while ($row) {
        // add only public snippets to the RSS feed
        if ($row['private'] !== "on"){
            $rss .= '<item>';
            $rss .= '<title>' .$row['name']. '</title>';
            $rss .= '<description>' .$row['description']. '</description>';
            $rss .= '<pubDate>' . $row['date'] . '</pubDate>';
            $rss .= '</item>';
        }
    }
    $rss .= '</channel>';
    $rss .= '</rss>';
 
    echo $rss;