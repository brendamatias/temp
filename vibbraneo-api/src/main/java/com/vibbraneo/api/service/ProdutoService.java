package com.vibbraneo.api.service;

import java.util.List;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

import com.vibbraneo.api.model.ProdutoModel;
import com.vibbraneo.api.repository.ProdutoRespository;

@Service
public class ProdutoService {

	@Autowired
	private ProdutoRespository repository;
	
	
	public List<ProdutoModel> listarTodos(){
		return repository.findAll();
	}


	public ProdutoModel buscarPorId(Long id) {  
		return repository.buscarPorId(id);
	}

	public void salvar(ProdutoModel produto) {
		repository.save(produto);
	} 

	public void excluirPorId(Long id) {
		 repository.deleteById(id);
	}
}
