package br.com.cbmp.ecommerce.requisicao;

import java.util.ArrayList;
import java.util.List;

import br.com.cbmp.ecommerce.contexto.ConfiguracaoTransacao;
import br.com.cbmp.ecommerce.contexto.Loja;
import br.com.cbmp.ecommerce.pedido.Cartao;
import br.com.cbmp.ecommerce.pedido.Pedido;

public class MensagemNovaTransacao extends Mensagem {
	
	private static final String parte_1 = "" +
	"<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>" + 
	"<requisicao-transacao id=\"%1$s\" versao=\"%2$s\">" + 
		"<dados-ec>" + 
		"	<numero>%3$d</numero>" + 
		"	<chave>%4$s</chave>" + 
		"</dados-ec>";
	private static final String parte_2 = "" +
		"<dados-portador>" +
		"	<numero>%16$s</numero>" +
		"	<validade>%17$s</validade>" +
		"	<indicador>%18$d</indicador>" +
		"	<codigo-seguranca>%19$s</codigo-seguranca>" +
		"</dados-portador>";
	private static final String parte_3 = "" +
		"<dados-pedido>" + 
			"<numero>%5$s</numero>" + 
			"<valor>%6$d</valor>" + 
			"<moeda>%7$03d</moeda>" + 
			"<data-hora>%8$tY-%8$tm-%8$tdT%8$tH:%8$tM:%8$tS</data-hora>" +
			"<descricao>%15$s</descricao>" +			
			"<idioma>PT</idioma>" +
		"</dados-pedido>" + 
		"<forma-pagamento>" + 
			"<bandeira>%9$s</bandeira>" +		
			"<produto>%10$s</produto>" + 
			"<parcelas>%11$s</parcelas>" + 
		"</forma-pagamento>" + 
		"<url-retorno>%12$s</url-retorno>" + 
		"<autorizar>%13$d</autorizar>" + 
		"<capturar>%14$s</capturar>" +
	"</requisicao-transacao>";	
	
	private Pedido pedido;
	
	public MensagemNovaTransacao(Loja loja, Pedido pedido) {
		super(loja);
		this.pedido = pedido;
	}

	String getTemplate() {
		if (pedido.temCartao()) {
		return parte_1 + parte_2 + parte_3;
		}
		else { 
			return parte_1 + parte_3;
		}
	}

	Object[] getArgumentos() {
		ConfiguracaoTransacao configuracaoTransacao = pedido.getConfiguracaoTransacao();
		
		List<Object> argumentos = new ArrayList<Object>();
		
		argumentos.add(0, getId());
		argumentos.add(1, getVersao());
		argumentos.add(2, getLoja().getNumero());
		argumentos.add(3, getLoja().getChave());
		argumentos.add(4, pedido.getNumero());
		argumentos.add(5, pedido.getValor());
		argumentos.add(6, 986);
		argumentos.add(7, pedido.getData());
		argumentos.add(8, pedido.getFormaPagamento().getBandeira().getNome());
		argumentos.add(9, pedido.getFormaPagamento().getModalidade().getCodigo());
		argumentos.add(10, pedido.getFormaPagamento().getParcelas());
		argumentos.add(11, configuracaoTransacao.getUrlRetorno());
		argumentos.add(12, configuracaoTransacao.getIndicadorAutorizacao().getCodigo());
		argumentos.add(13, configuracaoTransacao.isCapturarAutomaticamente());
		argumentos.add(14, pedido.getDescricao());
		
		if (pedido.getCartao() != null) {
			Cartao cartao = pedido.getCartao();
			
			argumentos.add(15, cartao.getNumero());
			argumentos.add(16, cartao.getValidade());
			argumentos.add(17, cartao.getIndicadorCodigoSeguranca().getCodigo());
			argumentos.add(18, cartao.getCodigoSeguranca());
		}
		
			
		return argumentos.toArray();
	}

}
