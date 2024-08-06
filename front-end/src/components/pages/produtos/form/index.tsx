import { useFormik } from 'formik';
import * as Yup from 'yup';
import {
  TextField,
  Checkbox,
  Button,
  Select,
  MenuItem,
  InputLabel,
  FormControl,
  FormControlLabel,
  Grid,
  Typography,
  Box,
} from '@mui/material';
import Produto from '../../../../model/produtoModel';
import { Categoria } from '../../../../model/categoriaModel';
import { useEffect, useState } from 'react';
import ProviderGeral from '../../../../providers/providerGeral';
import DialogoConfirmacao from '../../../dialogs';

const validationSchema = Yup.object({
  nome: Yup.string().required('Nome é obrigatório'),
  descricao: Yup.string().required('Descrição é obrigatória'),
  preco: Yup.number().required('Preço é obrigatório').min(0, 'Preço não pode ser negativo'),
  contatoVendedor: Yup.string().required('Contato é obrigatório'),
  categoria: Yup.number().required('Categoria é obrigatória'),
  fotoUrl: Yup.string().required('Foto é obrigatória'), // Removi a validação de URL
});

export default function ProdutoForm() {
  const [imagePreview, setImagePreview] = useState<string | null>(null);
  const [categorias, setCategorias] = useState<Categoria[]>([]);
  const [dialogOpen, setDialogOpen] = useState(false);

  const formik = useFormik({
    initialValues: {
      id: undefined,
      nome: '',
      descricao: '',
      preco: 0,
      disponivelParaTroca: false,
      contatoVendedor: '',
      categoria: '0',
      statusVenda: 'À VENDA',
      fotoUrl: '', 
      ativo: true,
    },
    validationSchema: validationSchema,
    onSubmit: (values) => {
      const novoProduto = new Produto(
        values.nome,
        values.descricao,
        values.preco,
        values.disponivelParaTroca,
        values.contatoVendedor,
        { id: parseInt(values.categoria), descricao: '' } as Categoria,
        values.statusVenda,
        values.fotoUrl, 
        values.ativo,
        values.id,
      );
      ProviderGeral.salvarProduto(novoProduto);
    },
  });

  useEffect(() => {
    async function carregarCategorias() {
      const response = await ProviderGeral.listarCategorias();
      setCategorias(response.data);
    }
    carregarCategorias();
  }, []);
  
   const handleCloseDialog = () => {
        setDialogOpen(false);
    };


  const handleImageUpload = (event: React.ChangeEvent<HTMLInputElement>) => {
    const file = event.currentTarget.files?.[0];
    if (file) {
      const reader = new FileReader();
      reader.onloadend = () => {
        const base64String = reader.result as string;
        formik.setFieldValue("fotoUrl", base64String); 
        setImagePreview(base64String); 
      };
      reader.readAsDataURL(file);
    }
  };

  return (
    <Box sx={{ width: '100%', maxWidth: 600, margin: 'auto', padding: '20px' }}>
      <Typography variant="h4" gutterBottom>
        Cadastrar Novo Produto
      </Typography>
      <form onSubmit={formik.handleSubmit}>
        <Grid container spacing={3}>
          <Grid item xs={12}>
            <TextField
              fullWidth
              id="nome"
              name="nome"
              label="Nome"
              value={formik.values.nome}
              onChange={formik.handleChange}
              error={formik.touched.nome && Boolean(formik.errors.nome)}
              helperText={formik.touched.nome && formik.errors.nome}
            />
          </Grid>

          <Grid item xs={12}>
            <TextField
              fullWidth
              id="descricao"
              name="descricao"
              label="Descrição"
              multiline
              rows={4}
              value={formik.values.descricao}
              onChange={formik.handleChange}
              error={formik.touched.descricao && Boolean(formik.errors.descricao)}
              helperText={formik.touched.descricao && formik.errors.descricao}
            />
          </Grid>

          <Grid item xs={12}>
            <TextField
              fullWidth
              id="preco"
              name="preco"
              label="Preço"
              type="number"
              value={formik.values.preco}
              onChange={formik.handleChange}
              error={formik.touched.preco && Boolean(formik.errors.preco)}
              helperText={formik.touched.preco && formik.errors.preco}
            />
          </Grid>

          <Grid item xs={12}>
            <FormControlLabel
              control={
                <Checkbox
                  id="disponivelParaTroca"
                  name="disponivelParaTroca"
                  checked={formik.values.disponivelParaTroca}
                  onChange={formik.handleChange}
                />
              }
              label="Disponível para Troca"
            />
          </Grid>

          <Grid item xs={12}>
            <TextField
              fullWidth
              id="contatoVendedor"
              name="contatoVendedor"
              label="Contato do Vendedor"
              value={formik.values.contatoVendedor}
              onChange={formik.handleChange}
              error={formik.touched.contatoVendedor && Boolean(formik.errors.contatoVendedor)}
              helperText={formik.touched.contatoVendedor && formik.errors.contatoVendedor}
            />
          </Grid>

          <Grid item xs={12}>
            <FormControl fullWidth error={formik.touched.categoria && Boolean(formik.errors.categoria)}>
              <InputLabel id="categoria-label">Categoria</InputLabel>
              <Select
                id="categoria"
                name="categoria"
                labelId="categoria-label"
                value={formik.values.categoria}
                onChange={formik.handleChange}
                style={{width: '250px'}}
              >
                <MenuItem value={0} selected >
                  <em>Selecione uma Categoria</em>
                </MenuItem>
                {categorias.map((categoria) =>
                  <MenuItem key={categoria.id} value={categoria.id}>{categoria.descricao}</MenuItem>
                )}
              </Select>
            </FormControl>
          </Grid>

          <Grid item xs={12}>
            <FormControl fullWidth>
              <InputLabel id="statusVenda-label">Status de Venda</InputLabel>
              <Select
                id="statusVenda"
                name="statusVenda"
                labelId="statusVenda-label"
                value={formik.values.statusVenda}
                onChange={formik.handleChange}
                disabled={true}
                style={{width: '250px'}}
              >
                <MenuItem value="À VENDA">À Venda</MenuItem>
                <MenuItem value="VENDIDO">Vendido</MenuItem>
              </Select>
            </FormControl>
          </Grid>

          <Grid item xs={12} style={{ textAlign: "center", marginBottom: "20px" }}>
                <div>
                  {imagePreview && (
              <img
                src={imagePreview}
                alt="Pré-visualização"
                style={{
                  width: "350px",
                  height: "350px",
                  objectFit: "cover", 
                  borderRadius: "10px", 
                  margin: "40px",
                  boxShadow: "0 4px 8px rgba(0, 0, 0, 0.5)", // Sombra leve para destaque
                }}
              />
            )}
                </div>
            
            <label htmlFor="fotoUrl">
              <input
                accept="image/*"
                id="fotoUrl"
                name="fotoUrl"
                type="file"
                onChange={handleImageUpload}
                style={{ display: "none" }} // Esconde o input real
              />
              <Button
                variant="contained"
                component="span"
                color="primary"
                style={{ marginBottom: "10px" }}
              >
                Escolher imagem
              </Button>
            </label>
            {formik.touched.fotoUrl && formik.errors.fotoUrl && (
              <Typography color="error">{formik.errors.fotoUrl}</Typography>
            )}
            
          </Grid>

          <Grid item xs={12}>
            <FormControlLabel
              control={
                <Checkbox
                  id="ativo"
                  name="ativo"
                  checked={formik.values.ativo}
                  onChange={formik.handleChange}
                />
              }
              label="Ativo"
            />
          </Grid>

          <Grid item xs={12}>
            <Button color="primary" variant="contained" fullWidth type="submit">
              Cadastrar Produto
            </Button>
          </Grid>
        </Grid>
      </form>

        <DialogoConfirmacao
                open={dialogOpen}
                mensagem="Link do produto copiado!"
                onClose={handleCloseDialog}
            />
    </Box>
  );
}