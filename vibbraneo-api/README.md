
# E-Commerce Backend

Este é o backend de um sistema de E-Commerce, que fornece as APIs necessárias para o funcionamento do front-end, além de gerenciar a lógica do chat.

## Tecnologias Utilizadas

- **Java**: Linguagem de programação utilizada para o desenvolvimento do backend.
- **Spring Boot**: Framework para construção de aplicações Java de forma rápida e produtiva.
- **Spring WebSocket**: Para gerenciamento da comunicação em tempo real via WebSocket.
- **Spring Data JPA**: Para gerenciamento de persistência de dados.
- **H2 Database**: Banco de dados em memória para desenvolvimento e testes.

## Funcionalidades

- **APIs REST**: Fornece endpoints para cadastro e listagem de produtos.
- **WebSocket**: Permite comunicação em tempo real entre usuários e vendedores através do chat.
- **Validação de Dados**: Garantia de que os dados inseridos sejam válidos antes de serem persistidos.

## Como Executar a Aplicação

1. Clone o repositório.
2. Navegue até o diretório do backend.
3. Execute a aplicação:
   ```bash
   mvn spring-boot:run
   ```

A API estará disponível em `http://localhost:8080`.

