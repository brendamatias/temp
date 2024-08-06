/**
 * Web Push Notification Manager
 * Gerencia as funcionalidades de Web Push no frontend
 */
class WebPushManager {
    constructor(config = {}) {
        this.config = {
            vapidPublicKey: config.vapidPublicKey || '',
            serviceWorkerPath: config.serviceWorkerPath || '/sw.js',
            apiEndpoint: config.apiEndpoint || '/api/webpush',
            ...config
        };
        
        this.isSupported = 'serviceWorker' in navigator && 'PushManager' in window;
        this.registration = null;
        this.subscription = null;
        
        this.init();
    }
    
    async init() {
        if (!this.isSupported) {
            console.warn('Web Push não é suportado neste navegador');
            return;
        }
        
        try {
            // Registrar Service Worker
            this.registration = await navigator.serviceWorker.register(this.config.serviceWorkerPath);
            console.log('Service Worker registrado:', this.registration);
            
            // Verificar se já existe uma inscrição
            this.subscription = await this.registration.pushManager.getSubscription();
            
            if (this.subscription) {
                console.log('Usuário já inscrito:', this.subscription);
                this.updateUI('subscribed');
            } else {
                this.updateUI('unsubscribed');
            }
        } catch (error) {
            console.error('Erro ao inicializar Web Push:', error);
            this.updateUI('error');
        }
    }
    
    async requestPermission() {
        if (!this.isSupported) {
            throw new Error('Web Push não é suportado');
        }
        
        const permission = await Notification.requestPermission();
        
        if (permission === 'granted') {
            return await this.subscribe();
        } else if (permission === 'denied') {
            throw new Error('Permissão negada pelo usuário');
        } else {
            throw new Error('Permissão não concedida');
        }
    }
    
    async subscribe() {
        if (!this.registration) {
            throw new Error('Service Worker não registrado');
        }
        
        try {
            const subscription = await this.registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: this.urlBase64ToUint8Array(this.config.vapidPublicKey)
            });
            
            this.subscription = subscription;
            
            // Enviar inscrição para o servidor
            await this.sendSubscriptionToServer(subscription);
            
            this.updateUI('subscribed');
            
            return subscription;
        } catch (error) {
            console.error('Erro ao inscrever:', error);
            throw error;
        }
    }
    
    async unsubscribe() {
        if (!this.subscription) {
            return;
        }
        
        try {
            await this.subscription.unsubscribe();
            
            // Remover inscrição do servidor
            await this.removeSubscriptionFromServer(this.subscription);
            
            this.subscription = null;
            this.updateUI('unsubscribed');
        } catch (error) {
            console.error('Erro ao desinscrever:', error);
            throw error;
        }
    }
    
    async sendSubscriptionToServer(subscription) {
        const response = await fetch(this.config.apiEndpoint + '/subscribe', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                subscription: subscription.toJSON(),
                userAgent: navigator.userAgent,
                timestamp: new Date().toISOString()
            })
        });
        
        if (!response.ok) {
            throw new Error('Erro ao enviar inscrição para o servidor');
        }
        
        return await response.json();
    }
    
    async removeSubscriptionFromServer(subscription) {
        const response = await fetch(this.config.apiEndpoint + '/unsubscribe', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                subscription: subscription.toJSON()
            })
        });
        
        if (!response.ok) {
            throw new Error('Erro ao remover inscrição do servidor');
        }
        
        return await response.json();
    }
    
    updateUI(state) {
        const elements = {
            subscribeBtn: document.getElementById('webpush-subscribe'),
            unsubscribeBtn: document.getElementById('webpush-unsubscribe'),
            status: document.getElementById('webpush-status'),
            error: document.getElementById('webpush-error')
        };
        
        // Limpar estados anteriores
        Object.values(elements).forEach(el => {
            if (el) el.style.display = 'none';
        });
        
        switch (state) {
            case 'subscribed':
                if (elements.unsubscribeBtn) elements.unsubscribeBtn.style.display = 'block';
                if (elements.status) {
                    elements.status.innerHTML = '<i class="fas fa-check-circle text-success"></i> Notificações ativadas';
                    elements.status.style.display = 'block';
                }
                break;
                
            case 'unsubscribed':
                if (elements.subscribeBtn) elements.subscribeBtn.style.display = 'block';
                if (elements.status) {
                    elements.status.innerHTML = '<i class="fas fa-bell-slash text-muted"></i> Notificações desativadas';
                    elements.status.style.display = 'block';
                }
                break;
                
            case 'error':
                if (elements.error) {
                    elements.error.innerHTML = '<i class="fas fa-exclamation-triangle text-danger"></i> Erro ao configurar notificações';
                    elements.error.style.display = 'block';
                }
                break;
        }
    }
    
    urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding)
            .replace(/-/g, '+')
            .replace(/_/g, '/');
        
        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);
        
        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        
        return outputArray;
    }
    
    // Métodos utilitários
    getPermissionStatus() {
        if (!('Notification' in window)) {
            return 'unsupported';
        }
        return Notification.permission;
    }
    
    isSubscribed() {
        return this.subscription !== null;
    }
    
    getSubscription() {
        return this.subscription;
    }
}

