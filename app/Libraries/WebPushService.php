<?php

namespace App\Libraries;

use Exception;

/**
 * Serviço para envio de notificações Web Push
 * Implementa o protocolo Web Push usando VAPID
 */
class WebPushService
{
    private $vapidPublicKey;
    private $vapidPrivateKey;
    private $vapidSubject;
    
    public function __construct($vapidPublicKey = null, $vapidPrivateKey = null, $vapidSubject = null)
    {
        $this->vapidPublicKey = $vapidPublicKey;
        $this->vapidPrivateKey = $vapidPrivateKey;
        $this->vapidSubject = $vapidSubject;
    }
    
    /**
     * Configurar chaves VAPID
     */
    public function setVapidKeys($publicKey, $privateKey, $subject)
    {
        $this->vapidPublicKey = $publicKey;
        $this->vapidPrivateKey = $privateKey;
        $this->vapidSubject = $subject;
    }
    
    /**
     * Enviar notificação push
     */
    public function sendNotification($subscription, $payload, $options = [])
    {
        if (!$this->vapidPublicKey || !$this->vapidPrivateKey || !$this->vapidSubject) {
            throw new Exception('Chaves VAPID não configuradas');
        }
        
        $subscriptionData = is_string($subscription) ? json_decode($subscription, true) : $subscription;
        
        if (!isset($subscriptionData['endpoint'])) {
            throw new Exception('Endpoint da inscrição não encontrado');
        }
        
        $endpoint = $subscriptionData['endpoint'];
        $p256dh = $subscriptionData['keys']['p256dh'] ?? null;
        $auth = $subscriptionData['keys']['auth'] ?? null;
        
        // Preparar payload
        $payloadData = is_array($payload) ? json_encode($payload) : $payload;
        
        // Criptografar payload se necessário
        if ($p256dh && $auth) {
            $encryptedPayload = $this->encryptPayload($payloadData, $p256dh, $auth);
        } else {
            $encryptedPayload = null;
        }
        
        // Gerar headers VAPID
        $vapidHeaders = $this->generateVapidHeaders($endpoint);
        
        // Preparar headers da requisição
        $headers = array_merge([
            'Content-Type: application/octet-stream',
            'Content-Length: ' . strlen($encryptedPayload ?: ''),
            'TTL: ' . ($options['ttl'] ?? 2419200), // 4 semanas por padrão
            'Urgency: ' . ($options['urgency'] ?? 'normal')
        ], $vapidHeaders);
        
        // Enviar requisição
        return $this->sendHttpRequest($endpoint, $encryptedPayload, $headers);
    }
    
    /**
     * Gerar headers VAPID para autenticação
     */
    private function generateVapidHeaders($endpoint)
    {
        $url = parse_url($endpoint);
        $audience = $url['scheme'] . '://' . $url['host'];
        
        $header = [
            'typ' => 'JWT',
            'alg' => 'ES256'
        ];
        
        $payload = [
            'aud' => $audience,
            'exp' => time() + 12 * 60 * 60, // 12 horas
            'sub' => $this->vapidSubject
        ];
        
        // Simular JWT (em produção, usar biblioteca JWT adequada)
        $jwt = $this->createSimpleJWT($header, $payload);
        
        return [
            'Authorization: vapid t=' . $jwt . ', k=' . $this->vapidPublicKey
        ];
    }
    
    /**
     * Criar JWT simples (para demonstração)
     * Em produção, usar biblioteca JWT adequada como firebase/php-jwt
     */
    private function createSimpleJWT($header, $payload)
    {
        $headerEncoded = $this->base64UrlEncode(json_encode($header));
        $payloadEncoded = $this->base64UrlEncode(json_encode($payload));
        
        $signature = $this->base64UrlEncode('simulated_signature_' . hash('sha256', $headerEncoded . '.' . $payloadEncoded));
        
        return $headerEncoded . '.' . $payloadEncoded . '.' . $signature;
    }
    
    /**
     * Criptografar payload da notificação
     * Implementação simplificada - em produção usar biblioteca adequada
     */
    private function encryptPayload($payload, $p256dh, $auth)
    {
        // Esta é uma implementação simplificada
        // Em produção, usar biblioteca como web-push-php ou similar
        return $payload;
    }
    
