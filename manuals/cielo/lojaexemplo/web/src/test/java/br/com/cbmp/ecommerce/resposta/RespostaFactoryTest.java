package br.com.cbmp.ecommerce.resposta;

import br.com.cbmp.ecommerce.BaseTestCase;

public class RespostaFactoryTest extends BaseTestCase {
	
	public void testMensagemErro99() {
		final String retorno = "" +
			"<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>" +
			"<erro>" +
				"<codigo>099</codigo>" +
				"<mensagem>[-3f3b3124:1261b4f933b:-7fda]Erro inesperado</mensagem>" +
			"</erro>";
		
		
		Resposta resposta = RespostaFactory.getInstance().criar(retorno);
		
		Erro erro = (Erro) resposta;
		
		assertEquals(99, erro.getCodigo());
		assertEquals("[-3f3b3124:1261b4f933b:-7fda]Erro inesperado", erro.getMensagem());
	}

}
