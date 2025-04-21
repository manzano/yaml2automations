<?php

// incluido o autoload do composer
require_once __DIR__ . '/../../vendor/autoload.php';
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

// Incluindo o php template/n8n.php
require_once __DIR__ . '/template/n8n.php';

// gerando um UUID
$uuid = bin2hex(random_bytes(16));

// Listando arquivos .yaml da pasta yaml-files
$yamlFiles = glob(__DIR__ . '/yaml-files/*.yaml');
// Ordenar em ordem alfabetica
sort($yamlFiles);

// Verificando se existem arquivos .yaml
if (empty($yamlFiles)) {
    echo "Nenhum arquivo .yaml encontrado na pasta yaml-files.\n";
    exit;
}

$escopo = [
    '/pessoa',
];


foreach($yamlFiles as $yamlFile) {

    echo "Processando arquivo: $yamlFile".PHP_EOL;
    echo "-------------------------------------".PHP_EOL;

    $parametrosArray = [];
    $conexoesArray = [];
    // Lendo o arquivo .yaml
    try {
        $data = Yaml::parseFile($yamlFile);
        $fileName = basename($yamlFile);

        //servers:
        //- description: Ambiente de Integração
        //  url: https://{dominiodocliente}.cvcrm.com.br/api/v1/cliente
        $n8nUrl = $data['servers'][0]['url'] ?? null;

        // Lindo os paths do yaml
        $paths = $data['paths'] ?? null;
        if ($paths === null) {
            echo "Nenhum path encontrado no arquivo: $yamlFile\n";
            continue;
        }
        $posicaoX = 200;
        $posicaoY = 0;
        $posicaoXIncremento = 250;
        $posicaoYIncremento = 0;
        $ultimoPath = '';
        foreach ($paths as $path => $value) {
            // Verificando se o path é um array
            if (is_array($value)) {

                // Verificando se o path deve ser ignorado
                if(!in_array($path, $escopo)) {
                    //echo "Ignorando o path: $path\n";
                    continue;
                }

                // Imprimindo os métodos
                foreach ($value as $method => $details) {


                    // Pegando a primeira parte do path
                    $pathArray = explode('/', $path);
                    $pathArray = array_filter($pathArray);
                    $pathArray = array_values($pathArray);
                    $path = $pathArray[0];
                    if($path !== $ultimoPath) {
                        $posicaoX = 0;
                        $posicaoY += 200;
                        $ultimoPath = $path;

                        $uuid = bin2hex(random_bytes(16));
                        $parametrosArrayAux = [
                            'parameters' => [
                                'content' => "## $path",
                                'color' => 5,
                            ],
                            'type' => 'n8n-nodes-base.stickyNote',
                            'typeVersion' => 1,
                            'position' => [-100, $posicaoY],
                            'id' => $uuid,
                            'name' => "Sticky Note do path $path",
                        ];
                        $n8nArray['nodes'][] = $parametrosArrayAux;
                    }
                    
                    $posicaoX += $posicaoXIncremento;
                    $posicaoY += $posicaoYIncremento;

                    echo "'$path', // $method - ".$details['summary'];

                    $parametrosArrayAux = $n8nParametros;
                    $parametrosArrayAux['position'] = [$posicaoX, $posicaoY];
                    $uuid = bin2hex(random_bytes(16));
                    $parametrosArrayAux['id'] = $uuid;
                    $parametrosArrayAux['name'] = $details['summary'] ?? 'N/A';

                    $parametrosArrayAux['parameters']['toolDescription'] = $details['description'] ?? $details['summary'];
                    $parametrosArrayAux['parameters']['url'] = '='. $n8nUrl . "/" . $path;
                    
                    // adicionando o metodo
                    $parametrosArrayAux['parameters']['method'] = strtoupper($method);

                    // Se precisa enviar GET
                    $parametrosArrayAux = processaGet($parametrosArrayAux, $details);
                    // Se precisa enviar headers
                    $parametrosArrayAux = processaHeaders($parametrosArrayAux, $details);
                    // Se precisa enviar body
                    $parametrosArrayAux = processaPost($parametrosArrayAux, $details);
                    

                    $n8nArray['nodes'][] = $parametrosArrayAux;
                    unset($parametrosArrayAux);

                    $conexoesArray[$details['summary']] = $n8nConexoes;
                    unset($arrayYamlAux);
                }
            } else {
                echo " | Valor: $value";
            }

            echo PHP_EOL;
        }

        // Adicionando os parâmetros e conexões ao array
        $n8nArray['connections'] = array_merge($n8nArray['connections'], $conexoesArray);

        // Salvando os dados em um arquivo JSON
        $jsonFile = __DIR__ . '/n8n/'.$fileName.'.json';
        // Se nao existir o arquivo, criamos
        if (!file_exists($jsonFile)) {
            $fp = fopen($jsonFile, 'w');
            if ($fp === false) {
                echo "Erro ao criar o arquivo JSON: $jsonFile\n";
                exit;
            }
            fclose($fp);
        }
        $fp = fopen($jsonFile, 'w');
        if ($fp === false) {
            echo "Erro ao criar o arquivo Yaml: $jsonFile\n";
            exit;
        }
        
        // salvando $n8nArray convertido para json em $fp
        // nao colocar o \ antes do /
        $jsonData = json_encode($n8nArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if ($jsonData === false) {
            echo "Erro ao converter os dados para JSON: " . json_last_error_msg() . "\n";
            fclose($fp);
            exit;
        }
        // Escrevendo os dados no arquivo
        $result = fwrite($fp, $jsonData);
        if ($result === false) {
            echo "Erro ao escrever no arquivo JSON: $jsonFile\n";
            fclose($fp);
            exit;
        }

        fclose($fp);
        echo "Arquivo CSV criado com sucesso: $jsonFile\n";

    } catch (ParseException $e) {
        echo "Erro ao ler o arquivo YAML: ", $e->getMessage();
        exit;
        continue;
    }

    // Verificando se o arquivo foi lido corretamente
    if (empty($data)) {
        echo "Arquivo YAML vazio ou inválido: $yamlFile\n";
        continue;
    }

}

function processaGet($parametrosArrayAux, $details) {
    // Se precisa enviar GET
    if (isset($details['parameters'])) {
        $parametrosArrayAux['parameters']['sendQuery'] = true;
        $parametrosArrayAux['parameters']['parametersQuery'] = [
            'values' => [
                [
                    'name' => 'idlead',
                    'valueProvider' => 'modelOptional',
                ]
            ],
        ];
    }
    return $parametrosArrayAux;
}

function processaHeaders($parametrosArrayAux, $details) {
    // Se precisa enviar headers
    if (isset($details['parameters'])) {
        $parametrosArrayAux['parameters']['sendHeaders'] = true;
        $parametrosArrayAux['parameters']['parametersHeaders'] = [
            'values' => [
                [
                    'name' => 'email',
                    'valueProvider' => 'fieldValue',
                    'value' => '{header_email}',
                ],
                [
                    'name' => 'token',
                    'valueProvider' => 'fieldValue',
                    'value' => '{header_token}',
                ],
            ],
        ];
    }
    return $parametrosArrayAux;
}


/*
                "specifyBody" => "json",
                "jsonBody" => '{
"parametro": "{valor}",
"parametro2": "{valor}"
}',
                "optimizeResponse" => true,
*/
function processaPost($parametrosArrayAux, $details) {
    // Se precisa enviar body
    if (isset($details['requestBody'])) {
        $parametrosArrayAux['parameters']['sendBody'] = true;
        $parametrosArrayAux['parameters']['parametersBody'] = [
            'values' => [
                [
                    'name' => 'idlead',
                    'valueProvider' => 'modelOptional',
                ]
            ],
        ];
    }
    return $parametrosArrayAux;
}
