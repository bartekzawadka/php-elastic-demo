<?php
    require 'vendor/autoload.php';
    $hosts = [
        'http://bz-elastic.westeurope.azurecontainer.io:9200'
    ];
    $client = \Elasticsearch\ClientBuilder::create()
        ->setHosts($hosts)
        ->build();

    function deleteIndex() {
        $delParams = [
            'index' => 'test'
        ];
        $c = $GLOBALS['client'];
        $response = $c->indices()->delete($delParams);
        echo $response;
    }

    function addDocuments() {
        $params1 = [
            'index' => 'test',
            'id'    => '1',
            'type'  => 'test_type',
            'body'  => ['name' => 'John', 'lastname' => 'Smith']
        ];
        $params2 = [
            'index' => 'test',
            'id'    => '2',
            'type'  => 'test_type',
            'body'  => ['name' => 'Ed', 'lastname' => 'Temp']
        ];

        $c = $GLOBALS['client'];
        $response1 = $c->index($params1);
        $response2 = $c->index($params2);

        echo json_encode([$response1, $response2]);
    }

    function findDocuments($search) {
        $params = [
            'index' => 'test',
            'type' => 'test_type',
            'body' => [
                'query' => [
                    'query_string' => [
                        'query' => '*' . $search . '*',
                        'fields' => ['name', 'lastname']
                    ]
                ]
            ]
        ];

        $c = $GLOBALS['client'];
        $results = $c->search($params);
        echo json_encode($results);
    }

    if($_SERVER['REQUEST_METHOD'] == "POST" and (isset($_POST['action'])))
    {
        switch ($_POST['action']){
            case "seed":
                addDocuments();
                break;
            case "search":
                $searchPhrase = '';
                if(isset($_POST['value'])) {
                    $searchPhrase = $_POST['value'];
                }
                findDocuments($searchPhrase);
                break;
        }
    }

