/**
 * Service Worker para Web Push Notifications
 * Gerencia o recebimento e exibição de notificações push
 */

const CACHE_NAME = 'webpush-v1';
const urlsToCache = [
    '/',
    '/css/app.css',
    '/js/app.js',
    '/js/webpush.js'
];

// Instalação do Service Worker
self.addEventListener('install', event => {
    console.log('Service Worker: Instalando...');
    
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                console.log('Service Worker: Cache aberto');
                return cache.addAll(urlsToCache);
            })
            .catch(error => {
                console.error('Service Worker: Erro ao abrir cache:', error);
            })
    );
});

// Ativação do Service Worker
self.addEventListener('activate', event => {
    console.log('Service Worker: Ativando...');
    
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    if (cacheName !== CACHE_NAME) {
                        console.log('Service Worker: Removendo cache antigo:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});

// Interceptar requisições de rede
self.addEventListener('fetch', event => {
    event.respondWith(
        caches.match(event.request)
            .then(response => {
                // Retorna do cache se disponível, senão busca na rede
                return response || fetch(event.request);
            })
            .catch(error => {
                console.error('Service Worker: Erro ao buscar recurso:', error);
            })
    );
});

// Receber notificação push
self.addEventListener('push', event => {
    console.log('Service Worker: Push recebido:', event);
    
    let notificationData = {
        title: 'Nova Notificação',
        body: 'Você recebeu uma nova mensagem',
        icon: '/images/icon-192x192.png',
        badge: '/images/badge-96x96.png',
        tag: 'default',
        requireInteraction: false,
        silent: false,
        data: {
            url: '/',
            timestamp: Date.now()
        }
    };
    
    // Processar dados da notificação se disponíveis
    if (event.data) {
        try {
            const pushData = event.data.json();
            notificationData = {
                ...notificationData,
                ...pushData,
                data: {
                    ...notificationData.data,
                    ...(pushData.data || {})
                }
            };
        } catch (error) {
            console.error('Service Worker: Erro ao processar dados push:', error);
            // Usar dados padrão se houver erro
        }
    }
    
    // Exibir notificação
    event.waitUntil(
        self.registration.showNotification(notificationData.title, {
            body: notificationData.body,
            icon: notificationData.icon,
            badge: notificationData.badge,
            tag: notificationData.tag,
            requireInteraction: notificationData.requireInteraction,
            silent: notificationData.silent,
            data: notificationData.data,
            actions: notificationData.actions || [],
            image: notificationData.image,
            timestamp: notificationData.timestamp || Date.now(),
            renotify: notificationData.renotify || false,
            vibrate: notificationData.vibrate || [200, 100, 200]
        })
        .then(() => {
            console.log('Service Worker: Notificação exibida');
            
            // Registrar visualização da notificação (opcional)
            if (notificationData.data && notificationData.data.trackingUrl) {
                fetch(notificationData.data.trackingUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        event: 'notification_displayed',
                        timestamp: Date.now(),
                        notificationId: notificationData.data.notificationId
                    })
                }).catch(error => {
                    console.error('Service Worker: Erro ao registrar visualização:', error);
                });
            }
        })
        .catch(error => {
            console.error('Service Worker: Erro ao exibir notificação:', error);
        })
    );
});

// Clique na notificação
self.addEventListener('notificationclick', event => {
    console.log('Service Worker: Notificação clicada:', event);
    
    event.notification.close();
    
    const notificationData = event.notification.data || {};
    let targetUrl = notificationData.url || '/';
    
    // Processar ações da notificação
    if (event.action) {
        console.log('Service Worker: Ação da notificação:', event.action);
        
        switch (event.action) {
            case 'view':
                targetUrl = notificationData.viewUrl || targetUrl;
                break;
            case 'dismiss':
                // Apenas fechar a notificação
                return;
            default:
                // Ação personalizada
                if (notificationData.actions && notificationData.actions[event.action]) {
                    targetUrl = notificationData.actions[event.action].url || targetUrl;
                }
        }
    }
    
    // Abrir/focar janela
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then(clientList => {
                // Verificar se já existe uma janela aberta com a URL
                for (let client of clientList) {
                    if (client.url === targetUrl && 'focus' in client) {
                        return client.focus();
                    }
                }
                
                // Abrir nova janela se não encontrou uma existente
                if (clients.openWindow) {
                    return clients.openWindow(targetUrl);
                }
            })
            .then(() => {
                // Registrar clique da notificação (opcional)
                if (notificationData.trackingUrl) {
                    return fetch(notificationData.trackingUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            event: 'notification_clicked',
                            action: event.action || 'default',
                            timestamp: Date.now(),
                            notificationId: notificationData.notificationId,
                            targetUrl: targetUrl
                        })
                    });
                }
            })
            .catch(error => {
                console.error('Service Worker: Erro ao processar clique:', error);
            })
    );
});

// Fechar notificação
self.addEventListener('notificationclose', event => {
    console.log('Service Worker: Notificação fechada:', event);
    
    const notificationData = event.notification.data || {};
    
    // Registrar fechamento da notificação (opcional)
    if (notificationData.trackingUrl) {
        fetch(notificationData.trackingUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                event: 'notification_closed',
                timestamp: Date.now(),
                notificationId: notificationData.notificationId
            })
        }).catch(error => {
            console.error('Service Worker: Erro ao registrar fechamento:', error);
        });
    }
});

// Sincronização em background (opcional)
self.addEventListener('sync', event => {
    console.log('Service Worker: Sincronização em background:', event.tag);
    
    if (event.tag === 'background-sync') {
        event.waitUntil(
            // Executar tarefas de sincronização
            doBackgroundSync()
        );
    }
});

// Função para sincronização em background
function doBackgroundSync() {
    return new Promise((resolve, reject) => {
        // Implementar lógica de sincronização se necessário
        console.log('Service Worker: Executando sincronização em background');
        resolve();
    });
}

// Gerenciar atualizações do Service Worker
self.addEventListener('message', event => {
    console.log('Service Worker: Mensagem recebida:', event.data);
    
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
    
    if (event.data && event.data.type === 'GET_VERSION') {
        event.ports[0].postMessage({
            type: 'VERSION',
            version: CACHE_NAME
        });
    }
});

// Tratamento de erros
self.addEventListener('error', event => {
    console.error('Service Worker: Erro:', event.error);
});

self.addEventListener('unhandledrejection', event => {
    console.error('Service Worker: Promise rejeitada:', event.reason);
    event.preventDefault();
});