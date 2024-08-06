import { useEffect, useState } from "react";
import { Grid, Typography, Button, Card, CardContent, CardMedia, Snackbar, Tooltip, IconButton } from "@mui/material";
import ProviderGeral from "../../../../providers/providerGeral";
import { useParams } from "react-router-dom";
import ImageNotfound from '../../../../assets/not-found-image.png';
import ScreenShareIcon from '@mui/icons-material/ScreenShare';
import { useSnackbar } from 'notistack';
import DialogoConfirmacao from "../../../dialogs";

export default function ProdutoDetalhes() {
    const { id } = useParams();
    const [produto, setProduto] = useState();
    const { enqueueSnackbar } = useSnackbar();
    const [dialogOpen, setDialogOpen] = useState(false);

    useEffect(() => {
        async function buscarProduto() {
            const response = await ProviderGeral.buscarProduto(id);
            setProduto(response.data);
        }
        buscarProduto();
    }, [id]);

    const handleCloseDialog = () => {
        setDialogOpen(false);
    };

    const handleCopiarLink = () => {
        const linkProduto = `https://ecommerce.com/produto/${id}`; // Link fictício
        navigator.clipboard.writeText(linkProduto).then(() => {
            enqueueSnackbar('Link copiado para a área de transferência!', { variant: 'success' });
        }).catch(err => {
            console.error('Erro ao copiar o link: ', err);
        });
        setDialogOpen(true);
    };

    return (
        <>
            {produto && (
                <Grid container spacing={6} style={{ padding: "20px", flexDirection: 'column', width: '100%' }}>
                    <Grid item xs={2} sm={6} spacing={5} style={{ margin: 'auto' }}>
                        <Card>
                            <CardMedia
                                component="img"
                                height="300"
                                image={produto.fotoUrl ? produto.fotoUrl : ImageNotfound}
                                alt={produto.nome}
                                style={{ objectFit: "cover" }}
                            />
                        </Card>
                    </Grid>

                    <Grid item xs={12} sm={6} style={{ margin: 'auto' }}>
                        <Card>
                            <CardContent>
                                <Typography variant="h5" component="h2" gutterBottom>
                                    {produto.nome}
                                </Typography>

                                <Typography variant="body1" color="textSecondary" gutterBottom>
                                    {produto.descricao}
                                </Typography>

                                <Typography variant="body2" color="textSecondary" gutterBottom>
                                    <strong>Preço: </strong> R$ {produto.preco.toFixed(2)}
                                </Typography>

                                <Typography variant="body2" color="textSecondary" gutterBottom>
                                    <strong>Disponível para troca: </strong> {produto.disponivelParaTroca ? "Sim" : "Não"}
                                </Typography>

                                <Typography variant="body2" color="textSecondary" gutterBottom>
                                    <strong>Contato do vendedor: </strong> {produto.contatoVendedor}
                                </Typography>

                                <Typography variant="body2" color="textSecondary" gutterBottom>
                                    <strong>Categoria: </strong> {produto.categoria.descricao || "Não especificada"}
                                </Typography>

                                <Typography variant="body2" color="textSecondary" gutterBottom>
                                    <strong>Status de Venda: </strong> {produto.statusVenda}
                                </Typography>

                                <Typography variant="body2" color="textSecondary">
                                    <strong>Ativo: </strong> {produto.ativo ? "Sim" : "Não"}
                                </Typography>

                                <Button
                                    variant="contained"
                                    color="primary"
                                    style={{ marginTop: "20px" }}
                                    onClick={() => alert("Contato realizado!")}
                                >
                                    Contatar Vendedor
                                </Button>


                                <Tooltip title="Compartilhar">
                                    <IconButton>
                                        <ScreenShareIcon onClick={handleCopiarLink} />
                                    </IconButton>
                                </Tooltip>

                            </CardContent>
                        </Card>
                    </Grid>
                </Grid>
            )}

            <DialogoConfirmacao
                open={dialogOpen}
                mensagem="Link do produto copiado!"
                onClose={handleCloseDialog}
            />
        </>
    );
}