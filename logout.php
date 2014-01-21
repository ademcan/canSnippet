<?php

    /*
    @author: Ademcan (ademcan@ademcan.net)
    @name: logout.php
    @description: logout code
    */
    session_start();
    $_SESSION = array(); //destroy all of the session variables
    session_destroy();
    header("location:index.php");
?>
