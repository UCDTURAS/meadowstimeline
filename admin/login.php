<?php
/**
 * @author daithi coombes <daithi.coombes@futureanalytics.ie>
 */

//logging out?
if(isset($_REQUEST['logout'])){
    unset($_SESSION['loggedIn']);
    session_unset();
    session_destroy();
    header('Location: '.$_SERVER['SCRIPT_NAME']);
}

//logging in?
if(isset($_POST['user']) && isset($_POST['pswd']) && !isset($_SESSION['loggedIn'])){

    if($_POST['user']==='admin' && $_POST['pswd']==='0nc3Apon471m3'){
        $_SESSION['loggedIn'] = true;
        header('Location: '.$_SERVER['SCRIPT_NAME']);
        die();
    }
}

//default show login form and die()
elseif(!isset($_SESSION['loggedIn'])){

    ?>
    <form method="post">
        <label for="user">User</label>
        <input type="text" name="user" id="user" placeholder="enter your username">
        <label for="pswd">Password</label>
        <input type="password" name="pswd" id="pswd" placeholder="enter your password">
        <input type="submit" value="Log In">
    </form>
    <?php

    die();
}
