package com.vibbraneo.api.repository;

import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;

import com.vibbraneo.api.model.CategoriaModel;

import jakarta.transaction.Transactional;

@Transactional
public interface CategoriaRepository extends JpaRepository<CategoriaModel, Long>{

	@Query(value = "SELECT * FROM CATEGORIAS WHERE ID = :id", nativeQuery = true)
	CategoriaModel buscarPorId(Long id);

}
