<?php
require_once(__DIR__ . "/../vendor/autoload.php");
require_once(__DIR__ . "/../src/Proxy.php");
defined("DEBUG") || define("DEBUG", 1);
$p = new RapTToR\Proxy;
echo "<pre>";
// load remote: 
// $all = $p->loadAll();
$all = $p->loadLocal();
echo json_encode($all, JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE);
echo "<br/>\n Total: " . count($all) . "\n\n<br/>\n";

//$p->remoteLoadSpyMe();
//$p->remoteLoadProxyDaily();
//$p->remoteLoadProxyListPlus();
//$p->remoteLoadSpysOne();

/* 
if (false) {
    $all = $p->loadAll();
    var_dump($all);
}

if (false) {
    ## test
    $proxies = (new \RapTToR\Proxy)->getAll(
        array(
            "scheme" => "socks4",
            "countries" => array("us", "ca"),
        )
    );
    var_dump("proxies count:", count($proxies));
}

if (false) {
    $proxy = $proxies[array_rand($proxies)];
    var_dump("proxy", $proxy);
}
*/