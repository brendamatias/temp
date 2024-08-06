package com.vibbraneo.api.resource;

import java.net.URI;
import java.util.List;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.MediaType;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.CrossOrigin;
import org.springframework.web.bind.annotation.DeleteMapping;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestBody;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.RestController;

import com.vibbraneo.api.model.CategoriaModel;
import com.vibbraneo.api.service.CategoriaService;

@RestController
@RequestMapping(path = "/categorias")
@CrossOrigin(origins = "http://localhost:3000")
public class CategoriaResource {

	@Autowired
	private CategoriaService service;
	
	@GetMapping(value = "/listar-categorias", produces = MediaType.APPLICATION_JSON_VALUE )
	public ResponseEntity<?> listar(){
		try {
			 List<CategoriaModel> categorias = service.listarTodos();			
			return ResponseEntity.ok(categorias);
		} catch (Exception e) {
			return ResponseEntity.badRequest().body(e.getMessage());
		}
	}
	
	@GetMapping(value = "/buscar-categoria", 
			produces = MediaType.APPLICATION_JSON_VALUE)
	public ResponseEntity<?> buscarPorId(@RequestParam("id") Long id){
	 try {
		 CategoriaModel categoria = service.buscarPorId(id); 
		 return categoria == null ? ResponseEntity.noContent().build() : ResponseEntity.ok(categoria);
			
		} catch (Exception e) {
			 return ResponseEntity.badRequest().body(e.getMessage());
		}
	}
		

	@PostMapping(value = "/salvar-categoria", 
			produces = MediaType.APPLICATION_JSON_VALUE,
			consumes = MediaType.APPLICATION_JSON_VALUE)
	public ResponseEntity<?> salvar(@RequestBody CategoriaModel categoria){
	 try {
		 service.salvar(categoria); 
		 URI location = URI.create(String.format("/categoria/buscar-categoria?id=%s", categoria.getId()));
		 return ResponseEntity.created(location).build();
			
		} catch (Exception e) {
			return ResponseEntity.badRequest().body(e.getMessage());
		}
	}
	
	@DeleteMapping(value = "/excluir-categoria")
	public ResponseEntity<?> excluirPorId(@RequestParam("id") Long id){
	 try {
		 service.excluirPorId(id); 
		 return ResponseEntity.ok().build();
			
		} catch (Exception e) {
			 return ResponseEntity.badRequest().body(e.getMessage());
		}
	}
}
