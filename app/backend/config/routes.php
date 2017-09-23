<?php
return [
    'GET' => [
        '/' => [
            'controller' => 'Test\\Counters\\Controller\\ApiController',
            'action' => 'hello',
            'response_formats' => ['json']
        ],
        'summary' => [
            'controller' => 'Test\\Counters\\Controller\\ApiController',
            'action' => 'summary',
            'response_formats' => ['json', 'csv']
        ]
    ],
    'POST' => [
        'counters' => [
            'controller' => 'Test\\Counters\\Controller\\ApiController',
            'action' => 'counters',
            'response_formats' => ['json']
        ]
    ]
];