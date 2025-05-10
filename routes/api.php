<?php

return [
    'v1/ping' => function () {
        header('Content-Type: application/json');
        echo json_encode(['message' => 'pong']);
    },

    'v1/versie' => function () {
        header('Content-Type: application/json');
        echo json_encode(['version' => '0.1-alpha']);
    },
];


