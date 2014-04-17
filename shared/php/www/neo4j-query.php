<?php
require('../vendor/autoload.php');

use Everyman\Neo4j\Client,
    Everyman\Neo4j\Transport,
    Everyman\Neo4j\Index\NodeIndex,
    Everyman\Neo4j\Node,
    Everyman\Neo4j\Relationship,
    Everyman\Neo4j\Gremlin;

$client = new Client(new Transport\Curl('localhost', 7474));

$personIndex = new NodeIndex($client, 'Person');

$bohdan = $personIndex->queryOne('name:Bohda*');
$viktor = $personIndex->queryOne('name:Viktor');
$anatoliy = $personIndex->queryOne('name:Anatoliy');

$path = $bohdan->findPathsTo($viktor)
            ->setMaxDepth(12)
            ->getSinglePath();

$pathDetailed = array();

foreach ($path as $i => $node) {
    $pathDetailed[] = $node->getProperty('name');
}

echo join(' --> ', $pathDetailed);