import { useState, useEffect } from 'react';
import { Box, IconButton, Paper, Typography, TextField, Button } from '@mui/material';
import CloseIcon from '@mui/icons-material/Close';
import { Client } from '@stomp/stompjs';

interface Mensagem {
  texto: string;
  tipo: 'enviado' | 'recebido';
}

export default function ChatComponent({ open, handleClose }: any) {
  const [mensagens, setMensagens] = useState<Mensagem[]>([]);
  const [novaMensagem, setNovaMensagem] = useState('');
  const [client, setClient] = useState<Client | null>(null);

  const handleEnviarMensagem = () => {
    if (novaMensagem.trim() === '') return; 

    setMensagens([...mensagens, { texto: novaMensagem, tipo: 'enviado' }]);

    if (client && client.connected) {
      client.publish({
        destination: '/app/chatmessage', 
        body: JSON.stringify({
          nomeUser: 'Usuario', 
          msg: novaMensagem,
        }),
      });
    }

    setNovaMensagem(''); 
  };

  useEffect(() => {
    const stompClient = new Client({
      brokerURL: 'ws://localhost:8080/chatmessage/websocket', 
      reconnectDelay: 5000, 
      onConnect: () => {
        console.log('Connected to WebSocket');

        stompClient.subscribe('/chat', (message) => {
          if (message.body) {
            const recebida = JSON.parse(message.body);
            setMensagens((prevMensagens) => [
              ...prevMensagens,
              { texto: recebida.msg, tipo: 'recebido' },
            ]);
          }
        });
      },
      onStompError: (frame) => {
        console.error('Broker reported error: ' + frame.headers['message']);
      },
    });

    stompClient.activate(); 

    setClient(stompClient);

    return () => {
      if (stompClient) {
        stompClient.deactivate(); 
      }
    };
  }, []);

  if (!open) return null;

  return (
    <Box sx={{ position: 'fixed', bottom: 20, right: 20, zIndex: 1000 }}>
      <Paper elevation={3} sx={{ width: 300, p: 2, display: 'flex', flexDirection: 'column' }}>
        <Box sx={{ display: 'flex', justifyContent: 'space-between' }}>
          <Typography variant="h6" style={{ borderBottom: '1px solid #b6b6b6ab' }}>
            Chat com o Vendedor
          </Typography>
          <IconButton onClick={handleClose}>
            <CloseIcon />
          </IconButton>
        </Box>

        <Box sx={{ flexGrow: 1, overflowY: 'auto', maxHeight: 200, mb: 2 }}>
          {mensagens.map((mensagem, index) => (
            <Box
              key={index}
              sx={{
                display: 'flex',
                justifyContent: mensagem.tipo === 'enviado' ? 'flex-end' : 'flex-start',
                mb: 1,
              }}
            >
              <Paper
                sx={{
                  p: 1,
                  backgroundColor: mensagem.tipo === 'enviado' ? '#e0e0e0' : '#1976d2',
                  color: mensagem.tipo === 'enviado' ? '#000' : '#fff',
                  borderRadius: 2,
                  maxWidth: '80%',
                }}
              >
                {mensagem.texto}
              </Paper>
            </Box>
          ))}
        </Box>

        <TextField
          fullWidth
          multiline
          rows={4}
          variant="outlined"
          size="small"
          placeholder="Digite sua mensagem..."
          value={novaMensagem}
          onChange={(e) => setNovaMensagem(e.target.value)}
          onKeyPress={(e) => {
            if (e.key === 'Enter') {
              e.preventDefault();
              handleEnviarMensagem();
            }
          }}
          sx={{ mb: 1 }}
        />
        <Button variant="contained" color="primary" fullWidth onClick={handleEnviarMensagem}>
          Enviar
        </Button>
      </Paper>
    </Box>
  );
}