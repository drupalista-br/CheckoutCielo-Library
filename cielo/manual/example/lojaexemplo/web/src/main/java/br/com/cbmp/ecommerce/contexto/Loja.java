package br.com.cbmp.ecommerce.contexto;

import org.apache.commons.lang.builder.ToStringBuilder;
import org.apache.commons.lang.builder.ToStringStyle;

public class Loja {
	
	private long numero;
	
	private String chave;
	
	public Loja(long numero, String chave) {
		this.numero = numero;
		this.chave = chave;
	}

	public static Loja leituraCartaoCielo() {
		return valueOf(1001734898L);
	}
	
	public static Loja leituraCartaoLoja() {
		return valueOf(1006993069L);
	}
	
	public long getNumero() {
		return numero;
	}

	public String getChave() {
		return chave;
	}
	
	public static Loja valueOf(long numeroLoja) {
		if (numeroLoja == 1001734898L) {
			return new Loja(numeroLoja, "e84827130b9837473681c2787007da5914d6359947015a5cdb2b8843db0fa832");
		}
		
		if (numeroLoja == 1006993069L) {
			return new Loja(numeroLoja, "25fbb99741c739dd84d7b06ec78c9bac718838630f30b112d033ce2e621b34f3");
		}
		
		// retorna outra qualquer qualquer
		return new Loja(numeroLoja, String.valueOf(System.currentTimeMillis()));
	}
	
	@Override
	public String toString() {
		return ToStringBuilder.reflectionToString(this, ToStringStyle.MULTI_LINE_STYLE);
	}

	@Override
	public int hashCode() {
		final int prime = 31;
		int result = 1;
		result = prime * result + ((chave == null) ? 0 : chave.hashCode());
		result = prime * result + (int) (numero ^ (numero >>> 32));
		return result;
	}

	@Override
	public boolean equals(Object obj) {
		if (this == obj)
			return true;
		if (obj == null)
			return false;
		if (getClass() != obj.getClass())
			return false;
		final Loja other = (Loja) obj;
		if (chave == null) {
			if (other.chave != null)
				return false;
		} else if (!chave.equals(other.chave))
			return false;
		if (numero != other.numero)
			return false;
		return true;
	}



}
