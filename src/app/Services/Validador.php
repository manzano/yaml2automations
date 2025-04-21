<?php

namespace Manzano\Yaml2Automations\Services;

use Symfony\Component\Yaml\Yaml;

class Validador
{
    public $confPath = __DIR__ . '/../../config';

    public function __construct()
    {
      
    }

    public function retornarSchema(): array
    {
        $schema = [
            'name' => [ 'type' => 'string', 'required' => true],
            'description' => [ 'type' => 'string', 'required' => false],
            'version' => [ 'type' => 'double', 'required' => false],
            'author' => [ 'type' => 'string', 'required' => false],
            'source' => [ 'type' => 'string', 'required' => true]
        ];
        return $schema;

    }

    // Funçao para ler o diretorio de configuracoes source e listar os arquivos com extensao .yaml ou yml
    public function validarConfiguracao($configuracao): bool
    {
        $schema = $this->retornarSchema();
        $validacao = true;

        foreach ($schema as $key => $value) {
            if ($value['required'] && !isset($configuracao[$key])) {
                echo "❌ Campo obrigatório '$key' não encontrado.\n";
                $validacao = false;
            } elseif (isset($configuracao[$key]) && gettype($configuracao[$key]) !== $value['type']) {
                echo "❌ Campo '$key' deve ser do tipo '{$value['type']}' e está como '".gettype($configuracao[$key])."'.\n";
                $validacao = false;
            }
        }

        return $validacao;
    }

}
