# Desafio_Php

O Desafio_Php é uma plataforma de pagamentos simplificada onde é possível depositar e realizar transferências de dinheiro entre usuários comuns e lojistas.

## Funcionalidades

### Cadastrar Usuário Comum

Permite cadastrar um usuário comum.

- **URL**: `/usuario/cadastrar`
- **Método**: `POST`
- **Body**:

```json
{
    "name": "Ze das cove",
    "email": "zezinho@123.com",
    "cpf_cnpj": "432.889.666-66",
    "password": "senha123",
    "type": "1"
}
#obs type 1 pra usuário comum e type 2 para lojista

- **Transferência de Dinheiro**:
- **URL**: `/transfer`
- **Método**: `POST`
- **Autenticação**: Requer autenticação JWT
- **Body**:

```json
{
  "value": 100.0,
  "payer": 4,
  "payee": 15
}

Documentação da API
Retorna a documentação interativa da API.

URL: `/api/docs`
Método: `GET`
Rotas Protegidas
As rotas a seguir requerem autenticação JWT.

- `/home`: Página inicial protegida do usuário.

Testes Unitários
Os testes unitários estão disponíveis para garantir a integridade das funcionalidades. Eles incluem:

Testes de Transferência: Testes para garantir que a transferência de dinheiro funcione corretamente.
Migrações do Banco de Dados
As migrações do banco de dados são usadas para criar a estrutura do banco de dados necessária para o funcionamento do sistema.

## Configurações

- **Banco de Dados**:
  - Arquivo `.env` contendo as configurações de acesso ao banco de dados.

- **Docker** (Opcional):
  - `Dockerfile` e `docker-compose.yml` para configurar o ambiente Dockerizado.

## Testes

- **Testes Unitários**:
  - Testes para verificar a funcionalidade de transferência de dinheiro.

## PHPStan
- O PHPStan é usado para garantir a qualidade do código através de análises estáticas. Certifique-se de executar o PHPStan regularmente para detectar possíveis erros no código.

Para executar o PHPStan, use o seguinte comando:
./vendor/bin/phpstan analyse

## Instalação e Uso

1. Clone o repositório:

   ```bash
   git clone https://github.com/DarksideXx/Desafio_Php.git

2. Instale as dependências do Composer:
composer install

3. Configure o arquivo .env com as credenciais do banco de dados.

4. Execute as migrações do banco de dados:
php artisan migrate

5. Inicie o servidor:
php artisan serve

Documentação da API
Para acessar a documentação interativa da API, acesse:
http://localhost:8000/api/docs

Docker (Opcional)
Se preferir, você pode usar Docker para executar o projeto:

1. Certifique-se de ter o Docker instalado.

2. Execute o seguinte comando na raiz do projeto:
docker-compose up --build