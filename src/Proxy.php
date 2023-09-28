<?php // ﷽‎
namespace RapTToR;

/**
 * @author rapttor
 *
 * require __DIR__ . '/protected/vendor/autoload.php';
 * define class Controller() when not using Yii framework
 * 
 * classes/export: Proxy
 * sources: 
- https://api.proxyscrape.com/?request=getproxies&proxytype=socks5
- https://www.npmjs.com/package/proxy-list-builder
- https://github.com/scidam/proxy-list
    https://raw.githubusercontent.com/scidam/proxy-list/master/proxy.json
- https://www.sslproxies.org  
 */

class Proxy
{
    public $proxies = false;
    public $countries = array("US", "CA", "DE", "IT", "ES", "FR", "GB", "AU", "NZ", "JP", "CN", "NL", "SE", "CH", "AT", "BE", "DK", "FI", "IE", "LU", "NO", "PT", "RU", "TR", "IN", "VN", "KR");
    public $schemes = array("http", "socks4", "socks5");
    protected $instance = null;

    public static function debug()
    {
        return defined("DEBUG") && DEBUG;
    }
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct()
    {
        $this->loadLocal();
    }

    public static function isValidIP($ip)
    {
        return filter_var($ip, FILTER_VALIDATE_IP);
    }

    /**
     * @param mixed $ip
     * @param bool $allow_private
     * @param array $proxy_ip
     * 
     * @return [type]
     */
    public static function serverIP($ip, $allow_private = false, $proxy_ip = [])
    {
        if (!is_string($ip) || is_array($proxy_ip) && in_array($ip, $proxy_ip))
            return false;
        $filter_flag = FILTER_FLAG_NO_RES_RANGE;

        if (!$allow_private) {
            //Disallow loopback IP range which doesn't get filtered via 'FILTER_FLAG_NO_PRIV_RANGE' [1]
            //[1] https://www.php.net/manual/en/filter.filters.validate.php
            if (preg_match('/^127\.$/', $ip))
                return false;
            $filter_flag |= FILTER_FLAG_NO_PRIV_RANGE;
        }

        return filter_var($ip, FILTER_VALIDATE_IP, $filter_flag) !== false;
    }
    /**
     * @param bool $allow_private
     * 
     * @return [type]
     */
    public static function clientIP($allow_private = false)
    {
        //Place your trusted proxy server IPs here.
        $proxy_ip = array('127.0.0.1');

        //The header to look for (Make sure to pick the one that your trusted reverse proxy is sending or else you can get spoofed)
        $header = 'HTTP_X_FORWARDED_FOR'; //HTTP_CLIENT_IP, HTTP_X_FORWARDED, HTTP_FORWARDED_FOR, HTTP_FORWARDED

        //If 'REMOTE_ADDR' seems to be a valid client IP, use it.
        if (self::serverIP($_SERVER['REMOTE_ADDR'], $allow_private, $proxy_ip))
            return $_SERVER['REMOTE_ADDR'];

        if (isset($_SERVER[$header])) {
            //Split comma separated values [1] in the header and traverse the proxy chain backwards.
            //[1] https://en.wikipedia.org/wiki/X-Forwarded-For#Format
            $chain = array_reverse(preg_split('/\s*,\s*/', $_SERVER[$header]));
            foreach ($chain as $ip)
                if (self::serverIP($ip, $allow_private, $proxy_ip))
                    return $ip;
        }

        return null;
    }

    //https://deviceatlas.com/blog/list-of-user-agent-strings
    /**
     * @return [type]
     */
    public static function agentsBot()
    {
        return array(
            "Google bot" =>
                "Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)",
            "Bing bot" =>
                "Mozilla/5.0 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)",
            "Yahoo! bot" =>
                "Mozilla/5.0 (compatible; Yahoo! Slurp; http://help.yahoo.com/help/us/ysearch/slurp)",
        );
    }

