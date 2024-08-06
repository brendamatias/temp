import Produto from '../../../../model/produtoModel';
import ImageNotfound from '../../../../assets/not-found-image.png';
import { Box, Button, Card, CardActions, CardContent, CardMedia, Grid, Typography } from '@mui/material';
import { useNavigate } from "react-router-dom";
import ChatComponent from '../../../chat-box';
import './styles.css';
import { useState } from 'react';


interface ProdutoArray extends Array<Produto> { };

interface ProdutoProps{
  produtos: ProdutoArray;
}

export default function ProdutoCard({produtos} :  ProdutoProps){  

   const [chatOpen, setChatOpen] = useState(false);
   const [contatoVendedor, setContatoVendedor] = useState('');

   const handleChamarVendedor = (contato: any) => {
    setContatoVendedor(contato);
    setChatOpen(true);
   };

  const handleCloseChat = () => {
    setChatOpen(false);
  };

  const navigate = useNavigate();
  
  const handleVerMais = (produto: Produto) => {
      navigate(`/buscar-produto/${produto.id}`);
  };

  return (
    <Box mt={10} sx={{width :'70%', margin: 'auto', marginTop: '5em !important'}}>
      <Typography variant="h4" gutterBottom className='title'>
        Produtos Disponíveis
      </Typography>
      <Grid container spacing={3}>
        {produtos.map((produto : Produto) => 
          <Grid item xs={12} md={4} key={produto.id} alignItems={'center'} gap={4}  className='product-card'>
            <Card>
              <CardMedia
                component="img"
                height="140"
                image={produto.fotoUrl ? produto.fotoUrl : ImageNotfound}
                alt={`Imagem do produto ${produto.nome}`}
              />
              <CardContent>
                <Typography variant="h5" component="div">
                  {produto.nome}
                </Typography>
                <Typography variant="body2" color="text.secondary">
                  Preço: R$ {produto.preco.toFixed(2)}
                </Typography>
              </CardContent>
              <CardActions>
                <Button size="small" onClick={() => handleVerMais(produto)}>
                  Ver mais
                </Button>
                <Button size="small" color="primary" onClick={() => handleChamarVendedor(produto.contatoVendedor)}>
                  Chamar vendedor
                </Button>
              </CardActions>
            </Card>
          </Grid>
          
        )}

        <ChatComponent
          open={chatOpen}
          handleClose={handleCloseChat}
          contato={contatoVendedor}
        />
      </Grid>
    </Box>
  );
}