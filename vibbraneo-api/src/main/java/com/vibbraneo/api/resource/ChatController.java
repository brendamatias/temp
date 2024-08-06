package com.vibbraneo.api.resource;

import org.springframework.messaging.handler.annotation.MessageMapping;
import org.springframework.messaging.handler.annotation.SendTo;
import org.springframework.web.bind.annotation.RestController;

import com.vibbraneo.api.model.Mensagem;

@RestController
public class ChatController {

	@MessageMapping("/app/chatmessage")
	@SendTo("/chat")
	public Mensagem sendMessage(Mensagem mensagem) {
		System.out.println("mensagem: "+ mensagem.getMsg());
		return mensagem;
	}
}
