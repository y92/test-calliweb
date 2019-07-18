<?php

function userConnected()
{
    return isset($_SESSION['userId']);
}

function connectUser(int $id)
{
    $_SESSION['userId'] = $id;
}

function getUserIdSession() : ?int
{
    return $_SESSION['userId'] ?? null;
}

function disconnectUser()
{
    unset($_SESSION['userId']);
}

function generateToken() : string
{
    return hash('sha256', microtime());
}