// A PRINCIPIO ESS APAGE SERIA A PRINCIPAL DE LISTAGEM DE PRODUTOS,
//PORÉM ESTOU DEIXANDO - A PARA SEGUNDA VERSAO POIS A COMPLEXIDADE DE DELAHES PENSADA PARA ELA FICOU
//INVIÁVEL PRO TEMPO RESTANTE.
// ATUALMENTE A APLICAÇÃO ESTÁ UTILIZANDO O INDEX DO PACOTE "CARDS"


import { useState } from "react";
import Produto from "../../../../model/produtoModel";
import './styles.css';
import ImageNotfound from '../../../../assets/not-found-image.png';
import { Box, Button, Card, CardMedia, CardContent, Typography, Dialog, IconButton } from "@mui/material";
import CloseIcon from '@mui/icons-material/Close';

interface ProdutoArray extends Array<Produto> { }
interface ProdutoProps {
    produtos: ProdutoArray;
}

export default function ProdutoListagem({ produtos }: ProdutoProps) {
    const [closeModal, setCloseModal] = useState(false);
    const [produtoDetalhe, setProdutoDetalhe] = useState<Produto[]>([]);

    const detailPage = (produto: Produto) => {
        setProdutoDetalhe([{ ...produto }]);
        setCloseModal(true);
    };

    return (
        <Box className="container">
            <Dialog open={closeModal} onClose={() => setCloseModal(false)} fullWidth maxWidth="sm">
                <Box className="detail-container">
                    <IconButton className="close" onClick={() => setCloseModal(false)}>
                        <CloseIcon />
                    </IconButton>
                    {produtoDetalhe.map((item, index) => (
                        <Box className="detail-info" key={index}>
                            <CardMedia
                                component="img"
                                image={item.fotoUrl ? item.fotoUrl : ImageNotfound}
                                alt={item.nome}
                                className="img-card"
                            />
                            <Box className="product-detail">
                                <Typography variant="h5" className="modal-title">
                                    {item.nome}
                                </Typography>
                                <Typography variant="h6" className="modal-price">
                                    R$ {item.preco}
                                </Typography>
                                <Typography variant="body1" className="modal-desc">
                                    {item.descricao}
                                </Typography>
                                <Button variant="contained">Chat</Button>
                            </Box>
                        </Box>
                    ))}
                </Box>
            </Dialog>

            <Typography variant="h4" gutterBottom className="h4">
                Listagem de Produtos
            </Typography>

            <Box component="section" className="product">
                {produtos.map((produto) => (
                    <Card className="card" key={produto.id}>
                        <CardMedia
                            component="img"
                            image={produto.fotoUrl ? produto.fotoUrl : ImageNotfound}
                            alt={produto.nome}
                            className="card-img"
                        />
                        <CardContent className="detail">
                            <Box className="info">
                                <Typography variant="h6" className="title">
                                    {produto.nome}
                                </Typography>
                                <Typography variant="body2" className="price">
                                    R$ {produto.preco.toFixed(2)}
                                </Typography>
                            </Box>
                            <Button variant="outlined" onClick={() => detailPage(produto)}>
                                View Product
                            </Button>
                        </CardContent>
                    </Card>
                ))}
            </Box>
        </Box>
    );
}