package com.vibbraneo.api.resource;

import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;

@RestController
@RequestMapping(path = "/")
public class VibbraneoApiResource {

	@GetMapping
	public String getIndexAplication() {
		return "Hello";
	}
}
