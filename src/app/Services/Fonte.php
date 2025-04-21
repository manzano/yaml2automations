<?php

namespace Manzano\Yaml2Automations\Services;

use Symfony\Component\Yaml\Yaml;

class Fonte
{
    public $sourcePath = __DIR__ . '/../../source';

    public function retornarFonte($arquivo): array
    {
        $arquivoPath = $this->sourcePath . '/' . $arquivo;
        if (file_exists($arquivoPath)) {
            try {
                $yamlArray = Yaml::parseFile($arquivoPath);
            } catch (Exception $e) {
                echo '❌ Erro ao ler o arquivo YAML: ',  $e->getMessage(), "\n";
            }
            return $yamlArray;
        }
        return [];
    }


    public function higienizarDados($dados): array
    {
        unset($dados['openapi']);
        unset($dados['info']);
        unset($dados['servers']);
        unset($dados['tags']);
        return $dados;
    }

    public function higienizarPaths($dados): array
    {
        foreach ($dados['paths'] as $path => $methods) {
            foreach ($methods as $method => $props) {
                unset($dados['paths'][$path][$method]['tags']);
                unset($dados['paths'][$path][$method]['description']);
                unset($dados['paths'][$path][$method]['operationId']);
                unset($dados['paths'][$path][$method]['summary']);
                unset($dados['paths'][$path][$method]['parameters']);
                unset($dados['paths'][$path][$method]['requestBody']);
                unset($dados['paths'][$path]['description']);
                unset($dados['paths'][$path]['content']['application/json']['examples']);
                $dados['paths'][$path] = $dados['paths'][$path][$method]['responses']['200']['content']['application/json']['schema'];
            }
        }
        return $dados;
    }

    public function tratarRefs($dados): array
    {
        foreach ($dados['paths'] as $path => $dados_paths) {
            foreach ($dados_paths['allOf'] as $key => $value) {
                if (isset($value['$ref'])) {
                    $ref = explode('/', ltrim($value['$ref'], '#/'));
                    $dados['paths'][$path] = array_merge($dados['paths'][$path], $dados['components']['schemas'][$ref[2]]['properties']);
                }
            }
            unset($dados['paths'][$path]['allOf']);
            unset($dados['paths'][$path]['dados']['example']);
        }

        foreach ($dados['paths'] as $path => $dados_paths) {
            if (isset($dados_paths['dados']['items']['$ref'])) {
                $ref = explode('/', ltrim($dados_paths['dados']['items']['$ref'], '#/'));
                $dados['paths'][$path]['dados'] = $dados['components']['schemas'][$ref[2]]['properties'];
            }
            if (isset($dados_paths['dados']['$ref'])) {
                $ref = explode('/', ltrim($dados_paths['dados']['$ref'], '#/'));
                $dados['paths'][$path]['dados'] = $dados['components']['schemas'][$ref[2]]['items']['properties'];
            }
        }

        return $dados;
    }

}
