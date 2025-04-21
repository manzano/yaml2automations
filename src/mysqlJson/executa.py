import pymysql
import json
import random
from faker import Faker

# Configuração para conexão com o MySQL
config = {
    'host': '192.168.8.55',
    'port': 3308,
    'user': 'manzano',     # Usuário MySQL
    'password': 'manzano',   # Senha
    'database': 'mercado'
}

# Definição da tabela a ser trabalhada
tabela = 'pessoas'

# Inicializa o Faker para gerar dados fictícios
fake = Faker('pt_BR')

# Lista de possíveis tags para escolha aleatória
possiveis_tags = [
    'promoção', 'desconto', 'oferta', 'liquidação', 'novidade', 
    'destaque', 'sazonal', 'edição_limitada', 'premium', 'básico',
    'orgânico', 'natural', 'vegano', 'sem_glúten', 'diet',
    'importado', 'nacional', 'artesanal', 'industrial', 'exclusivo',
    'presente', 'combo', 'kit', 'pacote', 'unitário'
]

# Função para gerar campos adicionais aleatórios
def gerar_campos_adicionais():
    campos = {}
    
    # Define número aleatório de campos adicionais (entre 2 e 5)
    num_campos = random.randint(2, 5)
    
    # Possíveis tipos de campos adicionais
    tipos_de_campos = [
        ('preço', lambda: round(random.uniform(1.99, 299.99), 2)),
        ('quantidade', lambda: random.randint(1, 500)),
        ('categoria', lambda: random.choice(['alimentos', 'bebidas', 'limpeza', 'higiene', 'eletrônicos', 'vestuário'])),
        ('validade', lambda: fake.date_between(start_date='today', end_date='+2y').strftime("%d/%m/%Y")),
        ('fornecedor', lambda: fake.company()),
        ('avaliacao', lambda: round(random.uniform(1, 5), 1)),
        ('cor', lambda: fake.color_name()),
        ('tamanho', lambda: random.choice(['P', 'M', 'G', 'GG', 'XG'])),
        ('origem', lambda: fake.country()),
        ('lote', lambda: f"L{fake.random_number(digits=6)}")
    ]
    
    # Seleciona campos aleatórios sem repetição
    campos_selecionados = random.sample(tipos_de_campos, num_campos)
    
    # Gera valores para os campos selecionados
    for nome_campo, gerador_valor in campos_selecionados:
        campos[nome_campo] = gerador_valor()
    
    return campos

# Função para gerar nuvem de tags aleatória
def gerar_nuvem_tags():
    # Número aleatório de tags (entre 2 e 6)
    num_tags = random.randint(2, 6)
    # Escolhe tags aleatórias sem repetição
    tags = random.sample(possiveis_tags, num_tags)
    return set(tags)

# Função para gerar dados JSON para cada linha
def gerar_dados_json():
    dados = {
        'nome': fake.name(),
        'email': fake.email(),
        'tags': list(gerar_nuvem_tags()),
        'campos_adicionais': gerar_campos_adicionais()
    }
    # Converter para string JSON sem caracteres especiais problemáticos
    return json.dumps(dados, ensure_ascii=False).replace("'", "\\'")

try:
    # Estabelece conexão com o banco de dados
    conexao = pymysql.connect(**config)
    cursor = conexao.cursor()
    
    print("Conectado ao banco de dados MySQL com sucesso!")
    print(f"\nProcessando tabela: {tabela}")
    
    # Verifica se a tabela existe e a estrutura da coluna 'dados'
    cursor.execute(f"SHOW TABLES LIKE '{tabela}'")
    if not cursor.fetchone():
        print(f"A tabela {tabela} não existe no banco de dados.")
    else:
        # Verifica informações da coluna 'dados'
        cursor.execute(f"SHOW COLUMNS FROM {tabela} LIKE 'dados'")
        coluna_dados = cursor.fetchone()
        
        if coluna_dados:
            print(f"Informações da coluna 'dados': {coluna_dados}")
            
            try:
                # Verifica o tipo da coluna para garantir que pode armazenar JSON
                cursor.execute(f"SELECT DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '{tabela}' AND COLUMN_NAME = 'dados'")
                tipo_coluna = cursor.fetchone()
                print(f"Tipo da coluna 'dados': {tipo_coluna}")
            except Exception as e:
                print(f"Não foi possível verificar o tipo da coluna: {e}")
            
            # Conta o número de linhas na tabela
            cursor.execute(f"SELECT COUNT(*) FROM {tabela} where dados is null")
            num_linhas = cursor.fetchone()[0]
            print(f"Encontradas {num_linhas} linhas na tabela {tabela}")
            
            # Obtém o ID de cada linha
            cursor.execute(f"SELECT id FROM {tabela} WHERE dados is null")
            ids = cursor.fetchall()
            
            # Atualiza a coluna 'dados' com JSON gerado para cada linha
            for (id_linha,) in ids:
                try:
                    dados_json = gerar_dados_json()
                    print(f"JSON gerado para linha {id_linha}: {dados_json[:50]}...") # Mostra os primeiros 50 caracteres
                    
                    # Atualiza a linha com o JSON gerado
                    cursor.execute(
                        f"UPDATE {tabela} SET dados = %s WHERE id = %s", 
                        (dados_json, id_linha)
                    )
                    
                    # Confirma a alteração imediatamente
                    conexao.commit()
                    
                    # Verifica se a atualização funcionou
                    cursor.execute(f"SELECT dados FROM {tabela} WHERE id = %s", (id_linha,))
                    dados_verificacao = cursor.fetchone()
                    
                    if dados_verificacao and dados_verificacao[0]:
                        print(f"--> Linha {id_linha} atualizada com sucesso")
                    else:
                        print(f"--> PROBLEMA: Linha {id_linha} não foi atualizada corretamente")
                        
                except Exception as e:
                    print(f"Erro ao atualizar linha {id_linha}: {e}")
            
            print(f"Processamento de {len(ids)} linhas na tabela {tabela} concluído")
        else:
            print(f"A tabela {tabela} não possui uma coluna 'dados'.")

except pymysql.Error as erro:
    print(f"Erro ao conectar ou processar o banco de dados: {erro}")

finally:
    # Fecha a conexão
    if 'conexao' in locals() and 'cursor' in locals():
        cursor.close()
        conexao.close()
        print("\nConexão com o MySQL encerrada.")