<?php
    require('../vendor/autoload.php');

    use Everyman\Neo4j\Client,
        Everyman\Neo4j\Transport;

    $client = new Client(new Transport\Curl('localhost', 7474));
    // $client = new Client(new Transport\Stream('localhost', 7474));

    echo '<pre>';
    print_r($client->getServerInfo());