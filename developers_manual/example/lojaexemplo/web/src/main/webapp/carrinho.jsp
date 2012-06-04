<%@page import="br.com.cbmp.ecommerce.pedido.Produto"%>
<%@page import="br.com.cbmp.ecommerce.util.Produtos"%>
<%@page import="br.com.cbmp.ecommerce.pedido.IndicadorAutorizacao"%>
<%@page import="br.com.cbmp.ecommerce.pedido.Modalidade"%>
<html>
	<head>
		<title>Loja Exemplo : Pedidos</title>
	</head>
	<center>
		<h2>
			Carrinho
		</h2>
		<form action="novoPedidoAguarde.jsp" method="post" >
			<table border="1">
				<tr>
					<td>Produto</td>
					<td>
						<select name="produto">
						<% for (Produto produto : Produtos.todos()) {%>
							<option value="<%= produto.getId() %>"><%= produto.getDescricao() %></option>
						<% } %>	
						</select>						 
					</td>			
				</tr>
				<tr>
					<td>Forma de pagamento</td>
					<td>
						<select name="codigoBandeira">
							<option value="1">Visa</option>
							<option value="2">Mastercard</option>
							<option value="3">Elo</option>
						</select>
						<br/>					
						<input type="radio" name="formaPagamento" value="D">Débito
						<br><input type="radio" name="formaPagamento" value="C" checked>Crédito à Vista
						<br><input type="radio" name="formaPagamento" value="3">3x
						<br><input type="radio" name="formaPagamento" value="6">6x
						<br><input type="radio" name="formaPagamento" value="12">12x
						<br><input type="radio" name="formaPagamento" value="18">18x
						<br><input type="radio" name="formaPagamento" value="36">36x
						<br><input type="radio" name="formaPagamento" value="56">56x
					</td>
				</tr>
				<tr>
					<td>Configuração</td>
					<td>
						<table>
							<tr>
								<td>
									Parcelamento
								</td>
								<td>
									<select name="tipoParcelamento">
										<option value="<%= Modalidade.PARCELADO_LOJA.getCodigo() %>">Loja</option>
										<option value="<%= Modalidade.PARCELADO_ADMINISTRADORA.getCodigo() %>">Administradora</option>
									</select>
								</td>
							</tr>
							<tr>
								<td>Capturar Automaticamente?</td>
								<td>
									<select name="capturarAutomaticamente">
										<option value="true">Sim</option>
										<option value="false" selected="selected">Não</option>
									</select>
								</td>
							</tr>
							<tr>
								<td>Autorização Automática</td>
								<td>
									<select name="indicadorAutorizacao">
										<% for (IndicadorAutorizacao ind : IndicadorAutorizacao.values()) { %>
										<option value="<%= ind.getCodigo() %>"><%= ind.getDescricao() %></option>
										<% } %>
									</select>
								</td>
							</tr>
						</table>
					</td>
				</tr>		
				<tr>
					<td align="center" colspan="2">
						<input type="submit" value="Pagar"/>
					</td>
				</tr>
			</table>
		</form>
		<a href="menu.html">Menu</a>
	</center>
</html>