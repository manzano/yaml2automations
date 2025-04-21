<?php

namespace Manzano\Yaml2Automations\Services;

use Symfony\Component\Yaml\Yaml;

class Configuracoes
{
    public $env;
    private $parent;
    public $confPath = __DIR__ . '/../../config';
    public $sourcePath = __DIR__ . '/../../source';

    public function __construct($env = null, $parent = null)
    {
        $this->env = $env;
        $this->parent = $parent;
    }

    public function retornarVersao(): string
    {
        return 'v1.0.0';
    }

    // Funçao para ler o diretorio de configuracoes source e listar os arquivos com extensao .yaml ou yml
    public function retornarConfiguracoes(): array
    {
        $arquivos = [];
        $diretorio = $this->confPath;
        if (is_dir($diretorio)) {
            $itens = scandir($diretorio);
            foreach ($itens as $item) {
                if (pathinfo($item, PATHINFO_EXTENSION) === 'yaml' || pathinfo($item, PATHINFO_EXTENSION) === 'yml') {
                    $arquivos[] = $item;
                }
            }
        }
        return $arquivos;
    }

    public function retornarConfiguracao($arquivo): array
    {
        $arquivoPath = $this->confPath . '/' . $arquivo;
        if (file_exists($arquivoPath)) {
            try {
                $yamlArray = Yaml::parseFile($arquivoPath);
                if (isset($yamlArray['configuracoes'])) {
                    $yamlArray = $yamlArray['configuracoes'];
                }
            } catch (Exception $e) {
                echo 'Erro ao ler o arquivo YAML: ',  $e->getMessage(), "\n";
            }
            return $yamlArray;
        }
        return [];
    }

    public function downloadConfiguracao($url): string
    {
        // fazer o download do url com Curl e salvar o arquivo no diretorio source
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $conteudo = curl_exec($ch);
        $erro = curl_error($ch);
        $codigo = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($codigo == 200) {
            $nomeArquivo = basename($url);
            $caminhoArquivo = $this->sourcePath . '/' . $nomeArquivo;
            file_put_contents($caminhoArquivo, $conteudo);
            return $nomeArquivo;
        } else {
            echo 'Erro ao baixar o arquivo: ',  $erro, "\n";
            return false;
        }
    }
}
