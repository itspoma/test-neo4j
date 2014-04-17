<?php
require('../vendor/autoload.php');

use Everyman\Neo4j\Client,
    Everyman\Neo4j\Transport,
    Everyman\Neo4j\Index\NodeIndex,
    Everyman\Neo4j\Node,
    Everyman\Neo4j\Relationship,
    Everyman\Neo4j\Gremlin;

$client = new Client(new Transport\Curl('localhost', 7474));

$queryTemplate = "g.V.in(type).dedup.sort{it.name}.toList()";

$params = array('type' => 'IN');
$query = new Gremlin\Query($client, $queryTemplate, $params);
$result = $query->getResultSet();

foreach ($result as $row) {
    echo "* " . $row[0]->getProperty('name')."\n";
}