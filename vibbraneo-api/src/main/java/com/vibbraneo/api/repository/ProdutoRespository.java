package com.vibbraneo.api.repository;

import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;

import com.vibbraneo.api.model.ProdutoModel;

import jakarta.transaction.Transactional;

@Transactional
public interface ProdutoRespository extends JpaRepository<ProdutoModel, Long>{
	
	@Query(value = "SELECT * FROM PRODUTO WHERE ID = :id", nativeQuery = true)
	ProdutoModel buscarPorId(Long id);

}
