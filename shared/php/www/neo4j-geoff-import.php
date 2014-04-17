<?php
require('../vendor/autoload.php');

use Everyman\Neo4j\Client,
    Everyman\Neo4j\Transport,
    Everyman\Neo4j\Node,
    Everyman\Neo4j\Relationship,
    Everyman\Neo4j\Geoff;

$client = new Client(new Transport\Curl('localhost', 7474));

$geoff = new Geoff($client);

// $handle = fopen('dump.geoff', 'r');
// $singleBatch = $geoff->load($handle);
// $singleBatch->commit();

$geoffString = '(Liz) {"name": "Elizabeth", "title": "Queen of the Commonwealth Realms"}';
$multiBatch = $geoff->load($handle);
