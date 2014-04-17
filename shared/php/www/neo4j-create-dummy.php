<?php
require('neo4j-cleanup.php');
require('../vendor/autoload.php');

use Everyman\Neo4j\Client,
    Everyman\Neo4j\Transport,
    Everyman\Neo4j\Node,
    Everyman\Neo4j\Relationship,
    Everyman\Neo4j\Index\NodeIndex,
    Everyman\Neo4j\Index\RelationshipIndex,
    Everyman\Neo4j\Index\NodeFulltextIndex;

$client = new Client(new Transport\Curl('localhost', 7474));

//
// index

$companyIndex = new NodeIndex($client, 'Company');
$personIndex = new NodeIndex($client, 'Person');
$positionIndex = new NodeIndex($client, 'Position');

$companyIndex->delete();
$personIndex->delete();
$positionIndex->delete();

$companyLabel = $client->makeLabel('Company');
$personLabel = $client->makeLabel('Person');
$positionLabel = $client->makeLabel('Position');


//
// data

$positions = array('Developer', 'Designer', 'Support', 'Recruiter');

$items = array(
    'EPAM Systems' => array(
        'Alexei', 'Anatoliy', 'Anton', 'Arkadiy', 'Artem',
        'Adolf', 'Azariy', 'Alfred', 'Amajak', 'Andrew',
        'Aristarh', 'Arnold', 'Artur', 'Ashot',
        'Anya',
    ),

    'SoftServe' => array(
        'Benedikt', 'Bernar', 'Bohdan', 'Boris',
        'Borislav', 'Bruno',
    ),

    'Ciklum' => array(
        'Vadim', 'Valentin', 'Valeriy', 'Vasiliy',
        'Viktor', 'Vitaliy', 'Vladimir', 'Vsevolod',
        'Vaselina',
    ),

    'Luxoft' => array(
        'Galaktion', 'Garry', 'Gennadiy',
        'Genrih', 'Gerasim', 'Gordon',
        'Galina',
    ),
);

$nodes = array(
    'positions' => array(),
    'companies' => array(),
    'persons' => array(),
);

echo '<pre>';

foreach ($positions as $positionName) {
    $position = $client->makeNode()
        ->setProperty('name', $positionName)
        ->save();

    $position->addLabels(array($positionLabel));

    $positionIndex->add($position, 'name', $position->getProperty('name'));

    $nodes['positions'][$positionName] = $position;

    echo sprintf("position #%s \"%s\"\n", $position->getId(), $positionName);
}

echo "\n";

foreach ($items as $companyName => $personsNames) {
    $company = $client->makeNode()
        ->setProperty('name', $companyName)
        ->save();

    $company->addLabels(array($companyLabel));

    $companyIndex->add($company, 'name', $company->getProperty('name'));

    $nodes['companies'][$companyName] = $company;

    echo sprintf("company #%s \"%s\"\n", $company->getId(), $companyName);

    foreach ($personsNames as $personName) {
        $person = $client->makeNode()
            ->setProperty('name', $personName)
            ->setProperty('age', rand(18,50))
            ->setProperty('companyName', $companyName)
            ->save();

        $person->addLabels(array($personLabel));

        $personIndex->add($person, 'name', $person->getProperty('name'));

        $position = $nodes['positions'][$positions[array_rand($positions)]];

        $person->relateTo($company, 'WORKS_IN')->save();
        $person->relateTo($position, 'POSITION')->save();

        $nodes['persons'][$personName] = $person;

        echo sprintf("\tperson #%s \"%s\"\n", $person->getId(), $personName);
    }
}

$nodes['persons']['Alexei']->relateTo($nodes['persons']['Bohdan'], 'FRIEND')->save();
$nodes['persons']['Vadim']->relateTo($nodes['persons']['Vladimir'], 'FRIEND')->save();
$nodes['persons']['Gennadiy']->relateTo($nodes['persons']['Alexei'], 'FRIEND')->save();
$nodes['persons']['Viktor']->relateTo($nodes['persons']['Gennadiy'], 'FRIEND')->save();
// $nodes['persons']['Vadim']->relateTo($nodes['persons']['Viktor'], 'FRIEND')->save();
// $nodes['persons']['Vladimir']->relateTo($nodes['persons']['Bohdan'], 'FRIEND')->save();

$nodes['persons']['Alexei']->relateTo($nodes['persons']['Anya'], 'LOVE')->save();
$nodes['persons']['Anya']->relateTo($nodes['persons']['Vadim'], 'LOVE')->save();
$nodes['persons']['Vadim']->relateTo($nodes['persons']['Anya'], 'LOVE')->save();
$nodes['persons']['Vaselina']->relateTo($nodes['persons']['Gennadiy'], 'LOVE')->save();
$nodes['persons']['Galina']->relateTo($nodes['persons']['Gennadiy'], 'LOVE')->save();

echo "\n";
echo sprintf('created %d nodes',
        count($nodes['positions']) +
        count($nodes['companies']) +
        count($nodes['persons'])
     );