# 🛒 E-commerce com Chat em Tempo Real

Esta aplicação é um sistema de e-commerce onde os usuários podem cadastrar produtos à venda, listar produtos disponíveis e entrar em contato com o vendedor através de um **chat em tempo real** utilizando WebSocket.

## 📜 Funcionalidades

- **Cadastro de Produtos**: Vendedores podem cadastrar produtos para venda.
- **Listagem de Produtos**: Usuários podem visualizar a lista de produtos disponíveis para compra.
- **Chat com o Vendedor**: O usuário pode se comunicar diretamente com o vendedor através de um chat em tempo real, facilitado pelo uso de WebSockets.
- **Envio e recebimento de mensagens instantâneas**: Mensagens trocadas em tempo real entre compradores e vendedores.
  
## 🖥️ Tecnologias Utilizadas

### Frontend:
- **React**: Biblioteca JavaScript para construção de interfaces de usuário dinâmicas.
- **Material-UI (MUI)**: Biblioteca de componentes de interface de usuário para React, oferecendo uma experiência visual moderna e amigável.
- **STOMP.js**: Biblioteca cliente para STOMP, usada para facilitar a comunicação via WebSocket com o backend.
- **SockJS**: Biblioteca que fornece um fallback para WebSocket, garantindo compatibilidade com todos os navegadores.

### Backend:
- **Spring Boot**: Framework para desenvolvimento rápido de aplicações Java, utilizado para o backend da aplicação.
- **Spring WebSocket**: Extensão do Spring para suportar o protocolo WebSocket, utilizado para a comunicação em tempo real entre cliente e servidor.
- **Spring Data JPA**: Abstração do JPA (Java Persistence API) para operações de banco de dados, facilitando o gerenciamento de produtos e usuários.
- **Lombok**: Biblioteca que reduz a verbosidade do código Java, gerando automaticamente métodos getters, setters, construtores e muito mais.
- **Jackson**: Biblioteca para conversão de objetos Java para JSON e vice-versa, utilizada para serializar e desserializar as mensagens trocadas via WebSocket.

---

## 🚀 Como rodar a aplicação

### Pré-requisitos
- **Node.js** e **npm** instalados (para o frontend)
- **Java 17** ou superior (para o backend)
- **Maven** instalado (para gerenciamento de dependências do backend)

### Passo 1: Rodando o Backend

1. Clone o repositório:
   ```bash
   git clone https://github.com/seu-repositorio/ecommerce-chat-websocket.git
   ```

2. Entre no diretório do projeto backend:
   ```bash
   cd ecommerce-chat-websocket/backend
   ```

3. Compile e execute o backend usando Maven:
   ```bash
   mvn clean install
   mvn spring-boot:run
   ```

4. O backend estará rodando na porta `8080` e pronto para aceitar conexões WebSocket, além de expor os endpoints REST para cadastro e listagem de produtos.

### Passo 2: Rodando o Frontend

1. Entre no diretório do frontend:
   ```bash
   cd ../frontend
   ```

2. Instale as dependências:
   ```bash
   npm install
   ```

3. Execute o frontend:
   ```bash
   npm start
   ```

4. O frontend estará rodando na porta `3000`. Abra seu navegador e acesse `http://localhost:3000` para visualizar a aplicação.

---

## 📦 Estrutura do Código

### Backend (Spring Boot)
- **`ProdutoController.java`**: Controlador REST que gerencia as operações de cadastro e listagem de produtos.
- **`ChatController.java`**: Controlador WebSocket que gerencia a troca de mensagens entre compradores e vendedores.
- **`WebSocketConfig.java`**: Configura os endpoints WebSocket e define o broker de mensagens para a comunicação em tempo real.
- **`Mensagem.java`**: Classe modelo que representa a mensagem enviada no chat.
- **`Produto.java`**: Classe modelo que representa o produto cadastrado no sistema.

### Frontend (React)
- **`ChatComponent.tsx`**: Componente React responsável pelo chat, onde os usuários podem enviar e receber mensagens em tempo real via WebSocket.
- **`ProdutoComponent.tsx`**: Componente responsável pela exibição dos produtos disponíveis.
- **`stompjs` e `sockjs-client`**: Bibliotecas que realizam a comunicação WebSocket com o backend.
- **`Material-UI`**: Componentes estilizados e prontos para uso, como botões, cards, caixas de texto, entre outros.

---

## 📝 Endpoints

### Backend

#### API REST (Produtos):
- `POST /produtos`: Cadastra um novo produto.
- `GET /produtos`: Lista todos os produtos disponíveis.

#### WebSocket (Chat):
- `ws://localhost:8080/chatmessage/websocket`: Endpoint WebSocket para troca de mensagens.
- **Tópico**: `/chat` — utilizado para enviar e receber mensagens entre compradores e vendedores.
- **Destinação da Mensagem**: `/app/chatmessage` — ponto de entrada para enviar mensagens ao WebSocket.

---

## 📦 Exemplo de Configuração WebSocket no Backend

```java
@Configuration
@EnableWebSocketMessageBroker
public class WebSocketConfig implements WebSocketMessageBrokerConfigurer {

    @Override
    public void configureMessageBroker(MessageBrokerRegistry registry) {
        registry.enableSimpleBroker("/chat"); // Tópico para envio de mensagens
        registry.setApplicationDestinationPrefixes("/app"); // Prefixo para a destinação de mensagens
    }

    @Override
    public void registerStompEndpoints(StompEndpointRegistry registry) {
        registry.addEndpoint("/chatmessage").setAllowedOriginPatterns("*").withSockJS(); // Endereço do WebSocket
    }
}
```

---

## 🛠️ Melhorias Futuras

- **Autenticação de usuários**: Implementar autenticação e autorização para o sistema de chat.
- **Suporte a múltiplas salas de chat**: Possibilitar a criação de diferentes salas para diferentes vendedores e compradores.
- **Histórico de mensagens**: Persistir as mensagens trocadas no chat em um banco de dados.
- **Notificações de novas mensagens**: Notificar o usuário quando uma nova mensagem chegar.

---

## 🛠️ Como Contribuir

1. Fork este repositório
2. Crie uma branch: `git checkout -b feature/nova-funcionalidade`
3. Envie suas modificações: `git commit -m 'Adiciona nova funcionalidade'`
4. Faça um push para a branch: `git push origin feature/nova-funcionalidade`
5. Envie um Pull Request

---

Feito com 💙 por [Seu Nome]!
