<?php
require('../vendor/autoload.php');

use Everyman\Neo4j\Client,
    Everyman\Neo4j\Transport,
    Everyman\Neo4j\Node,
    Everyman\Neo4j\Relationship,
    Everyman\Neo4j\Geoff;

$client = new Client(new Transport\Curl('localhost', 7474));

$geoff = new Geoff($client);

// $handle = fopen('dump.geoff', 'w+');
// $geoff->dump(array($pathA, $pathB), $handle);

// $geoffString = $geoff->dump($pathA);

$geoff->dump(array($pathA, $pathB));