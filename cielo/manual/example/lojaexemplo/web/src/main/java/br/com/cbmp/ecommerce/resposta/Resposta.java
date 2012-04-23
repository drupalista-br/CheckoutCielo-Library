package br.com.cbmp.ecommerce.resposta;

public abstract class Resposta {
	
	private String conteudo;
	
	public String getConteudo() {
		return conteudo;
	}

	public void setConteudo(String xml) {
		this.conteudo = xml;
	}
}
