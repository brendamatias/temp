<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API de Notificações - Documentação</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.24.1/themes/prism.min.css" rel="stylesheet">
    <style>
        .endpoint {
            border-left: 4px solid #007bff;
            padding-left: 15px;
            margin-bottom: 30px;
        }
        .method {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            color: white;
            font-weight: bold;
            font-size: 12px;
        }
        .method.get { background-color: #28a745; }
        .method.post { background-color: #007bff; }
        .method.put { background-color: #ffc107; color: #000; }
        .method.delete { background-color: #dc3545; }
        .response-example {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 15px;
            margin-top: 10px;
        }
        .sidebar {
            position: sticky;
            top: 20px;
            height: calc(100vh - 40px);
            overflow-y: auto;
        }
        .nav-link {
            color: #495057;
            padding: 8px 15px;
        }
        .nav-link:hover {
            background-color: #f8f9fa;
        }
        .nav-link.active {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3">
                <div class="sidebar">
                    <h5 class="mt-3 mb-3">Navegação</h5>
                    <nav class="nav flex-column">
                        <a class="nav-link" href="#introducao">Introdução</a>
                        <a class="nav-link" href="#autenticacao">Autenticação</a>
                        <a class="nav-link" href="#envio">Envio de Notificações</a>
                        <a class="nav-link" href="#envio-bulk">Envio em Massa</a>
                        <a class="nav-link" href="#historico">Histórico</a>
                        <a class="nav-link" href="#estatisticas">Estatísticas</a>
                        <a class="nav-link" href="#detalhes">Detalhes da Notificação</a>
                        <a class="nav-link" href="#reenvio">Reenvio</a>
                        <a class="nav-link" href="#configuracoes">Configurações</a>
                        <a class="nav-link" href="#webhooks">Webhooks</a>
                        <a class="nav-link" href="#exemplos">Exemplos de Código</a>
                    </nav>
                </div>
            </div>
            
            <!-- Content -->
            <div class="col-md-9">
                <div class="container-fluid py-4">
                    <h1 class="mb-4">API de Notificações</h1>
                    <p class="lead">Documentação completa da API REST para integração com o sistema de notificações.</p>
                    
                    <!-- Introdução -->
                    <section id="introducao" class="mb-5">
                        <h2>Introdução</h2>
                        <p>Esta API permite integrar seu sistema com nossa plataforma de notificações, suportando Web Push, E-mail e SMS.</p>
                        <p><strong>URL Base:</strong> <code><?= base_url('api') ?></code></p>
                        <p><strong>Formato:</strong> JSON</p>
                        <p><strong>Encoding:</strong> UTF-8</p>
                    </section>
                    
                    <!-- Autenticação -->
                    <section id="autenticacao" class="mb-5">
                        <h2>Autenticação</h2>
                        <p>Todas as requisições devem incluir o cabeçalho de autenticação:</p>
                        <div class="response-example">
                            <code>X-API-Key: sua_api_key_aqui</code>
                        </div>
                        <div class="alert alert-info mt-3">
                            <strong>Nota:</strong> A API Key pode ser encontrada nas configurações da sua aplicação.
                        </div>
                    </section>
                    
                    <!-- Envio de Notificações -->
                    <section id="envio" class="mb-5">
                        <div class="endpoint">
                            <h3><span class="method post">POST</span> /notifications/send</h3>
                            <p>Envia uma notificação individual.</p>
                            
                            <h5>Parâmetros</h5>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Campo</th>
                                        <th>Tipo</th>
                                        <th>Obrigatório</th>
                                        <th>Descrição</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>channel</td>
                                        <td>string</td>
                                        <td>Sim</td>
                                        <td>Canal: webpush, email, sms</td>
                                    </tr>
                                    <tr>
                                        <td>recipient</td>
                                        <td>string</td>
                                        <td>Sim</td>
                                        <td>Destinatário (endpoint, email ou telefone)</td>
                                    </tr>
                                    <tr>
                                        <td>title</td>
                                        <td>string</td>
                                        <td>Não</td>
                                        <td>Título da notificação</td>
                                    </tr>
                                    <tr>
                                        <td>message</td>
                                        <td>string</td>
                                        <td>Sim</td>
                                        <td>Conteúdo da mensagem</td>
                                    </tr>
                                    <tr>
                                        <td>data</td>
                                        <td>object</td>
                                        <td>Não</td>
                                        <td>Dados adicionais específicos do canal</td>
                                    </tr>
                                    <tr>
                                        <td>scheduled_at</td>
                                        <td>datetime</td>
                                        <td>Não</td>
                                        <td>Agendamento (Y-m-d H:i:s)</td>
                                    </tr>
                                </tbody>
                            </table>
                            
                            <h5>Exemplo de Requisição</h5>
                            <pre class="response-example"><code class="language-json">{
  "channel": "email",
  "recipient": "usuario@exemplo.com",
  "title": "Bem-vindo!",
  "message": "Obrigado por se cadastrar em nossa plataforma.",
  "data": {
    "template": "welcome",
    "sender_name": "Equipe Suporte"
  }
}</code></pre>
                            
                            <h5>Resposta de Sucesso</h5>
                            <pre class="response-example"><code class="language-json">{
  "success": true,
  "message": "Notificação enviada com sucesso",
  "notification_id": 123,
  "status": "sent"
}</code></pre>
                        </div>
                    </section>
                    
                    <!-- Envio em Massa -->
                    <section id="envio-bulk" class="mb-5">
                        <div class="endpoint">
                            <h3><span class="method post">POST</span> /notifications/send/bulk</h3>
                            <p>Envia notificações para múltiplos destinatários.</p>
                            
                            <h5>Exemplo de Requisição</h5>
                            <pre class="response-example"><code class="language-json">{
  "channel": "sms",
  "recipients": ["+5511999999999", "+5511888888888"],
  "message": "Promoção especial! 50% de desconto hoje.",
  "data": {
    "sender": "LOJA",
    "unicode": false
  }
}</code></pre>
                        </div>
                    </section>
                    
                    <!-- Histórico -->
                    <section id="historico" class="mb-5">
                        <div class="endpoint">
                            <h3><span class="method get">GET</span> /notifications/history</h3>
                            <p>Obtém o histórico de notificações com filtros opcionais.</p>
                            
                            <h5>Parâmetros de Query</h5>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Parâmetro</th>
                                        <th>Tipo</th>
                                        <th>Descrição</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>channel</td>
                                        <td>string</td>
                                        <td>Filtrar por canal</td>
                                    </tr>
                                    <tr>
                                        <td>status</td>
                                        <td>string</td>
                                        <td>Filtrar por status</td>
                                    </tr>
                                    <tr>
                                        <td>date_from</td>
                                        <td>date</td>
                                        <td>Data inicial (Y-m-d)</td>
                                    </tr>
                                    <tr>
                                        <td>date_to</td>
                                        <td>date</td>
                                        <td>Data final (Y-m-d)</td>
                                    </tr>
                                    <tr>
                                        <td>page</td>
                                        <td>int</td>
                                        <td>Página (padrão: 1)</td>
                                    </tr>
                                    <tr>
                                        <td>limit</td>
                                        <td>int</td>
                                        <td>Itens por página (padrão: 20)</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>
                    
                    <!-- Estatísticas -->
                    <section id="estatisticas" class="mb-5">
                        <div class="endpoint">
                            <h3><span class="method get">GET</span> /notifications/stats</h3>
                            <p>Obtém estatísticas das notificações.</p>
                            
                            <h5>Resposta</h5>
                            <pre class="response-example"><code class="language-json">{
  "success": true,
  "data": {
    "total": 1250,
    "sent": 1180,
    "failed": 45,
    "pending": 25,
    "delivered": 1150,
    "success_rate": 94.4,
    "by_channel": {
      "webpush": 500,
      "email": 600,
      "sms": 150
    }
  }
}</code></pre>
                        </div>
                    </section>
                    
                    <!-- Detalhes da Notificação -->
                    <section id="detalhes" class="mb-5">
                        <div class="endpoint">
                            <h3><span class="method get">GET</span> /notifications/{id}</h3>
                            <p>Obtém detalhes de uma notificação específica.</p>
                        </div>
                    </section>
                    
                    <!-- Reenvio -->
                    <section id="reenvio" class="mb-5">
                        <div class="endpoint">
                            <h3><span class="method post">POST</span> /notifications/{id}/retry</h3>
                            <p>Reenvia uma notificação que falhou.</p>
                        </div>
                    </section>
                    
                    <!-- Configurações -->
                    <section id="configuracoes" class="mb-5">
                        <div class="endpoint">
                            <h3><span class="method get">GET</span> /channels/config</h3>
                            <p>Obtém as configurações dos canais de notificação.</p>
                        </div>
                    </section>
                    
                    <!-- Webhooks -->
                    <section id="webhooks" class="mb-5">
                        <div class="endpoint">
                            <h3><span class="method post">POST</span> /webhooks/status</h3>
                            <p>Endpoint para receber atualizações de status de provedores externos.</p>
                            
                            <div class="alert alert-warning">
                                <strong>Nota:</strong> Este endpoint é usado internamente pelos provedores de SMS e e-mail para atualizar o status das notificações.
                            </div>
                        </div>
                    </section>
                    
                    <!-- Exemplos de Código -->
                    <section id="exemplos" class="mb-5">
                        <h2>Exemplos de Código</h2>
                        
                        <h4>PHP (cURL)</h4>
                        <pre class="response-example"><code class="language-php"><?php
$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL => '<?= base_url('api/notifications/send') ?>',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'X-API-Key: sua_api_key_aqui'
    ],
    CURLOPT_POSTFIELDS => json_encode([
        'channel' => 'email',
        'recipient' => 'usuario@exemplo.com',
        'title' => 'Teste',
        'message' => 'Mensagem de teste'
    ])
]);

$response = curl_exec($curl);
curl_close($curl);

echo $response;
?></code></pre>
                        
                        <h4>JavaScript (Fetch)</h4>
                        <pre class="response-example"><code class="language-javascript">fetch('<?= base_url('api/notifications/send') ?>', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-API-Key': 'sua_api_key_aqui'
    },
    body: JSON.stringify({
        channel: 'webpush',
        recipient: 'endpoint_do_usuario',
        title: 'Nova mensagem',
        message: 'Você tem uma nova mensagem!'
    })
})
.then(response => response.json())
.then(data => console.log(data));</code></pre>
                        
                        <h4>Python (Requests)</h4>
                        <pre class="response-example"><code class="language-python">import requests
