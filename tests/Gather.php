<?php
require_once(__DIR__ . "/../vendor/autoload.php");
require_once(__DIR__ . "/../src/Proxy.php");
defined("DEBUG") || define("DEBUG", 1);
$p = new RapTToR\Proxy;
/* 
$all = $p->loadAll();
var_dump($all);
*/

## test
$proxies = (new \RapTToR\Proxy)->getAll(
    array(
        "scheme" => "socks4",
        "countries" => array("us", "ca"),
    )
);
var_dump("proxies count:", count($proxies));

$proxy = $proxies[array_rand($proxies)];
var_dump("proxy", $proxy);