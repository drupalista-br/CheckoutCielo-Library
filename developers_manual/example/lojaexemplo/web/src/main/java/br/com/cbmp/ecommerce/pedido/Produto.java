package br.com.cbmp.ecommerce.pedido;

import org.apache.commons.lang.builder.ToStringBuilder;
import org.apache.commons.lang.builder.ToStringStyle;

public class Produto {
	
	private long id;
	
	private String descricao;
	
	private long valor;

	public Produto(long id, String descricao, long valor) {
		super();
		this.id = id;
		this.descricao = descricao;
		this.valor = valor;
	}

	public long getId() {
		return id;
	}

	public String getDescricao() {
		return descricao;
	}

	public long getValor() {
		return valor;
	}
	
	@Override
	public String toString() {
		return ToStringBuilder.reflectionToString(this, ToStringStyle.MULTI_LINE_STYLE);
	}

}