    /**
     * Enviar requisição HTTP
     */
    private function sendHttpRequest($url, $data, $headers)
    {
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        
        curl_close($ch);
        
        if ($error) {
            throw new Exception('Erro cURL: ' . $error);
        }
        
        return [
            'success' => $httpCode >= 200 && $httpCode < 300,
            'http_code' => $httpCode,
            'response' => $response,
            'error' => $httpCode >= 400 ? $this->getErrorMessage($httpCode) : null
        ];
    }
    
    /**
     * Obter mensagem de erro baseada no código HTTP
     */
    private function getErrorMessage($httpCode)
    {
        $errors = [
            400 => 'Requisição inválida',
            401 => 'Não autorizado - verifique as chaves VAPID',
            403 => 'Proibido - endpoint pode estar inválido',
            404 => 'Endpoint não encontrado',
            410 => 'Inscrição expirada',
            413 => 'Payload muito grande',
            429 => 'Muitas requisições - tente novamente mais tarde',
            500 => 'Erro interno do servidor push',
            502 => 'Gateway inválido',
            503 => 'Serviço indisponível'
        ];
        
        return $errors[$httpCode] ?? 'Erro desconhecido: ' . $httpCode;
    }
    
    /**
     * Codificar em Base64 URL-safe
     */
    private function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    
    /**
     * Decodificar Base64 URL-safe
     */
    private function base64UrlDecode($data)
    {
        return base64_decode(strtr($data, '-_', '+/') . str_repeat('=', 3 - (3 + strlen($data)) % 4));
    }
    
    /**
     * Validar inscrição
     */
    public function validateSubscription($subscription)
    {
        $subscriptionData = is_string($subscription) ? json_decode($subscription, true) : $subscription;
        
        if (!is_array($subscriptionData)) {
            return false;
        }
        
        // Verificar campos obrigatórios
        if (!isset($subscriptionData['endpoint'])) {
            return false;
        }
        
        // Verificar se é um endpoint válido
        if (!filter_var($subscriptionData['endpoint'], FILTER_VALIDATE_URL)) {
            return false;
        }
        
        // Verificar chaves se presentes
        if (isset($subscriptionData['keys'])) {
            if (!isset($subscriptionData['keys']['p256dh']) || !isset($subscriptionData['keys']['auth'])) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Gerar chaves VAPID
     * Implementação simplificada - em produção usar biblioteca adequada
     */
    public static function generateVapidKeys()
    {
        // Esta é uma implementação simplificada para demonstração
        // Em produção, usar biblioteca como web-push-php ou similar
        
        $privateKey = bin2hex(random_bytes(32));
        $publicKey = bin2hex(random_bytes(65));
        
        return [
            'public_key' => rtrim(strtr(base64_encode(hex2bin($publicKey)), '+/', '-_'), '='),
            'private_key' => rtrim(strtr(base64_encode(hex2bin($privateKey)), '+/', '-_'), '=')
        ];
    }
    
    /**
     * Criar payload de notificação
     */
    public function createNotificationPayload($title, $body, $options = [])
    {
        $payload = [
            'title' => $title,
            'body' => $body
        ];
        
        // Opções adicionais
        if (isset($options['icon'])) {
            $payload['icon'] = $options['icon'];
        }
        
        if (isset($options['badge'])) {
            $payload['badge'] = $options['badge'];
        }
        
        if (isset($options['image'])) {
            $payload['image'] = $options['image'];
        }
        
        if (isset($options['url'])) {
            $payload['data'] = ['url' => $options['url']];
        }
        
        if (isset($options['tag'])) {
            $payload['tag'] = $options['tag'];
        }
        
        if (isset($options['requireInteraction'])) {
            $payload['requireInteraction'] = $options['requireInteraction'];
        }
        
        if (isset($options['silent'])) {
            $payload['silent'] = $options['silent'];
        }
        
        if (isset($options['actions'])) {
            $payload['actions'] = $options['actions'];
        }
        
        if (isset($options['data'])) {
            $payload['data'] = array_merge($payload['data'] ?? [], $options['data']);
        }
        
        return $payload;
    }
    
    /**
     * Enviar notificação para múltiplas inscrições
     */
    public function sendToMultiple($subscriptions, $payload, $options = [])
    {
        $results = [];
        
        foreach ($subscriptions as $index => $subscription) {
            try {
                $result = $this->sendNotification($subscription, $payload, $options);
                $results[$index] = $result;
            } catch (Exception $e) {
                $results[$index] = [
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
        }
        
        return $results;
    }
}