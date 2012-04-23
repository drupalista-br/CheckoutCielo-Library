<%@ page isErrorPage="true" %>
<%@page import="br.com.cbmp.ecommerce.util.web.WebUtils"%>
<%
	exception.printStackTrace();
%>
<html>
	<head>
		<title>Pagamento VISA</title>
	</head>
	<body>
		<p>A transação não pôde ser criada!</p>
		<p>Erro: <%= exception.getMessage() %></p>
		<p>
			<input type="button" onclick="javascript:window.location = 'menu.html'; " value="Retornar"/>
		</p>
		<p>
			Pedido efetuado:<br/>
			<pre>
<%= new WebUtils(request).recuperarUltimoPedido().toString() %>
			</pre> 
		</p>
	</body>
</html>