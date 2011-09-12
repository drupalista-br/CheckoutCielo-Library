package br.com.cbmp.ecommerce.contexto;

import org.apache.commons.lang.builder.ToStringBuilder;
import org.apache.commons.lang.builder.ToStringStyle;

public class Destino {
	
	private String url;
	
	public Destino(String url) {
		this.url = url;
	}
	
	public Destino() {
		this.url = "https://qasecommerce.cielo.com.br/servicos/ecommwsec.do";
//		this.url = "https://ecommerce.cbmp.com.br/servicos/ecommwsec.do";
	}

	public String getUrl() {
		return url;
	}
	
	@Override
	public String toString() {
		return ToStringBuilder.reflectionToString(this, ToStringStyle.MULTI_LINE_STYLE);
	}

}