    /**
     * @return [type]
     */
    public static function agentsDesktop()
    {
        return array(
            "Windows 10-based PC using Edge browser" =>
                "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.135 Safari/537.36 Edge/12.246",
            "Chrome OS-based laptop using Chrome browser (Chromebook)" =>
                "Mozilla/5.0 (X11; CrOS x86_64 8172.45.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.64 Safari/537.36",
            "Mac OS X-based computer using a Safari browser" =>
                "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_2) AppleWebKit/601.3.9 (KHTML, like Gecko) Version/9.0.2 Safari/601.3.9",
            "Windows 7-based PC using a Chrome browser" =>
                "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.111 Safari/537.36",
            "Linux-based PC using a Firefox browser" =>
                "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:15.0) Gecko/20100101 Firefox/15.0.1",
        );
    }

    /**
     * @return [type]
     */
    public static function agentsTablet()
    {
        return array(
            "Google Pixel C" =>
                "Mozilla/5.0 (Linux; Android 7.0; Pixel C Build/NRD90M; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/52.0.2743.98 Safari/537.36",
            "Sony Xperia Z4 Tablet" =>
                "Mozilla/5.0 (Linux; Android 6.0.1; SGP771 Build/32.2.A.0.253; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/52.0.2743.98 Safari/537.36",
            "Nvidia Shield Tablet K1" =>
                "Mozilla/5.0 (Linux; Android 6.0.1; SHIELD Tablet K1 Build/MRA58K; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/55.0.2883.91 Safari/537.36",
            "Samsung Galaxy Tab S3" =>
                "Mozilla/5.0 (Linux; Android 7.0; SM-T827R4 Build/NRD90M) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.116 Safari/537.36",
            "Samsung Galaxy Tab A" =>
                "Mozilla/5.0 (Linux; Android 5.0.2; SAMSUNG SM-T550 Build/LRX22G) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/3.3 Chrome/38.0.2125.102 Safari/537.36",
            "Amazon Kindle Fire HDX 7" =>
                "Mozilla/5.0 (Linux; Android 4.4.3; KFTHWI Build/KTU84M) AppleWebKit/537.36 (KHTML, like Gecko) Silk/47.1.79 like Chrome/47.0.2526.80 Safari/537.36",
            "LG G Pad 7.0" =>
                "Mozilla/5.0 (Linux; Android 5.0.2; LG-V410/V41020c Build/LRX22G) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/34.0.1847.118 Safari/537.36",
        );
    }

    /**
     * @return [type]
     */
    public static function agentsWindowsMobile()
    {
        return array(
            "Microsoft Lumia 650" =>
                "Mozilla/5.0 (Windows Phone 10.0; Android 6.0.1; Microsoft; RM-1152) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116 Mobile Safari/537.36 Edge/15.15254",
            "Microsoft Lumia 550" =>
                "Mozilla/5.0 (Windows Phone 10.0; Android 4.2.1; Microsoft; RM-1127_16056) AppleWebKit/537.36(KHTML, like Gecko) Chrome/42.0.2311.135 Mobile Safari/537.36 Edge/12.10536",
            "Microsoft Lumia 950" =>
                "Mozilla/5.0 (Windows Phone 10.0; Android 4.2.1; Microsoft; Lumia 950) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2486.0 Mobile Safari/537.36 Edge/13.1058",
        );
    }

