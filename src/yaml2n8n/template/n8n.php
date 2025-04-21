<?php

$n8nArray = [
    'name' => 'CV Portal do Cliente - MCP Server',
    'nodes' => [
        [
            'parameters' => ['path' => 'portaldocliente'],
            'type' => '@n8n/n8n-nodes-langchain.mcpTrigger',
            'typeVersion' => 1,
            'position' => [0, 0],
            'id' => '9e90c038-e496-45d0-87d4-68916b0ef074',
            'name' => 'MCP Server Trigger',
            'webhookId' => 'portaldocliente',
        ]
    ],
    'pinData' => [],
    'connections' => [
        
    ],
    'active' => false,
    'settings' => ['executionOrder' => 'v1'],
    'versionId' => '9c8f20a6-25cb-4e16-bc57-fa0b90636679',
    'meta' => ['instanceId' => 'a24c8a02c6b8dd99614e179358a27bafbcfafa7e5af0bffc8eb0455cb14fd18f'],
    'id' => 'LiaDHugTU31s7ZkK',
    'tags' => [],
];

$n8nParametros = [
    "parameters" => [
        "toolDescription" => "",
        "url" => "=",
        "sendHeaders" => true,
        "parametersHeaders" => [
            "values" => [
                [
                    "name" => "email",
                    "valueProvider" => "fieldValue",
                    "value" => "{header_email}",
                ],
                [
                    "name" => "token",
                    "valueProvider" => "fieldValue",
                    "value" => "{header_token}",
                ],
            ],
        ],
    ],
    "type" => "@n8n/n8n-nodes-langchain.toolHttpRequest",
    "typeVersion" => 1.1,
    "position" => [180, 280],
    "id" => "8e0009bc-0dd1-4884-9ef7-229b83adecac",
    "name" => "Retorna leads",
];

$n8nConexoes = [
        "ai_tool" => [
            [
                [
                    "node" => "MCP Server Trigger",
                    "type" => "ai_tool",
                    "index" => 0,
                ],
            ],
    ]
];
