# RapTToR Proxy Class

## renew proxies:
occasionally, recommended once a hour run loadAll method
the list will invalidate once a day, and will get re-loaded.

    $p=new \RapTToR\Proxy; $P->loadAll();

## run/get a proxy: 
get one proxy (no parameters required, but can use all of them combines)

    $proxy = (new \RapTToR\Proxy)->get(
        array(
            "country" => "ca"
            // "countries"=>array("ca","us),
            // "speed"=>"100", // all lower response than 100ms
            // "uptime"=>"90", // all uptime larger than 90%
            // "anonimity"=>"anonimous", 
            // "scheme"=>"socks4", 
        )
    );

## response:

  array(4) {
    ["scheme"]=> string(6) "socks4"
    ["ip"]=> string(13) "192.99.201.39"
    ["port"]=> string(5) "18336"
    ["country"]=> string(2) "CA"
  }


## todo: 
- adding more sources
- keep and validate logs
- optimization
- unit-Testing with PHPUnit [in progress]

## changelog 
- Sep 29. 2023 : First public release
- Sep 21. 2023 : first draft of the class

## links

- [github](https://github.com/rapttor/proxy.git)
- [website](www.rapttor.com) 

## licence

- DBAD (https://github.com/rapttor/proxy.git/LICENCE)

## install 

    composer require rapttor/proxy

or

    "require": {
      "rapttor/proxy": "dev-master"
    },
    "repositories":[
      {
        "type": "vcs",
        "url": "https://github.com/rapttor/proxy.git"
      }
    ]