    /**
     * @return [type]
     */
    public static function agentsIOS()
    {
        return array(
            "Apple iPhone XR (Safari)" =>
                "Mozilla/5.0 (iPhone; CPU iPhone OS 12_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.0 Mobile/15E148 Safari/604.1",
            "Apple iPhone XS (Chrome)" =>
                "Mozilla/5.0 (iPhone; CPU iPhone OS 12_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/69.0.3497.105 Mobile/15E148 Safari/605.1",
            "Apple iPhone XS Max (Firefox)" =>
                "Mozilla/5.0 (iPhone; CPU iPhone OS 12_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) FxiOS/13.2b11866 Mobile/16A366 Safari/605.1.15",
            "Apple iPhone X" =>
                "Mozilla/5.0 (iPhone; CPU iPhone OS 11_0 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A372 Safari/604.1",
            "Apple iPhone 8" =>
                "Mozilla/5.0 (iPhone; CPU iPhone OS 11_0 like Mac OS X) AppleWebKit/604.1.34 (KHTML, like Gecko) Version/11.0 Mobile/15A5341f Safari/604.1",
            "Apple iPhone 8 Plus" =>
                "Mozilla/5.0 (iPhone; CPU iPhone OS 11_0 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A5370a Safari/604.1",
            "Apple iPhone 7" =>
                "Mozilla/5.0 (iPhone9,3; U; CPU iPhone OS 10_0_1 like Mac OS X) AppleWebKit/602.1.50 (KHTML, like Gecko) Version/10.0 Mobile/14A403 Safari/602.1",
            "Apple iPhone 7 Plus" =>
                "Mozilla/5.0 (iPhone9,4; U; CPU iPhone OS 10_0_1 like Mac OS X) AppleWebKit/602.1.50 (KHTML, like Gecko) Version/10.0 Mobile/14A403 Safari/602.1",
            "Apple iPhone 6" =>
                "Mozilla/5.0 (Apple-iPhone7C2/1202.466; U; CPU like Mac OS X; en) AppleWebKit/420+ (KHTML, like Gecko) Version/3.0 Mobile/1A543 Safari/419.3",
        );
    }

    /**
     * @return [type]
     */
    public static function agentsAndroid()
    {
        return array(
            "Samsung Galaxy S9" =>
                "Mozilla/5.0 (Linux; Android 8.0.0; SM-G960F Build/R16NW) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.84 Mobile Safari/537.36",
            "Samsung Galaxy S8" =>
                "Mozilla/5.0 (Linux; Android 7.0; SM-G892A Build/NRD90M; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/60.0.3112.107 Mobile Safari/537.36",
            "Samsung Galaxy S7" =>
                "Mozilla/5.0 (Linux; Android 7.0; SM-G930VC Build/NRD90M; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/58.0.3029.83 Mobile Safari/537.36",
            "Samsung Galaxy S7 Edge" =>
                "Mozilla/5.0 (Linux; Android 6.0.1; SM-G935S Build/MMB29K; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/55.0.2883.91 Mobile Safari/537.36",
            "Samsung Galaxy S6" =>
                "Mozilla/5.0 (Linux; Android 6.0.1; SM-G920V Build/MMB29K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.98 Mobile Safari/537.36",
            "Samsung Galaxy S6 Edge Plus" =>
                "Mozilla/5.0 (Linux; Android 5.1.1; SM-G928X Build/LMY47X) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.83 Mobile Safari/537.36",
            "Nexus 6P" =>
                "Mozilla/5.0 (Linux; Android 6.0.1; Nexus 6P Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.83 Mobile Safari/537.36",
            "Sony Xperia XZ" =>
                "Mozilla/5.0 (Linux; Android 7.1.1; G8231 Build/41.2.A.0.219; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/59.0.3071.125 Mobile Safari/537.36",
            "Sony Xperia Z5" =>
                "Mozilla/5.0 (Linux; Android 6.0.1; E6653 Build/32.2.A.0.253) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.98 Mobile Safari/537.36",
            "HTC One X10" =>
                "Mozilla/5.0 (Linux; Android 6.0; HTC One X10 Build/MRA58K; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/61.0.3163.98 Mobile Safari/537.36",
            "HTC One M9" =>
                "Mozilla/5.0 (Linux; Android 6.0; HTC One M9 Build/MRA58K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.98 Mobile Safari/537.3",
        );
    }

    /**
     * @return [type]
     */
    public static function agents()
    {
        return
            self::agentsAndroid() +
            self::agentsDesktop() +
            self::agentsIOS() +
            self::agentsTablet() +
            self::agentsWindowsMobile();
    }

