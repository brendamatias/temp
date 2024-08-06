package com.vibbraneo.api.service;

import java.util.List;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

import com.vibbraneo.api.model.CategoriaModel;
import com.vibbraneo.api.repository.CategoriaRepository;

@Service
public class CategoriaService {


	@Autowired
	private CategoriaRepository repository;
	

	public List<CategoriaModel> listarTodos() {
		return repository.findAll();
	}

	public CategoriaModel buscarPorId(Long id) {
		return repository.buscarPorId(id);
	}

	public void salvar(CategoriaModel categoria) {
		repository.save(categoria);
	}

	public void excluirPorId(Long id) {
		 repository.deleteById(id);
	}
}