import json

url = '<?= base_url('api/notifications/send') ?>'
headers = {
    'Content-Type': 'application/json',
    'X-API-Key': 'sua_api_key_aqui'
}
data = {
    'channel': 'sms',
    'recipient': '+5511999999999',
    'message': 'Sua mensagem SMS aqui'
}

response = requests.post(url, headers=headers, data=json.dumps(data))
print(response.json())</code></pre>
                    </section>
                    
                    <!-- Códigos de Erro -->
                    <section id="erros" class="mb-5">
                        <h2>Códigos de Erro</h2>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Descrição</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>400</td>
                                    <td>Requisição inválida - parâmetros obrigatórios ausentes</td>
                                </tr>
                                <tr>
                                    <td>401</td>
                                    <td>Não autorizado - API Key inválida ou ausente</td>
                                </tr>
                                <tr>
                                    <td>404</td>
                                    <td>Recurso não encontrado</td>
                                </tr>
                                <tr>
                                    <td>429</td>
                                    <td>Muitas requisições - limite de taxa excedido</td>
                                </tr>
                                <tr>
                                    <td>500</td>
                                    <td>Erro interno do servidor</td>
                                </tr>
                            </tbody>
                        </table>
                    </section>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.24.1/components/prism-core.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.24.1/plugins/autoloader/prism-autoloader.min.js"></script>
    <script>
        // Smooth scrolling para links da navegação
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href').substring(1);
                const targetElement = document.getElementById(targetId);
                if (targetElement) {
                    targetElement.scrollIntoView({ behavior: 'smooth' });
                    
                    // Atualizar link ativo
                    document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                    this.classList.add('active');
                }
            });
        });
        
        // Destacar seção ativa durante scroll
        window.addEventListener('scroll', function() {
            const sections = document.querySelectorAll('section');
            const navLinks = document.querySelectorAll('.nav-link');
            
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                if (pageYOffset >= sectionTop - 200) {
                    current = section.getAttribute('id');
                }
            });
            
            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === '#' + current) {
                    link.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>