    /**
     * @param null $id (<1 for random)
     * @param null $agents
     * @return mixed
     */
    public static function agent($id = null, $agents = null)
    {
        if (is_null($agents))
            $agents = self::agents();
        $agents = array_values($agents);
        if (is_null($id) || $id < 1)
            $id = rand(0, count($agents) - 1);
        return $agents[$id];
    }

    /**
     * @return [type]
     */
    public static function localProxiesFilename($makedir = true)
    {
        $dir = __DIR__ . "/data";
        $filename = __DIR__ . "/data/proxylist.json";
        if (!is_dir($dir) && $makedir)
            @mkdir($dir);
        return $filename;
    }

    public function loadLocal()
    {
        $return = false;
        $filename = self::localProxiesFilename();
        if (is_file($filename)) {
            $data = file_get_contents($filename);
            $json = json_decode($data, true);
            foreach ($json as $p) {
                $this->addProxy($p);
            }
            $return = $json;
        }
        return $return;
    }

    public function saveLocal()
    {
        $filename = self::localProxiesFilename();
        if (!($this->proxies)) {
            $this->loadLocal();
        } else {
            $data = json_encode(array_values($this->proxies), JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE);
            return file_put_contents($filename, $data);
        }
        return false;
    }


    public static function asProxies($mixed)
    {
        if (is_array($mixed))
            return $mixed;
        return json_decode($mixed, true);
    }

    /**
     * @return [type]
     */
    public static function proxyKey($p)
    {
        return (is_array($p) ?
            (($p["scheme"] ?? "") . '-' . ($p["ip"] ?? "") . '-' . ($p["port"] ?? ""))
            : json_encode($p));
    }

    public static function loadLocalFile()
    {
        $file = self::localProxiesFilename();
        $return = false;
        if (is_file($file)) {
            $data = file_get_contents($file);
            $json = json_decode($data, true);
            $return = array();
            if ($json && is_array($json)) {
                foreach ($json as $proxy) {
                    $key = self::proxyKey($proxy);
                    $return[$key] = $proxy;
                }
            }
        }
        return $return;
    }


    public function loadAll()
    {
        $this->remoteLoadProxyscrape();
        $this->remoteLoadFreeProxyList();
        // $this->remoteLoadProxyNova(); // problems in parsing, need selenium
        return $this->proxies;
    }

    public function loadAllCountries()
    {
        $filename = __DIR__ . "/data/countiesstates.json";
        $data = $countries = false;
        if (is_file($filename)) {
            $data = file_get_contents($filename);
            $countries = json_decode($data, true);
        }
        return $countries;
    }

    public static function getCachedUrl($url, &$forced = false)
    {
        $key = sha1($url);
        if (!is_dir(__DIR__ . "/cache"))
            @mkdir(__DIR__ . "/cache");
        $filename = __DIR__ . "/cache/" . $key;

        $data = false;
        if (is_file($filename)) {
            $data = gzuncompress(file_get_contents($filename));
        }
        if (!$data || $forced) {
            if (self::debug())
                echo "Loading remotely:" . $url . "\n";
            $forced = true;
            $data = file_get_contents($url);
            if ($data && strlen($data) > 1) {
                file_put_contents($filename, gzcompress($data));
                file_put_contents($filename . '.raw', $data);
            }
        }
        return $data;
    }

    public function addProxy($p, $autosave = true)
    {
        if (
            is_array($p)
            && isset($p["ip"])
            && self::isValidIP($p["ip"])
        ) {
            if (!$this->proxies)
                $this->proxies = array();
            foreach ($p as $k => $v)
                $p[$k] = (is_string($v)) ? trim($v) : $v;
            $this->proxies[self::proxyKey($p)] = $p;
            if ($autosave)
                $this->saveLocal();
        }
    }

