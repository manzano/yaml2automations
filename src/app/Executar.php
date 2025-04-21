<?php

namespace Manzano\Yaml2Automations;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use Manzano\Yaml2Automations\Services\Configuracoes;
use Manzano\Yaml2Automations\Services\Validador;
use Manzano\Yaml2Automations\Services\Fonte;


#[AsCommand(
    name: 'executar',
    description: 'Executando o Yaml2Automations',
    hidden: false,
    aliases: ['Executar', 'execute', 'Execute']
)]
class Executar extends Command
{
    protected static $defaultName = 'executar';
    protected $logObjeto = false;
    protected InputInterface $input;
    protected OutputInterface $output;
    /**
     * @var string[]
     */
    public array $variaveisAmbiente = [];
    public bool $voltarProMenu = false;
    protected $configSelecionada = false;
    protected $configDetalhada = false;
    protected $configObj;
    protected $inputConfig;
    const OPCAO_SAIR = 'Sair (CTRL+C)';

    protected function configure()
    {
        $this->setName('executar')
            ->setDescription('Executar o Yaml2Automations')
            ->addArgument('config', InputArgument::OPTIONAL, 'Qual configuracao deseja executar');
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->input = $input;
        $this->output = $output;

        // Verifica se o comando foi chamado com o argumento 'config'
        if ($input->getArgument('config')) {
            $this->configSelecionada = $input->getArgument('config');
        }

        return $this->executarYaml2Automations($input, $output);
    }

    protected function executarYaml2Automations(InputInterface $input, OutputInterface $output): int
    {

        $io = new SymfonyStyle($input, $output);
        $io->title('Executando o Yaml2Automations');

        $this->configObj = new Configuracoes();

        $versaoY2A = $this->configObj->retornarVersao();
        $this->output->writeln('Versão: ' . $versaoY2A);

        if ($this->configSelecionada) {
            $this->configDetalhada = $this->configObj->retornarConfiguracao($this->configSelecionada);
            if (empty($this->configDetalhada)) {
                $io->error('Arquivo de configuração não encontrado.');
                return Command::FAILURE;
            }
            $this->processaConfiguracao($this->configDetalhada);
            return Command::SUCCESS;
        } else {
            $configuracoes = $this->configObj->retornarConfiguracoes();
            if (empty($configuracoes)) {
                $io->error('Nenhum arquivo de configuração encontrado.');
                return Command::FAILURE;
            }
        }
        $io->text('Arquivos de configuração disponíveis:');
        $opcoes = [];
        foreach ($configuracoes as $config) {
            $opcoes[] = $config;
        }
        $opcoes[] = $this::OPCAO_SAIR;
        $io->text('Escolha um arquivo de configuração:');
        $this->configSelecionada = $io->choice('Arquivos de configuração disponíveis:', $opcoes);
        if ($this->configSelecionada === $this::OPCAO_SAIR) {
            $io->text(['Até mais!', '']);
            return Command::SUCCESS;
        }

        $io->text(['Você escolheu: ' . $this->configSelecionada, '']);
        $this->configDetalhada = $this->configObj->retornarConfiguracao($this->configSelecionada);

        $this->voltarProMenu = true;

        return Command::SUCCESS;
    }

    public function processaConfiguracao($configuracao)
    {

        echo PHP_EOL;
        echo 'Validando arquivo de condiguracao: '.$this->configSelecionada.PHP_EOL;
        $validadosObj = new Validador();
        $validado = $validadosObj->validarConfiguracao($configuracao);
        if (!$validado) {
            $this->output->writeln('<error>Erro ao validar a configuração.</error>');
            return Command::FAILURE;
        }
        $this->output->writeln('<info>✅ Configuração válida!</info>');

        echo PHP_EOL;
        echo 'Iniciando execução da configuração: '.$configuracao['name'].PHP_EOL;
        echo PHP_EOL;

        // Validar se source contem http ou https
        if (isset($configuracao['source']) && (strpos($configuracao['source'], 'http://') !== false || strpos($configuracao['source'], 'https://') !== false)) {
            $this->output->writeln('<info>- Souce é um endereço: '.$configuracao['source'].'</info>');
            $this->output->writeln('<info>Fazendo download do arquivo de configuração...</info>');
            $nomeArquivoFonte = $this->configObj->downloadConfiguracao($configuracao['source']);
            $this->output->writeln('<info>✅ Download concluído!</info>');
        } else {
            $this->output->writeln('<info>- Souce é um arquivo: '.$configuracao['source'].'</info>');
            $nomeArquivoFonte = $configuracao['source'];
        }

        $this->output->writeln('<info>Fazendo leitura do arquivo de configuração...</info>');
        $fonteObj = new Fonte();
        $dadosFonte = $fonteObj->retornarFonte($nomeArquivoFonte);


        echo PHP_EOL;
        echo "Vamos higienizar os dados do YAML baixado...\n";
        echo "Removendo openapi, info, servers...\n";
        $dadosFonte = $fonteObj->higienizarDados($dadosFonte);
        $this->output->writeln('<info>✅ Higienização concluída!</info>');


        echo PHP_EOL;
        echo "Higianizando paths e removendo tags, description, operationId, summary, parameters, requestBody...\n";
        $dadosFonte = $fonteObj->higienizarPaths($dadosFonte);
        $this->output->writeln('<info>✅ Varrendo paths concluído!</info>');

        echo PHP_EOL;
        echo "Tratando os \$refs dos schemas...\n";
        $dadosFonte = $fonteObj->tratarRefs($dadosFonte);
        $this->output->writeln('<info>✅ $refs tratados com sucesso!</info>');



        print_r($dadosFonte);

        return [];
    }

    protected function voltarProMenu($io)
    {
        if ($this->voltarProMenu) {
            if ($io->confirm('Vamos voltar pro menu anterior?', true)) {
                $this->limparTela();
                return $this->execute($this->input, $this->output);
            } else {
                return 0;
            }
        }
    }
    protected function limparTela(): void
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // Limpa a tela no Windows
            system('cls');
        } else {
            // Limpa a tela em sistemas Unix-like
            system('clear');
        }
    }

}
