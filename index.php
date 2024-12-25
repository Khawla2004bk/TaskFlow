<?php

include_once "config/helper.php";
include_once "config/connexion.php";

if (isset($_GET['action'])) {
    include_once "controllers/" . $_GET['action'] . ".php";
}


if (isset($_GET['page'])) {
    include_once "views/" . $_GET['page'] . ".php";
} else {
    include_once "views/home.php";
}

