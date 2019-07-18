<?php session_start(); ?>

<?php

include_once __DIR__.'/functions.php';

disconnectUser();

header('Location: .');
exit();
