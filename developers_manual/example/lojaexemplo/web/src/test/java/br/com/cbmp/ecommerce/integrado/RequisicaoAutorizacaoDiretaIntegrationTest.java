package br.com.cbmp.ecommerce.integrado;

import br.com.cbmp.ecommerce.BaseTestCase;
import br.com.cbmp.ecommerce.contexto.Destino;
import br.com.cbmp.ecommerce.contexto.Loja;
import br.com.cbmp.ecommerce.pedido.Bandeira;
import br.com.cbmp.ecommerce.pedido.Cartao;
import br.com.cbmp.ecommerce.pedido.FormaPagamento;
import br.com.cbmp.ecommerce.pedido.Modalidade;
import br.com.cbmp.ecommerce.pedido.Pedido;
import br.com.cbmp.ecommerce.pedido.Produto;
import br.com.cbmp.ecommerce.requisicao.MensagemAutorizacaoDireta;
import br.com.cbmp.ecommerce.requisicao.MensagemTid;
import br.com.cbmp.ecommerce.requisicao.Requisicao;
import br.com.cbmp.ecommerce.resposta.FalhaComunicaoException;
import br.com.cbmp.ecommerce.resposta.Transacao;

public class RequisicaoAutorizacaoDiretaIntegrationTest extends BaseTestCase {
	
	public void testAutorizacaoDireta() throws FalhaComunicaoException {
		Loja loja = Loja.leituraCartaoLoja();
		Produto produto = new Produto(23434L, "TV", 1500);
		FormaPagamento formaPagamento = new FormaPagamento(Modalidade.PARCELADO_LOJA, 3, Bandeira.VISA);
		
		Pedido pedido = new Pedido(produto, formaPagamento);
		pedido.setConfiguracaoTransacao(configuracaoTransacao);
		Cartao cartao = new Cartao("4551870000000183", "201501", "585");
		pedido.setCartao(cartao);
		
		MensagemTid mensagemTid = new MensagemTid(loja, formaPagamento);
		Requisicao requisicaoTid = new Requisicao(mensagemTid);
		Transacao transacaoTid = requisicaoTid.enviarPara(destino);
		
		String tid = transacaoTid.getTid();
		
		MensagemAutorizacaoDireta mensagem = new MensagemAutorizacaoDireta(loja)
			.setPedido(pedido)
			.setTid(tid);
		
		Requisicao requisicao = new Requisicao(mensagem);
		
		Destino destino = new Destino();
		Transacao transacao = requisicao.enviarPara(destino);
		
		getLogger().info(transacao);
	}

}
