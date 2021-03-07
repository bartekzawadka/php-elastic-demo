<?php

use Elasticsearch\ClientBuilder;

require 'vendor/autoload.php';
$hosts = [];
$hosts = isset($_ENV['ELASTIC_ENDPOINT'])
    ? [$_ENV['ELASTIC_ENDPOINT']]
    : ['http://bz-elastic.westeurope.azurecontainer.io:9200'];

$client = ClientBuilder::create()
    ->setHosts($hosts)
    ->build();

function addDocument($data)
{
    $entry = [
        'index' => 'test',
        'type' => 'test_type',
        'body' => $data
    ];

    $response = $GLOBALS['client']->index($entry);
    echo json_encode($response);
}

function findDocuments($search)
{
    $params = [
        'index' => 'test',
        'type' => 'test_type',
        'body' => [
            'query' => [
                'query_string' => [
                    'query' => '*' . $search . '*',
                    'fields' => ['firstName', 'lastName', 'description', 'houseNo']
                ]
            ]
        ]
    ];

    $c = $GLOBALS['client'];
    $results = $c->search($params);
    echo json_encode($results);
}

function findDocumentsFuzzy($search)
{
    $params = [
        'index' => 'test',
        'type' => 'test_type',
        'body' => [
            'query' => [
                'match' => [
                    'description' => [
                        'query' => $search,
                        'operator' => 'and',
                        'fuzziness' => 2
                    ]
                ]
            ]
        ]
    ];

    $c = $GLOBALS['client'];
    $results = $c->search($params);
    echo json_encode($results);
}

if ($_SERVER['REQUEST_METHOD'] == "POST" and (isset($_POST['action']))) {
    switch ($_POST['action']) {
        case "addDocument":
            addDocument($_POST['data']);
            break;
        case "search":
            $searchPhrase = '';
            if (isset($_POST['value'])) {
                $searchPhrase = $_POST['value'];
            }

            findDocuments($searchPhrase);
            break;
        case "searchFuzzy":
            $searchPhrase = '';
            if (isset($_POST['value'])) {
                $searchPhrase = $_POST['value'];
            }

            findDocumentsFuzzy($searchPhrase);
    }
}

