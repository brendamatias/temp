import { Categoria } from "./categoriaModel";

export default class Produto {
  nome: string;
  descricao: string;
  preco: number;
  disponivelParaTroca: boolean;
  contatoVendedor: string;
  categoria: Categoria;
  statusVenda: string;
  fotoUrl: string;
  ativo: boolean;
  id?: number;

  constructor(
    nome: string,
    descricao: string,
    preco: number,
    disponivelParaTroca: boolean,
    contatoVendedor: string,
    categoria: Categoria,
    statusVenda: string,
    fotoUrl: string,
    ativo: boolean,
     id?: number,
  ) {
    this.nome = nome;
    this.descricao = descricao;
    this.preco = preco;
    this.disponivelParaTroca = disponivelParaTroca;
    this.contatoVendedor = contatoVendedor;
    this.categoria = categoria;
    this.statusVenda = statusVenda;
    this.fotoUrl = fotoUrl;
    this.ativo = ativo;
    this.id = id;
  }

}