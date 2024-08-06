import { useState } from "react";
import { Categoria } from "../../../../model/categoriaModel";
import { Box, Button, Container, Grid, TextField, Typography } from "@mui/material";
import ProviderGeral from "../../../../providers/providerGeral";

export default function CategoriaForm(){
  const [descricao, setDescricao] = useState<string>('');
  const [success, setSuccess] = useState<boolean>(false);

  const handleSubmit = (event: React.FormEvent) => {
    event.preventDefault();

    try {
        const entity = new Categoria(descricao);
        const response = ProviderGeral.salvarCategoria(entity);
        console.log(response);
    } catch (error) {
        
    }


    // Simulação de sucesso
    setSuccess(true);

    // Limpa o campo
    setDescricao('');
  };

  return (
    <Container maxWidth="sm">
      <Box sx={{ marginTop: 4, padding: 4, backgroundColor: '#f5f5f5', borderRadius: 2 }}>
        <Typography variant="h5" gutterBottom>
          Cadastro de Categoria
        </Typography>
        <form onSubmit={handleSubmit}>
          <Grid container spacing={2}>
            <Grid item xs={12}>
              <TextField
                label="Descrição da Categoria"
                variant="outlined"
                fullWidth
                required
                value={descricao}
                onChange={(e) => setDescricao(e.target.value)}
              />
            </Grid>

            <Grid item xs={12}>
              <Button type="submit" variant="contained" color="primary" fullWidth>
                Cadastrar Categoria
              </Button>
            </Grid>

            {success && (
              <Grid item xs={12}>
                <Typography variant="body1" color="success">
                  Categoria cadastrada com sucesso!
                </Typography>
              </Grid>
            )}
          </Grid>
        </form>
      </Box>
    </Container>
  );
}