    public function remoteLoadProxyscrape()
    {
        //var_dump($proxies);
        $countries = array("all") + $this->countries; // this api supports "all"
        $schemes = $this->schemes;
        $currentrun = array();
        foreach ($countries as $code) {
            foreach ($schemes as $scheme) {
                $url = 'https://api.proxyscrape.com?request=getproxies&proxytype=' . $scheme . '&timeout=10000&country=' . $code . '&ssl=all&anonymity=all';
                $isForced = false;
                $data = self::getCachedUrl($url, $isForced);
                echo $url . "\n";
                if ($data) {
                    $data = str_ireplace("\\r\\n", "\n", $data);
                    $lines = explode("\n", $data);
                    if (is_array($lines)) {
                        foreach ($lines as $one) {
                            $proxy = explode(':', $one);
                            if (is_array($proxy) && isset($proxy[0])) {
                                $proxy[1] = $proxy[1] ?? 80;
                                $record = array(
                                    "scheme" => $scheme,
                                    "ip" => $proxy[0],
                                    "port" => $proxy[1],
                                    "country" => $code,
                                );
                                $this->addProxy($record, false);
                                $currentrun[self::proxyKey($record)] = $record;
                            }
                        }
                    }
                    if (count($this->proxies) > 0)
                        $this->saveLocal();
                }
                if ($isForced) // was forced loading
                    sleep(1);
            } // scheme
            if (self::debug()) {
                echo "\n total/current:", count($this->proxies), "/", count($currentrun) . "\n";
            }
            $this->saveLocal();
        } // country
        return $currentrun;
    }

    public function remoteLoadProxyNova()
    {
        $errors = array();
        $currentrun = array();
        $scheme = "http";
        $head = array("ip", "port", false, "speed", "uptime", false, "anonymity");
        foreach ($this->countries as $country) {
            $url = "https://www.proxynova.com/proxy-server-list/country-" . strtolower($country);
            $html = $this->getCachedUrl($url);

            $table = substr($html, stripos($html, "<table"), strlen($html));
            $table = substr($table, 0, stripos($table, "/table>") + 7);

            $table = $html;
            $DOM = new \DOMDocument();
            libxml_use_internal_errors(true);
            $DOM->loadHTML($table);
            $errors = libxml_get_errors();
            if (self::debug()) {
                foreach ($errors as $error) {
                    echo '[ERROR]' . json_encode($error) . "\n";
                }
                // die;
            }
            $rows = $DOM->getElementsByTagName("tr");
            for ($i = 0; $i < $rows->length; $i++) {
                $cols = $rows->item($i)->getElementsbyTagName("td");
                $row = array();
                for ($j = 0; $j < $cols->length; $j++) {
                    if (isset($head[$j]) && $head[$j]) {
                        $key = (isset($head[$j]) && $head[$j]) ? $head[$j] : $j;
                        $row[$key] = $cols->item($j)->nodeValue;
                        if (
                            isset($row) && is_array($row)
                            &&
                            ((isset($row[0]) && $this->isValidIP($row[0]) ||
                                (isset($row["ip"]) && $this->isValidIP($row["ip"])))
                            )
                        ) {
                            $row["scheme"] = $scheme;
                            // $this->addProxy($row);
                            $currentrun[] = $row;
                        } else
                            $errors[] = $row;
                    }

                }
                //echo "\n";
            }
            var_dump($currentrun);
            var_dump($errors);
            die;
        }

        return $currentrun;
    }

