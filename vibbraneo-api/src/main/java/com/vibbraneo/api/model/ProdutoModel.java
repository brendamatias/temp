package com.vibbraneo.api.model;

import com.fasterxml.jackson.annotation.JsonProperty;

import jakarta.persistence.Column;
import jakarta.persistence.Entity;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.GenerationType;
import jakarta.persistence.Id;
import jakarta.persistence.JoinColumn;
import jakarta.persistence.ManyToOne;

@Entity(name = "PRODUTO")
public class ProdutoModel {

	@Id
	@GeneratedValue(strategy = GenerationType.IDENTITY)
	@Column(name = "ID")
	@JsonProperty(value = "id")
	private Long id;
	
	@Column(name = "NOME")
	@JsonProperty(value = "nome")
    private String nome;
	
	@Column(name = "DESCRICAO")
	@JsonProperty(value = "descricao")
    private String descricao;
	
	@Column(name = "PRECO")
	@JsonProperty(value = "preco")
    private double preco;
	
	@Column(name = "DISP_TROCA")
	@JsonProperty(value = "disponivelParaTroca")
    private boolean disponivelParaTroca;
	
	@Column(name = "CONTATO")
	@JsonProperty(value = "contatoVendedor")
    private String contatoVendedor;
	
	@ManyToOne
	@JoinColumn(name = "ID_CATEGORIA")
	@JsonProperty(value = "categoria")
    private CategoriaModel categoria;
	
	@Column(name = "STATUS_VENDA")
	@JsonProperty(value = "statusVenda")
    private String statusVenda;
	
	@Column(name = "FOTO")
	@JsonProperty(value = "fotoUrl")
    private String fotoUrl;
	
	@Column(name = "ATIVO")
	@JsonProperty(value = "ativo")
	private Boolean ativo;
    
    
    
	public Boolean getAtivo() {
		return ativo;
	}
	public void setAtivo(Boolean ativo) {
		this.ativo = ativo;
	}
	public void setCategoria(CategoriaModel categoria) {
		this.categoria = categoria;
	}
	public Long getId() {
		return id;
	}
	public void setId(Long id) {
		this.id = id;
	}
	public String getNome() {
		return nome;
	}
	public void setNome(String nome) {
		this.nome = nome;
	}
	public String getDescricao() {
		return descricao;
	}
	public void setDescricao(String descricao) {
		this.descricao = descricao;
	}
	public double getPreco() {
		return preco;
	}
	public void setPreco(double preco) {
		this.preco = preco;
	}
	public boolean isDisponivelParaTroca() {
		return disponivelParaTroca;
	}
	public void setDisponivelParaTroca(boolean disponivelParaTroca) {
		this.disponivelParaTroca = disponivelParaTroca;
	}
	public String getContatoVendedor() {
		return contatoVendedor;
	}
	public void setContatoVendedor(String contatoVendedor) {
		this.contatoVendedor = contatoVendedor;
	}
	public CategoriaModel getCategoria() {
		return categoria;
	}
	public void setCategorias(CategoriaModel categoria) {
		this.categoria = categoria;
	}
	public String getStatusVenda() {
		return statusVenda;
	}
	public void setStatusVenda(String statusVenda) {
		this.statusVenda = statusVenda;
	}
	public String getFotoUrl() {
		return fotoUrl;
	}
	public void setFotoUrl(String fotoUrl) {
		this.fotoUrl = fotoUrl;
	}
    
}
