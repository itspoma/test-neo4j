<?php
require('../vendor/autoload.php');

use Everyman\Neo4j\Client,
    Everyman\Neo4j\Transport;

$client = new Client(new Transport\Curl('localhost', 7474));

$query = new Everyman\Neo4j\Cypher\Query($client, 'START n=node(*) RETURN count(n) as c');
$result = $query->getResultSet();

$count = $result[0]['c'];

$query = new Everyman\Neo4j\Cypher\Query($client, 'MATCH (n) OPTIONAL MATCH (n)-[r]-() DELETE n,r');
$result = $query->getResultSet();

echo sprintf('removed %d nodes/rels', $count);