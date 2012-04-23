package br.com.cbmp.ecommerce.contexto;

import org.apache.commons.lang.builder.ToStringBuilder;
import org.apache.commons.lang.builder.ToStringStyle;

import br.com.cbmp.ecommerce.pedido.IndicadorAutorizacao;

public class ConfiguracaoTransacao {
	
	private String urlRetorno;
	
	private IndicadorAutorizacao indicadorAutorizacao;
	
	private boolean capturarAutomaticamente;
	
	public ConfiguracaoTransacao(String capturarAutomaticamente) {
		this.capturarAutomaticamente = Boolean.valueOf(capturarAutomaticamente);
	}
	
	public ConfiguracaoTransacao(String indAutorizacao, String capturarAutomaticamente) {
		this(capturarAutomaticamente);
		indicadorAutorizacao = IndicadorAutorizacao.valueOf(Integer.parseInt(indAutorizacao));
	}
	
	public ConfiguracaoTransacao() {
		this.indicadorAutorizacao = IndicadorAutorizacao.NAO_AUTORIZAR;
		this.capturarAutomaticamente = false;
	}
	
	public String getUrlRetorno() {
		return urlRetorno;
	}

	public IndicadorAutorizacao getIndicadorAutorizacao() {
		return indicadorAutorizacao;
	}

	public boolean isCapturarAutomaticamente() {
		return capturarAutomaticamente;
	}
	
	public void setUrlRetorno(String urlRetorno) {
		this.urlRetorno = urlRetorno;
	}
	
	@Override
	public String toString() {
		return ToStringBuilder.reflectionToString(this, ToStringStyle.MULTI_LINE_STYLE);
	}

}
