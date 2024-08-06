export class Categoria {
  descricao: string;
  id?: number;

  constructor(descricao: string, id?: number) {
    this.descricao = descricao;
    this.id = id; 
  }
}