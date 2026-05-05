<?php

session_start();
session_destroy();
header('Location: ../../public/connexionClient.php');
exit;