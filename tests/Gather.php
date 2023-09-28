<?php
require_once(__DIR__ . "/../vendor/autoload.php");
require_once(__DIR__ . "/../src/Proxy.php");

defined("DEBUG") || define("DEBUG", 1);

$p = new RapTToR\Proxy;
$all = $p->loadAll();
var_dump($all);

## test

$p = (new \RapTToR\Proxy)->get(
    array(
        "scheme" => "socks4",
        "country" => "ca"
    )
);
var_dump($p);