    public function remoteLoadFreeProxyList()
    {
        $currentrun = array();
        $domains = array(
            array(
                "scheme" => "http",
                "url" => "https://free-proxy-list.net/anonymous-proxy.html",
                "head" => array("ip", "port", "country", false, "anonymity", "google", "https", false)
            ),
            array(
                "scheme" => "socks4",
                "url" => "https://www.socks-proxy.net/",
                "head" => array("ip", "port", "country", false, false, "anonymity", "https", false)
            ),
        );
        foreach ($domains as $domain) {
            $scheme = $domain["scheme"];
            $url = $domain["url"];
            $head = $domain["head"];

            $html = $this->getCachedUrl($url);

            $table = substr($html, stripos($html, "<TABLE"), strlen($html));
            $table = substr($table, 0, stripos($table, "/TABLE>") + 7);

            $DOM = new \DOMDocument();
            $DOM->loadHTML($table);
            $rows = $DOM->getElementsByTagName("tr");

            for ($i = 0; $i < $rows->length; $i++) {
                $cols = $rows->item($i)->getElementsbyTagName("td");
                $row = array();
                for ($j = 0; $j < $cols->length; $j++) {
                    // echo $cols->item($j)->nodeValue, "\t";
                    // you can also use DOMElement::textContent
                    // echo $cols->item($j)->textContent, "\t";
                    if (isset($head[$j]) && $head[$j]) {
                        $key = (isset($head[$j]) && $head[$j]) ? $head[$j] : $j;
                        $row[$key] = $cols->item($j)->nodeValue;
                        if (
                            isset($row) && is_array($row)
                            &&
                            ((isset($row[0]) && $this->isValidIP($row[0]) ||
                                (isset($row["ip"]) && $this->isValidIP($row["ip"])))
                            )
                        ) {
                            $row["scheme"] = $scheme;
                            $this->addProxy($row);
                            $currentrun[] = $row;
                        }
                    }

                }
                //echo "\n";
            }
        }
        return $currentrun;
    }

    /**
     * @param bool $force
     * 
     * @return [type]
     */
    public static function all($force = false)
    {
        if (file_exists(self::localProxiesFilename()) && !$force) {
            $data = file_get_contents(self::localProxiesFilename());
        } else {
            $data = self::load();
        }
        return $data;
    }

    /**
     * @param null $id (<1 for random)
     * @return array|null
     */
    public static function one($id = null)
    {
        $data = self::all();
        $proxies = explode("\r\n", $data);
        if (strlen($data) > 0 && count($proxies) > 2) {
            if (is_null($id) || $id < 1)
                $id = rand(0, count($proxies) - 2);
            $proxyUrl = $proxies[$id];
            $p = explode(":", $proxyUrl);
            if (count($p) == 1 || strlen($p[1]) == 0)
                $p[1] = "80";
            $p[2] = $id;
            return $p;
        }
        return null;
    }

    public function getAll($criteria)
    {
        $matching = $this->matching($criteria);
        return $matching;
    }

    public function get($criteria = array())
    {
        $matching = $this->matching($criteria);
        if (is_array($matching) && count($matching) > 0) {
            $random = array_rand($matching);
            return $matching[$random];
        }
        return false;
    }

    public function matching($criteria)
    {
        $matching = array();
        if (is_array($criteria)) {
            foreach ($criteria as $k => $v)
                if (is_string($v) && is_string($k)) {
                    $criteria[strtolower($k)] == strtolower($v);
                }
            foreach ($this->proxies as $proxy) {
                if (
                    isset($criteria["scheme"]) && isset($proxy["scheme"])
                    && strtolower($criteria["scheme"]) != strtolower($proxy["scheme"])
                )
                    continue;
                if (
                    isset($criteria["country"]) && isset($proxy["country"]) &&
                    strtolower($criteria["country"]) != strtolower($proxy["country"])
                )
                    continue;
                if (
                    isset($criteria["countries"]) && isset($proxy["country"])
                    && !in_array(strtolower($proxy["country"]), explode(',', strtolower(implode(',', $criteria["countries"]))))
                )
                    continue;
                if (isset($criteria["speed"]) && isset($proxy["speed"]) && !((int) $criteria["speed"] > (int) $proxy["speed"]))
                    continue;
                if (isset($criteria["uptime"]) && isset($proxy["uptime"]) && !((int) $criteria["uptime"] < (int) $proxy["uptime"]))
                    continue;
                if (
                    isset($criteria["anonymity"]) && isset($proxy["anonymity"]) &&
                    !(stripos(strtolower($proxy["anonymity"]), $criteria["anonymity"]) !== false)
                )
                    continue;
                $matching[] = $proxy;
            }
        } else
            return $this->proxies;
        return $matching;
    }
}