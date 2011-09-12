package br.com.cbmp.ecommerce.pedido;

public enum Bandeira {
	
	VISA("visa"),
	MASTERCARD("mastercard"),
	ELO("elo")
	;
	
	private String nome;
	
	private Bandeira(String nome) {
		this.nome = nome;
	}
	
	public static Bandeira valueOf(int codigo) {
		switch (codigo) {
		case 1:
			return VISA;
		case 2:
			return MASTERCARD;
		case 3:
			return ELO;
		default:
			throw new IllegalArgumentException("Código '" + codigo + "' de bandeira não suportado.");
		}
	}

	public String getNome() {
		return nome;
	}

	@Override
	public String toString() {
		return nome;
	}
	
	
	

}
