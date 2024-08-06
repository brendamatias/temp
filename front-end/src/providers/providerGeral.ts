import axios from "axios";
import Produto from "../model/produtoModel";
import { Categoria } from "../model/categoriaModel";


const URL_BASE = 'http://localhost:8080';

export default class ProviderGeral{


    //endpoints de produtos
    static async listarProdutos() : Promise<any> {
        return axios.get(`${URL_BASE}/produtos/listar-todos`);
    }

    static async buscarProduto(id: Number) : Promise<any> {
        return axios.get(`${URL_BASE}/produtos/buscar-produto?id=${id}`);
    }

    static salvarProduto(entity: Produto): Promise<any> {
        const data = this.mapearProduto(entity);
        return axios.post(`${URL_BASE}/produtos/salvar-produto`, data,
             {
                 headers: {
                    'Content-Type': 'application/json',
                 }
             }
        );
    }

    static deletarProduto(id: Number): Promise<any> {
        return axios.delete(`${URL_BASE}/produtos/excluir-produto/${id}`);
    }

    static mapearProduto(entity: Produto): any {
        const produto = 
            new Produto(
                entity.nome,
                entity.descricao,
                entity.preco,
                entity.disponivelParaTroca,
                entity.contatoVendedor,
                entity.categoria,
                entity.statusVenda,
                entity.fotoUrl,
                entity.ativo,
                entity.id,
            );

        return produto;
    }

//// endpoints de categoria:

    static async listarCategorias() : Promise<any> {
        return axios.get(`${URL_BASE}/categorias/listar-categorias`);
    }

    static salvarCategoria(entity: Categoria): Promise<any> {
        const data = JSON.stringify(this.mapearCategoria(entity));
        return axios.post(`${URL_BASE}/categorias/salvar-categoria`, data,
             {
                 headers: {
                    'Content-Type': 'application/json',
                 }
             }
        );
    }

    static deletarCategoria(id: Number): Promise<any> {
        return axios.delete(`${URL_BASE}/categorias/excluir-categoria/${id}`);
    }

    static mapearCategoria(entity: Categoria): any {
        const categoria = 
            new Categoria(
                entity.descricao,
                entity.id,
            );

        return categoria;
    }
}