import { Grid } from "@material-ui/core";
import { Typography } from "@mui/material";

export default function Home() {
    return (
        <>
            <Grid style={{ height: '40em', margin: 'auto', alignContent: 'center' }}>
                <h2>Home page</h2>
                <Grid>
                    <Typography>
                        Página de home de um sistema que irá permitir cadastrar, listar e buscar produtos a venda (ou troca)
                    </Typography>
                </Grid>
            </Grid>
        </>
    )
}