import { useEffect, useState } from "react";
import ProviderGeral from "../../../providers/providerGeral";
import { Grid, Typography } from "@mui/material";
import { DataGrid } from "@mui/x-data-grid";


interface Categoria {
  id: number;
  descricao: string;
}

export default function CategoriaList() {
  const [categorias, setCategorias] = useState<Categoria[]>([]);

  useEffect(() => {
    async function carregarCategorias() {
      try {
        const response = await ProviderGeral.listarCategorias();
        setCategorias(response.data); 
      } catch (error) {
        console.error("Erro ao carregar categorias:", error);
      }
    }

    carregarCategorias();
  }, []);


  const columns = [
    { field: 'id', headerName: 'Numero', width: 150 },
    { field: 'descricao', headerName: 'Nome', width: 300 },
  ];

  return (
    <Grid container spacing={4} className="container-categorias">
      <div style={{ marginTop: '8em',  width: '100%' }}>
        <Typography variant="h4" gutterBottom className='title'>
            Lista de Categorias 
        </Typography>
        <DataGrid
          rows={categorias} 
          columns={columns} 
          initialState={{
            pagination: {
                paginationModel: {
                pageSize: 5,
                },
            },
          }}
         pageSizeOptions={[5]}
         checkboxSelection={false}
        disableRowSelectionOnClick
        />
      </div>
    </Grid>
  );
}