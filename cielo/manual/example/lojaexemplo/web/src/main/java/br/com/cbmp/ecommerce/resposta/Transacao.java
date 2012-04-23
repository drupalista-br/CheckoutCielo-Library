package br.com.cbmp.ecommerce.resposta;

import org.apache.commons.lang.builder.ToStringBuilder;
import org.apache.commons.lang.builder.ToStringStyle;

import br.com.cbmp.ecommerce.pedido.StatusTransacao;



public class Transacao extends Resposta {

	class FormaPagamento {
		String bandeira;
		String produto;
		short parcelas;
	}
	
	public class DadosPedido {
		String numero;
		long valor;
		short moeda;
		String dataHora;
		String idioma;
		String descricao;
		
		public String getNumero() {
			return numero;
		}
		
		public long getValor() {
			return valor;
		}
	}
	
	public class Processamento {
		short codigo;
		String mensagem;
		String dataHora;
		long valor;
	}
	
	public class Autenticacao extends Processamento {
		short eci;
	}
	
	public class Autorizacao extends Processamento {
		String lr;
		String codigoAutorizacao;
		String arp;
		
		public String getLr() {
			return lr;
		}
		
		public String getCodigoAutorizacao() {
			return codigoAutorizacao;
		}
		
		public String getArp() {
			return arp;
		}
	}
	
	public class Captura extends Processamento {
	}
	
	public class Cancelamento extends Processamento {
	}
	
	private String urlAutenticacao;
	
	private String tid;

	private short status;
	
	private Autorizacao autorizacao;
	
	private Autenticacao autenticacao;
	
	private Cancelamento cancelamento;
	
	private Captura captura;
	
	private DadosPedido dadosPedido;
	
	private FormaPagamento formaPagamento;
	
	private String pan;
	
	public Transacao(String tid) {
		this.tid = tid;
	}
	
	public String toString() {
		return ToStringBuilder.reflectionToString(this, ToStringStyle.MULTI_LINE_STYLE);
	}

	public String getUrlAutenticacao() {
		return urlAutenticacao;
	}

	public String getTid() {
		return tid;
	}

	public short getStatus() {
		return status;
	}
	
	public StatusTransacao getStatusTransacao() {
		return StatusTransacao.valueOf(getStatus());
	}

	public Autorizacao getAutorizacao() {
		return autorizacao;
	}

	public DadosPedido getDadosPedido() {
		return dadosPedido;
	}

	public FormaPagamento getFormaPagamento() {
		return formaPagamento;
	}

	protected Autenticacao getAutenticacao() {
		return autenticacao;
	}

	protected String getPan() {
		return pan;
	}

	public Cancelamento getCancelamento() {
		return cancelamento;
	}

	public Captura getCaptura() {
		return captura;
	}
	
}
