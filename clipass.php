<?php

use CliPass\Associator;
use CliPass\Command;
use CliPass\Crypt;
use CliPass\Identity;
use CliPass\Input\StdIn;
use CliPass\KeePassConnector;
use CliPass\LoginsProvider;
use CliPass\Output\Factory As OutputFactory;
use CliPass\Response\Builder AS ResponseBuilder;
use CliPass\StringEncoder\Base64Encoder;
use Gaufrette\Adapter\Local As GaufretteLocalAdapter;
use Ulrichsg\Getopt;
use Buzz\Browser As BuzzBrowser;

chdir(__DIR__);

include('./bootstrap.php');

umask(0066);

$container = new Pimple();

$container['identity_path'] = $_SERVER['HOME'];

$container['base64Encoder'] = $container->share(function() {
    return new Base64Encoder();
});

$container['identity'] = $container->share(function($c) {
    return new Identity(new GaufretteLocalAdapter($c['identity_path']), $c['base64Encoder']);
});

$container['crypt'] = $container->share(function() {
    return new Crypt();
});

$container['buzzBrowser']  = $container->share(function() {
    return new BuzzBrowser();
});

$container['responseBuilder']  = $container->share(function() {
    return new ResponseBuilder();
});

$container['associator']  = $container->share(function($c) {
    return new Associator($c['identity'], $c['crypt'], $c['base64Encoder'], $c['buzzBrowser'], $c['responseBuilder']);
});

$container['loginsProvider']  = $container->share(function($c) {
    return new LoginsProvider(
        $c['identity'],
        $c['crypt'],
        $c['base64Encoder'],
        $c['buzzBrowser'],
        $c['responseBuilder']
    );
});

$container['keePassConnector']  = $container->share(function($c) {
    return new KeePassConnector($c['associator'], $c['loginsProvider']);
});

$command = new Command(new Getopt(), new StdIn(), new OutputFactory(), $container['keePassConnector']);
$command->execute();
