<%@page import="br.com.cbmp.ecommerce.pedido.Pedido"%>
<%@page import="br.com.cbmp.ecommerce.util.web.WebUtils"%>

<%	
	Pedido pedido = new WebUtils(request).criarPedido();
%>

<html>
	<head>
		<title>Pagamento VISA</title>		
	</head>
	<body onload="document.forms[0].submit();">
		<form name="frmpagamento" method="post" action="novoPedido.jsp">
			Redirecionando...			
		</form>
	</body>
</html>