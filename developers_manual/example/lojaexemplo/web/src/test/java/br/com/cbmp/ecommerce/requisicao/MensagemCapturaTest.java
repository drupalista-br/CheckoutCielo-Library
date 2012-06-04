package br.com.cbmp.ecommerce.requisicao;

import java.io.FileNotFoundException;
import java.io.IOException;

import br.com.cbmp.ecommerce.BaseTestCase;
import br.com.cbmp.ecommerce.resposta.Transacao;

public class MensagemCapturaTest extends BaseTestCase {
	
	public void testToXml() throws FileNotFoundException, IOException {
		// R$ 38,80
		long valor = 3880;
		Transacao transacao = new Transacao("12345");
		
		Mensagem mensagem = new MensagemCaptura(loja, transacao, valor);
		
		escreverParaArquivo(mensagem.toXml(), "MensagemCaptura.xml");
	}

	public void testValorZeroToXml() throws FileNotFoundException, IOException {
		long valor = 0;
		Transacao transacao = new Transacao("12345");
		
		Mensagem mensagem = new MensagemCaptura(loja, transacao, valor);
		
		escreverParaArquivo(mensagem.toXml(), "MensagemCapturaValorZero.xml");
	}
}
