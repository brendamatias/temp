import { Grid } from "@material-ui/core";
import { Typography } from "@mui/material";
import ErrorOutlineIcon from '@mui/icons-material/ErrorOutline';

export default function ErrorPage() {
    return (
        <Grid style={{alignContent: 'center', margin: 'auto', height: '40em', textAlign: 'center'}}>
            <ErrorOutlineIcon/>
            <Typography>
                 ERRROR 404
            </Typography>
        </Grid>
    )
}