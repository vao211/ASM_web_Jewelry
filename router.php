<?php
//router file

if ($_SERVER['REQUEST_URI'] === '/' || $_SERVER['REQUEST_URI'] === '') {
    readfile('frontend/index.html');
    exit;
}