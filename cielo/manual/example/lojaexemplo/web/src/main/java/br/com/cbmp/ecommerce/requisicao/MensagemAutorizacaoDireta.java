package br.com.cbmp.ecommerce.requisicao;

import br.com.cbmp.ecommerce.contexto.ConfiguracaoTransacao;
import br.com.cbmp.ecommerce.contexto.Loja;
import br.com.cbmp.ecommerce.pedido.Cartao;
import br.com.cbmp.ecommerce.pedido.Pedido;

public class MensagemAutorizacaoDireta extends Mensagem {
	
	private static final String template = "" +
	"<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>" + 
	"<requisicao-autorizacao-portador id=\"%1$s\" versao=\"%2$s\">" +
		"<tid>%3$s</tid>" +
		"<dados-ec>" + 
			"<numero>%4$d</numero>" + 
			"<chave>%5$s</chave>" + 
		"</dados-ec>" +
		"<dados-cartao>" +
			"<numero>%6$s</numero>" +
			"<validade>%7$s</validade>" +
			"<indicador>%8$d</indicador>" +
			"<codigo-seguranca>%9$s</codigo-seguranca>" +
		"</dados-cartao>" +
		"<dados-pedido>" + 
			"<numero>%10$s</numero>" + 
			"<valor>%11$d</valor>" + 
			"<moeda>%12$03d</moeda>" + 
			"<data-hora>%13$tY-%13$tm-%13$tdT%13$tH:%13$tM:%13$tS</data-hora>" +
			"<descricao>%17$s</descricao>" +			
			"<idioma>PT</idioma>" +
			"</dados-pedido>" + 
		"<forma-pagamento>" +
			"<bandeira>%14$s</bandeira>" +
			"<produto>%15$s</produto>" + 
			"<parcelas>%16$s</parcelas>" + 
		"</forma-pagamento>" + 
		"<capturar-automaticamente>%18$s</capturar-automaticamente>" + 
	"</requisicao-autorizacao-portador>";		

	private Pedido pedido;
	
	private String tid;
	
	public MensagemAutorizacaoDireta(Loja loja) {
		super(loja);
	}

	@Override
	Object[] getArgumentos() {
		Cartao cartao = pedido.getCartao();
		ConfiguracaoTransacao configuracaoTransacao = pedido.getConfiguracaoTransacao();
		
		return new Object [] {
			getId(),
			getVersao(),
			
			tid,
			
			getLoja().getNumero(),
			getLoja().getChave(),
			
			cartao.getNumero(),
			cartao.getValidade(),
			cartao.getIndicadorCodigoSeguranca().getCodigo(),
			cartao.getCodigoSeguranca(),
			
			pedido.getNumero(),
			pedido.getValor(),
			986,
			pedido.getData(),
			pedido.getFormaPagamento().getBandeira().getNome(),
			pedido.getFormaPagamento().getModalidade().getCodigo(),
			pedido.getFormaPagamento().getParcelas(),
			pedido.getDescricao(),
			
			configuracaoTransacao.isCapturarAutomaticamente()			
		};
	}

	@Override
	String getTemplate() {
		return template;
	}

	public MensagemAutorizacaoDireta setPedido(Pedido pedido) {
		this.pedido = pedido;
		return this;
	}

	public MensagemAutorizacaoDireta setTid(String tid) {
		this.tid = tid;
		return this;
	}
}
