<?php
[
    "name" => "CV Portal do Cliente - MCP Server",
    "nodes" => [
        [
            "parameters" => ["path" => "ec38de88-51c0-4754-99e5-39b51b4af485"],
            "type" => "@n8n/n8n-nodes-langchain.mcpTrigger",
            "typeVersion" => 1,
            "position" => [0, 0],
            "id" => "9e90c038-e496-45d0-87d4-68916b0ef074",
            "name" => "MCP Server Trigger",
            "webhookId" => "ec38de88-51c0-4754-99e5-39b51b4af485",
        ],
        [
            "parameters" => [
                "toolDescription" => "Faça buscas nos leads do CV CRM",
                "url" => "=https://{subdominio}.cvcrm.com.br/api/cvio/lead",
                "sendQuery" => true,
                "parametersQuery" => [
                    "values" => [
                        [
                            "name" => "idlead",
                            "valueProvider" => "modelOptional",
                        ],
                        ["name" => "email", "valueProvider" => "modelOptional"],
                        [
                            "name" => "telefone",
                            "valueProvider" => "modelOptional",
                        ],
                        [
                            "name" => "idcorretor",
                            "valueProvider" => "modelOptional",
                        ],
                    ],
                ],
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
        ],
        [
            "parameters" => [
                "toolDescription" =>
                    "Faça buscas nas reservas e propostas do CV CRM. Utilize o id/numero da reserva/proposta em {id}",
                "url" => '=https://{subdominio}.cvcrm.com.br/api/cvio/reserva/{id}
',
                "sendQuery" => true,
                "parametersQuery" => ["values" => [[]]],
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
            "position" => [380, 280],
            "id" => "c17f3330-a7c9-41e5-a9f7-bb525e5e3f29",
            "name" => "Retorna reserva",
        ],
        [
            "parameters" => [
                "toolDescription" =>
                    "Retorna dados do empreendimento cadastrado no CV CRM. Utilize o id/numero da reserva/proposta em {idEmpreendimento}",
                "url" => '=https://{subdominio}.cvcrm.com.br/api/cvio/empreendimento/{idEmpreendimento}
',
                "sendQuery" => true,
                "parametersQuery" => ["values" => [[]]],
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
            "position" => [580, 260],
            "id" => "ef4311bb-4822-42eb-bec5-4db8a4cc27e4",
            "name" => "Retorna empreendimento",
        ],
        [
            "parameters" => [
                "toolDescription" =>
                    "Retorna dados do empreendimento cadastrado no CV CRM. Utilize o id/numero da reserva/proposta em {idEmpreendimento}",
                "method" => "POST",
                "url" => '=https://{subdominio}.cvcrm.com.br/api/cvio/empreendimento/{idEmpreendimento}
',
                "sendQuery" => true,
                "parametersQuery" => ["values" => [[]]],
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
                "sendBody" => true,
                "parametersBody" => ["values" => [["name" => "variavel"]]],
            ],
            "type" => "@n8n/n8n-nodes-langchain.toolHttpRequest",
            "typeVersion" => 1.1,
            "position" => [760, 260],
            "id" => "708a88c8-2547-4f30-9cd9-48d5fcf1efd5",
            "name" => "DEMO TOOL",
        ],
        [
            "parameters" => [
                "toolDescription" =>
                    "Retorna dados do empreendimento cadastrado no CV CRM. Utilize o id/numero da reserva/proposta em {idEmpreendimento}",
                "method" => "POST",
                "url" => '=https://{subdominio}.cvcrm.com.br/api/cvio/empreendimento/{idEmpreendimento}
',
                "sendQuery" => true,
                "parametersQuery" => ["values" => [[]]],
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
                "sendBody" => true,
                "specifyBody" => "json",
                "jsonBody" => '{
"parametro": "{valor}",
"parametro2": "{valor}"
}',
                "optimizeResponse" => true,
            ],
            "type" => "@n8n/n8n-nodes-langchain.toolHttpRequest",
            "typeVersion" => 1.1,
            "position" => [420, -20],
            "id" => "ed4cde76-fe66-4914-b19f-35320f3222c0",
            "name" => "DEMO TOOL JSON BODY",
        ],
    ],
    "pinData" => [],
    "connections" => [
        "Retorna leads" => [
            "ai_tool" => [
                [
                    [
                        "node" => "MCP Server Trigger",
                        "type" => "ai_tool",
                        "index" => 0,
                    ],
                ],
            ],
        ],
        "Retorna reserva" => [
            "ai_tool" => [
                [
                    [
                        "node" => "MCP Server Trigger",
                        "type" => "ai_tool",
                        "index" => 0,
                    ],
                ],
            ],
        ],
        "Retorna empreendimento" => [
            "ai_tool" => [
                [
                    [
                        "node" => "MCP Server Trigger",
                        "type" => "ai_tool",
                        "index" => 0,
                    ],
                ],
            ],
        ],
        "DEMO TOOL" => [
            "ai_tool" => [
                [
                    [
                        "node" => "MCP Server Trigger",
                        "type" => "ai_tool",
                        "index" => 0,
                    ],
                ],
            ],
        ],
        "DEMO TOOL JSON BODY" => [
            "ai_tool" => [
                [
                    [
                        "node" => "MCP Server Trigger",
                        "type" => "ai_tool",
                        "index" => 0,
                    ],
                ],
            ],
        ],
    ],
    "active" => false,
    "settings" => ["executionOrder" => "v1"],
    "versionId" => "9c8f20a6-25cb-4e16-bc57-fa0b90636679",
    "meta" => [
        "instanceId" =>
            "a24c8a02c6b8dd99614e179358a27bafbcfafa7e5af0bffc8eb0455cb14fd18f",
    ],
    "id" => "LiaDHugTU31s7ZkK",
    "tags" => [],
];
