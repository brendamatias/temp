
import { useEffect, useState } from 'react';
import ProdutoProvider from '../../../providers/providerGeral';
import  Produto from '../../../model/produtoModel';
import './styles.css'; 
import { Grid, Grid2 } from '@mui/material';
import ProdutoCard from './cards';

export default function ProdutoPage(){

    const [produtos, setProdutos] = useState<Produto[]>([]);

    useEffect(()=>{
        async function buscarLista(){
            const response = await ProdutoProvider.listarProdutos();
            setProdutos(response.data);
        }

        buscarLista();
    }, []);

    return (
        <>
        <Grid spacing={4} className='container-produtos'>
                <ProdutoCard produtos={produtos} />
                {/* <ProdutoListagem produtos={produtos}/> */}
        </Grid>
        </>
    )
}