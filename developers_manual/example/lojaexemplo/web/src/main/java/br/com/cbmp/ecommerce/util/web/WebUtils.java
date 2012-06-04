package br.com.cbmp.ecommerce.util.web;

import javax.servlet.http.HttpServletRequest;

import org.apache.log4j.Logger;

import br.com.cbmp.ecommerce.contexto.ConfiguracaoTransacao;
import br.com.cbmp.ecommerce.pedido.Cartao;
import br.com.cbmp.ecommerce.pedido.FormaPagamento;
import br.com.cbmp.ecommerce.pedido.Pedido;
import br.com.cbmp.ecommerce.pedido.Produto;
import br.com.cbmp.ecommerce.util.Pagamento;
import br.com.cbmp.ecommerce.util.Pedidos;
import br.com.cbmp.ecommerce.util.Produtos;

public class WebUtils {
	
	private static final Logger logger = Logger.getLogger(WebUtils.class);
	
	private HttpServletRequest request;
	
	private Pedidos pedidos;
	
	public WebUtils(HttpServletRequest request) {
		this.request = request;
		pedidos = (Pedidos) request.getSession().getAttribute("pedidos");
	}
	
	public Pedido criarPedido() {
		Produto produto = getProduto();
		FormaPagamento formaPagamento = getFormaPagamento();

		Pedido pedido = new Pedido(produto, formaPagamento);
		pedido.setConfiguracaoTransacao(getConfiguracaoTransacao());
		
		pedido.setCartao(getCartao());
		
		pedido.setDescricao("[origem:" + request.getRemoteAddr() + "]");
		
		armazenarPedido(pedido);
		
		if (logger.isDebugEnabled()) {
			logger.debug("Pedido criado " + pedido);
		}
		
		return pedido;
	}
	
	public Cartao getCartao() {
		String numeroCartao = request.getParameter("cartao.numero");
		
		if (numeroCartao == null) {
			return null;
		}
		else {
			String validade = request.getParameter("cartao.validade");
			String codigoSeguranca = request.getParameter("cartao.codigoSeguranca");
			Cartao cartao = new Cartao(numeroCartao, validade, codigoSeguranca);
			return cartao;
		}
	}	
	
	public Pedido recuperarUltimoPedido() {
		String numeroPedido = (String) request.getSession().getAttribute("numeroPedido");

		if (numeroPedido == null) {
			throw new IllegalStateException("Pedido não encontrado!");
		}
		
		return pedidos.recuperar(numeroPedido);
	}
	
	public Pedidos getPedidos() {
		return pedidos;
	}

	private Produto getProduto() {
		String idProduto = request.getParameter("produto");
		Produto produto = Produtos.recuperar(Long.valueOf(idProduto));
		return produto;
	}

	private FormaPagamento getFormaPagamento() {
		String frPagamento = request.getParameter("formaPagamento");
		String tipoParcelamento = request.getParameter("tipoParcelamento");		
		String codigoBandeira = request.getParameter("codigoBandeira");
		FormaPagamento formaPagamento = Pagamento.inferirFormaPagamento(frPagamento, tipoParcelamento, codigoBandeira);
		return formaPagamento;
	}
	
	private void armazenarPedido(Pedido pedido) {
		pedidos.adicionar(pedido);
		request.getSession().setAttribute("numeroPedido", pedido.getNumero());
	}
	
	private ConfiguracaoTransacao getConfiguracaoTransacao() {		
		String indAutorizacao = request.getParameter("indicadorAutorizacao");
		String capturarAutomaticamente = request.getParameter("capturarAutomaticamente");
		
		ConfiguracaoTransacao configuracaoTransacao;
		
		if (indAutorizacao != null) {
			configuracaoTransacao = new ConfiguracaoTransacao(indAutorizacao, capturarAutomaticamente);
		}
		else {
			configuracaoTransacao = new ConfiguracaoTransacao(capturarAutomaticamente);
		}
		
		configuracaoTransacao.setUrlRetorno(getUrlRetorno());
		
		return configuracaoTransacao;
	}

	private String getUrlRetorno() {
		String scheme = request.getScheme();
		String localAddress = request.getLocalAddr();
		int localPort = request.getLocalPort();
		String contextPath = request.getContextPath();
		
		StringBuilder builder = new StringBuilder();
		builder
			.append(scheme)
			.append("://")
			.append(localAddress)
			.append(":")
			.append(localPort)
			.append(contextPath)
			.append("/retorno.jsp");
		
		return builder.toString();
	}
}