// Widget de permissão personalizado
class WebPushPermissionWidget {
    constructor(config = {}) {
        this.config = {
            title: config.title || 'Permitir Notificações',
            message: config.message || 'Gostaríamos de enviar notificações para mantê-lo atualizado.',
            allowText: config.allowText || 'Permitir',
            denyText: config.denyText || 'Não, obrigado',
            position: config.position || 'top-right',
            autoShow: config.autoShow !== false,
            ...config
        };
        
        this.element = null;
        this.onAllow = config.onAllow || (() => {});
        this.onDeny = config.onDeny || (() => {});
        
        if (this.config.autoShow) {
            this.show();
        }
    }
    
    show() {
        if (this.element) {
            return; // Já está sendo exibido
        }
        
        this.element = this.createElement();
        document.body.appendChild(this.element);
        
        // Animação de entrada
        setTimeout(() => {
            this.element.classList.add('show');
        }, 100);
    }
    
    hide() {
        if (!this.element) {
            return;
        }
        
        this.element.classList.remove('show');
        
        setTimeout(() => {
            if (this.element && this.element.parentNode) {
                this.element.parentNode.removeChild(this.element);
            }
            this.element = null;
        }, 300);
    }
    
    createElement() {
        const widget = document.createElement('div');
        widget.className = `webpush-permission-widget webpush-${this.config.position}`;
        
        widget.innerHTML = `
            <div class="webpush-widget-content">
                <div class="webpush-widget-header">
                    <i class="fas fa-bell"></i>
                    <h6>${this.config.title}</h6>
                    <button class="webpush-close" onclick="this.closest('.webpush-permission-widget').remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="webpush-widget-body">
                    <p>${this.config.message}</p>
                </div>
                <div class="webpush-widget-actions">
                    <button class="webpush-btn webpush-btn-allow">${this.config.allowText}</button>
                    <button class="webpush-btn webpush-btn-deny">${this.config.denyText}</button>
                </div>
            </div>
        `;
        
        // Event listeners
        widget.querySelector('.webpush-btn-allow').addEventListener('click', () => {
            this.onAllow();
            this.hide();
        });
        
        widget.querySelector('.webpush-btn-deny').addEventListener('click', () => {
            this.onDeny();
            this.hide();
        });
        
        widget.querySelector('.webpush-close').addEventListener('click', () => {
            this.hide();
        });
        
        return widget;
    }
}

// Estilos CSS para o widget (injetados dinamicamente)
const widgetStyles = `
.webpush-permission-widget {
    position: fixed;
    z-index: 10000;
    max-width: 350px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    transform: translateY(-20px);
    opacity: 0;
    transition: all 0.3s ease;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.webpush-permission-widget.show {
    transform: translateY(0);
    opacity: 1;
}

.webpush-top-right {
    top: 20px;
    right: 20px;
}

.webpush-top-left {
    top: 20px;
    left: 20px;
}

.webpush-bottom-right {
    bottom: 20px;
    right: 20px;
}

.webpush-bottom-left {
    bottom: 20px;
    left: 20px;
}

.webpush-widget-content {
    padding: 16px;
}

.webpush-widget-header {
    display: flex;
    align-items: center;
    margin-bottom: 12px;
}

.webpush-widget-header i {
    color: #007bff;
    margin-right: 8px;
}

.webpush-widget-header h6 {
    margin: 0;
    flex: 1;
    font-size: 16px;
    font-weight: 600;
}

.webpush-close {
    background: none;
    border: none;
    color: #6c757d;
    cursor: pointer;
    padding: 4px;
}

.webpush-widget-body p {
    margin: 0;
    color: #6c757d;
    font-size: 14px;
    line-height: 1.4;
}

.webpush-widget-actions {
    display: flex;
    gap: 8px;
    margin-top: 16px;
}

.webpush-btn {
    flex: 1;
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: background-color 0.2s;
}

.webpush-btn-allow {
    background: #007bff;
    color: white;
}

.webpush-btn-allow:hover {
    background: #0056b3;
}

.webpush-btn-deny {
    background: #f8f9fa;
    color: #6c757d;
    border: 1px solid #dee2e6;
}

.webpush-btn-deny:hover {
    background: #e9ecef;
}
`;

// Injetar estilos
if (!document.getElementById('webpush-styles')) {
    const styleSheet = document.createElement('style');
    styleSheet.id = 'webpush-styles';
    styleSheet.textContent = widgetStyles;
    document.head.appendChild(styleSheet);
}

// Exportar para uso global
window.WebPushManager = WebPushManager;
window.WebPushPermissionWidget = WebPushPermissionWidget;