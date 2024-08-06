-- Estrutura do banco de dados para Plataforma de Notificações
-- Database: notification_platform

CREATE DATABASE IF NOT EXISTS notification_platform;
USE notification_platform;

-- Tabela de usuários
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL
);

-- Tabela de aplicativos
CREATE TABLE applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    api_key VARCHAR(255) UNIQUE NOT NULL,
    api_secret VARCHAR(255) NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabela de canais de notificação
CREATE TABLE notification_channels (
    id INT AUTO_INCREMENT PRIMARY KEY,
    application_id INT NOT NULL,
    channel_type ENUM('webpush', 'email', 'sms') NOT NULL,
    is_enabled BOOLEAN DEFAULT TRUE,
    configuration JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE
);

-- Tabela de configurações Web Push
CREATE TABLE webpush_configs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    channel_id INT NOT NULL,
    site_name VARCHAR(255) NOT NULL,
    site_url VARCHAR(255) NOT NULL,
    icon_url VARCHAR(255),
    permission_message TEXT,
    allow_button_text VARCHAR(100) DEFAULT 'Permitir',
    deny_button_text VARCHAR(100) DEFAULT 'Negar',
    welcome_title VARCHAR(255),
    welcome_message TEXT,
    welcome_link_enabled BOOLEAN DEFAULT FALSE,
    welcome_link_url VARCHAR(255),
    vapid_public_key TEXT,
    vapid_private_key TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (channel_id) REFERENCES notification_channels(id) ON DELETE CASCADE
);

-- Tabela de configurações Email
CREATE TABLE email_configs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    channel_id INT NOT NULL,
    smtp_host VARCHAR(255) NOT NULL,
    smtp_port INT NOT NULL DEFAULT 587,
    smtp_username VARCHAR(255) NOT NULL,
    smtp_password VARCHAR(255) NOT NULL,
    smtp_encryption ENUM('tls', 'ssl', 'none') DEFAULT 'tls',
    from_name VARCHAR(255) NOT NULL,
    from_email VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (channel_id) REFERENCES notification_channels(id) ON DELETE CASCADE
);

-- Tabela de templates de email
CREATE TABLE email_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    channel_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    subject VARCHAR(255),
    html_content LONGTEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (channel_id) REFERENCES notification_channels(id) ON DELETE CASCADE
);

-- Tabela de configurações SMS
CREATE TABLE sms_configs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    channel_id INT NOT NULL,
    provider ENUM('twilio', 'nexmo', 'custom') NOT NULL,
    api_username VARCHAR(255) NOT NULL,
    api_password VARCHAR(255) NOT NULL,
    sender_id VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (channel_id) REFERENCES notification_channels(id) ON DELETE CASCADE
);

-- Tabela de assinantes Web Push
CREATE TABLE webpush_subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    application_id INT NOT NULL,
    endpoint TEXT NOT NULL,
    p256dh_key TEXT NOT NULL,
    auth_key TEXT NOT NULL,
    user_agent TEXT,
    ip_address VARCHAR(45),
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    unsubscribed_at TIMESTAMP NULL,
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE
);

-- Tabela de notificações enviadas
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    application_id INT NOT NULL,
    channel_type ENUM('webpush', 'email', 'sms') NOT NULL,
    send_type ENUM('api', 'manual') NOT NULL,
    title VARCHAR(255),
    message TEXT NOT NULL,
    recipients JSON NOT NULL,
    metadata JSON,
    status ENUM('pending', 'sent', 'failed', 'delivered', 'read') DEFAULT 'pending',
    sent_at TIMESTAMP NULL,
    delivered_at TIMESTAMP NULL,
    read_at TIMESTAMP NULL,
    error_message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE
);

-- Tabela de logs de entrega
CREATE TABLE delivery_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    notification_id INT NOT NULL,
    recipient VARCHAR(255) NOT NULL,
    status ENUM('sent', 'delivered', 'failed', 'bounced', 'read') NOT NULL,
    response_data JSON,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (notification_id) REFERENCES notifications(id) ON DELETE CASCADE
);

-- Índices para otimização
CREATE INDEX idx_applications_user_id ON applications(user_id);
CREATE INDEX idx_applications_api_key ON applications(api_key);
CREATE INDEX idx_notification_channels_app_id ON notification_channels(application_id);
CREATE INDEX idx_notifications_app_id ON notifications(application_id);
CREATE INDEX idx_notifications_status ON notifications(status);
CREATE INDEX idx_notifications_created_at ON notifications(created_at);
CREATE INDEX idx_delivery_logs_notification_id ON delivery_logs(notification_id);
CREATE INDEX idx_webpush_subscribers_app_id ON webpush_subscribers(application_id);

-- Inserir usuário padrão para testes
INSERT INTO users (name, email, password) VALUES 
('Admin', 'admin@notification.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');