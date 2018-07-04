<?php

    /*
    @author: Ademcan (ademcan@ademcan.net)
    @name: index.php
    @description: creation of the rss feed
    */
    $rawurl = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $url = explode('/', $rawurl );
    array_pop($url);
    array_pop($url);
    $url2=implode("/",$url);

    header("Content-Type: application/rss+xml; charset=UTF-8");
    $rss = '<?xml version="1.0" encoding="UTF-8"?>';
    $rss .= '<rss version="2.0">';
    $rss .= '<channel>';
    $rss .= '<title>snippets RSS feed</title>';
    $rss .= '<link>'.$url2.'</link>';
    $rss .= '<description>This is an example RSS feed</description>';
    $rss .= '<language>en-us</language>';

    $dbname='../snippets.sqlite';
    $mytable ="snippets";
    $base=new SQLite3($dbname);
    $query_name = "SELECT * FROM $mytable WHERE private='off' ";
    $results_name = $base->query($query_name);

    while($row = $results_name->fetchArray())
    {
        $rss .= '<item>';
        $rss .= '<title>' .$row['name']. '</title>';
        $rss .= '<link>'.$url2.'/details.php?id='.$row['ID'].'</link>';
        $rss .= '<description>' .$row['description']. '</description>';
        $rss .= '<pubDate>' . $row['date'] . '</pubDate>';
        $rss .= '</item>';
    }
    $rss .= '</channel>';
    $rss .= '</rss>';
    echo $rss;

?>
