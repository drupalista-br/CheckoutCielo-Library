package br.com.cbmp.ecommerce.util;

import java.util.Collection;
import java.util.LinkedHashMap;
import java.util.Map;

import br.com.cbmp.ecommerce.pedido.Produto;

public class Produtos {
	
	private static final Map<Long, Produto> produtos = new LinkedHashMap<Long, Produto>();
	
	static {
		produtos.put(88L, new Produto(88L, "Celular - R$ 100,00", 10000L));
		produtos.put(99L, new Produto(99L, "Celular - R$ 370,57", 37057L));
		produtos.put(589L, new Produto(589L, "iPhone - R$ 2.000,00", 200000L));
		produtos.put(55L, new Produto(55L, "Legacy 500 - R$ 9.990.000,00", 999000000L));
		produtos.put(0L, new Produto(0L, "Injeção - R$ 0,00", 0L));
		produtos.put(852L, new Produto(852L, "TV 46'' LED - R$ 7.999,90", 799990L));
		produtos.put(8554L, new Produto(8554L, "Bala Chita - R$ 1,00", 100L));
	}
	
	public static Produto recuperar(long id) {
		return produtos.get(id); 
	}
	
	public static Collection<Produto> todos() {
		return produtos.values();
	}
	
	
}
