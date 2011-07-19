<html>
<?php
if(!isset($_GET['screen'])){
  //falls on default screen (main menu)
  $_GET['screen'] = '';
}

//shoping cart screens
switch($_GET['screen']){
  default: ?>
  
  <!-- /** Main menu **/ -->
  
	<head>
		<title>Demo : Shopping Cart for Payment Processing via Cielo</title>
	</head>
	<h2>Demo : Shopping Cart for Payment Processing via Cielo</h2>
	<h4>
		<a href="cielo_test_index.php?screen=cart_cielo">Purchase Order (Redirects to Cielo for Card Handling)</a>
		<br>
		<a href="cielo_test_index.php?screen=cart_merchant">Purchase Order (Customers provide their Card Details at Merchant's Website)</a>
		<br><br>
		<a href="cielo_test_index.php?screen=cart_manage_po">Purchase Order Manager</a>

	</h4>

	<br>
	<h2>Portuguese</h2>
	<h4>
		<a href="cielo_test_index.php?screen=cart_cielo">Fazer Pedido (Redireciona para a Cielo coletar os dados do Cartao)</a>
		<br>
		<a href="cielo_test_index.php?screen=cart_merchant">Fazer Pedido (Dados do Cartao e' coletado pela Loja)</a>
		<br><br>
		<a href="cielo_test_index.php?screen=cart_manage_po">Gerenciador de Pedidos</a>

	</h4>
	
<?php
  break;
  case 'cart_cielo': ?>
  
    <!-- /** Purchase Order Example for card handling at cielo **/ -->
    
	<head>
		<title>Cart Demo : Purchase order (Card Details are not collected by the Merchant's web store)</title>
	</head>	
	<center>
		<h2>Shopping Cart - Customers are redirected to cielo for Card handling</h2>
		<form action="cielo_test_process_payment.php" method="post" >
			<table border="1">
				<tr>
					<td>Order Number</td>
					<td><input type="text" name="po_#" value="<?php echo rand(1000000, 9999999); ?>"></td>
				</tr>
				<tr>
					<td>Products</td>
					<td>
						<select name="order_total">
							<option value="100.00">Celular - R$ 100,00</option>						
							<option value="370.57">Celular - R$ 370,57</option>					
							<option value="2000.00">iPhone - R$ 2.000,00</option>
							<option value="9990000.00">Legacy 500 - R$ 9.990.000,00</option>
							<option value="0.00">Injeção - R$ 0,00</option>
							<option value="7999.90">TV 46'' LED - R$ 7.999,90</option>
							<option value="1.00">Bala Chita - R$ 1,00</option>
						</select>						 
					</td>			
				</tr>
				<tr>
					<td>Payment Method</td>
					<td>
						Card Flag<br>
						<select name="card_flag">
							<option value="visa">Visa</option>
							<option value="mastercard">Mastercard</option>
							<option value="elo">Elo</option>
						</select>
						<br><br>
						Card Type<br>
						<input type="radio" name="card_type" value="1" checked>Credit
						<br/><input type="radio" name="card_type" value="A">Debit
						<br/><br/>
						Installments				
						<br><input type="radio" name="inst" value="1" checked>Single Payment (à Vista)
						<br><input type="radio" name="inst" value="3">3x
						<br><input type="radio" name="inst" value="6">6x
						<br><input type="radio" name="inst" value="12">12x
						<br><input type="radio" name="inst" value="18">18x
						<br><input type="radio" name="inst" value="36">36x
						<br><input type="radio" name="inst" value="56">56x
					</td>
				</tr>
				<tr>
					<td>Payment Attributes</td>
					<td>
						<table>
							<tr>
								<td>Creditor for installment term payment</td>
								<td>
									<select name="creditor">
										<option value="2">Merchant</option>
										<option value="3">Cielo</option>
									</select>
								</td>
							</tr>
							<tr>
								<td>Auto Capture?</td>
								<td>
									<select name="auto_capture">
										<option value="true" selected="selected">Yes</option>
										<option value="false">No</option>
									</select>
								</td>
							</tr>
							<tr>
								<td>Authorization Type</td>
								<td>
									<select name="authorization_type">
										<option value="3">Skip authentication and go straight to authorization</option>
										<option value="2">Authorize either authenticated or not</option>
										<option value="0">Authentication only</option>
										<option value="1">Authorize only if authenticaded</option>
									</select>
								</td>
							</tr>
						</table>
					</td>
				</tr>		
				<tr>
					<td align="center" colspan="2">
						<input type="submit" value="Process Payment"/>
					</td>
				</tr>
			</table>
		</form>
		<a href="cielo_test_index.php">Menu</a>
	</center>

<?php
  break;
  case 'cart_merchant': ?>
  
    <!-- /** Purchase Order Example for card handling at merchant's website **/ -->
	<head>
		<title>Cart Demo : Purchase order (Card details collecting)</title>
	</head>
	<body>
		<center>
			<h2>Shopping Cart - Customer interaction only with merchant's website</h2>
			<form name="frm" action="cielo_test_process_payment.php" method="post">
				<table border="1">
					<tr>
						<td>Order Number</td>
						<td><input type="text" name="po_#" value="<?php echo rand(1000000, 9999999); ?>"></td>
					</tr>
					<tr>
						<td>Products</td>
						<td>
							<select name="order_total">
								<option value="100.00">Celular - R$ 100,00</option>						
								<option value="370.57">Celular - R$ 370,57</option>					
								<option value="2000.00">iPhone - R$ 2.000,00</option>
								<option value="9990000.00">Legacy 500 - R$ 9.990.000,00</option>
								<option value="0.00">Injeção - R$ 0,00</option>
								<option value="7999.90">TV 46'' LED - R$ 7.999,90</option>
								<option value="1.00">Bala Chita - R$ 1,00</option>
							</select>						 
						</td>
					</tr>
					<tr>
						<td>Payment Method</td>
						<td>
							Card Flag<br>
							<select name="card_flag">
								<option value="visa">Visa</option>
								<option value="mastercard">Mastercard</option>
								<option value="elo">Elo</option>
							</select>
							<br><br>
							Card Type<br>
							<input type="radio" name="card_type" value="1" checked>Credit
							<br/><input type="radio" name="card_type" value="A">Debit
							<br/><br/>
							Installments					
							<br><input type="radio" name="inst" value="1" checked>Single Payment (à Vista)
							<br><input type="radio" name="inst" value="3">3x
							<br><input type="radio" name="inst" value="6">6x
							<br><input type="radio" name="inst" value="12">12x
							<br><input type="radio" name="inst" value="18">18x
							<br><input type="radio" name="inst" value="36">36x
							<br><input type="radio" name="inst" value="56">56x
						</td>
					</tr>
					<tr>
						<td>Card Details</td>
						<td>
							<table border="0">
								<tr>
									<td>Number</td>
									<td><input type="text" name="card_number" value="4551870000000183"></td>
								</tr>
								<tr>
									<td>Valid Until (ie. jun/2010 = 201006)</td>
									<td><input type="text" name="card_expiration" value="201508"></td>
								</tr>
								<tr>
									<td>Security Code</td>
									<td><input type="text" name="card_code" value="973"></td>
								</tr>												
							</table>
						</td>
					</tr>
					<tr>
						<td>Payment Attributes</td>
						<td>
							<table>
								<tr>
									<td>Creditor for installment term payment</td>
									<td>
										<select name="creditor">
											<option value="2">Merchant</option>
											<option value="3">Cielo</option>
										</select>
									</td>
								</tr>
								<tr>
									<td>Auto Capture?</td>
									<td>
										<select name="auto_capture">
											<option value="true" selected="selected">Yes</option>
											<option value="false">No</option>
										</select>
									</td>
								</tr>
								<tr>
									<td>Try Authentication?</td>
									<td>
										<input type="radio" name="authenticate" value="1"/>Yes
										<input type="radio" name="authenticate" value="0" checked="checked"/>No
									</td>
								</tr>
								<tr>
									<td>Authorization Type</td>
									<td>
										<select name="authorization_type">
											<option value="3">Skip authentication and go straight to authorization</option>
											<option value="2">Authorize either authenticated or not</option>
											<option value="0">Authentication only</option>
											<option value="1">Authorize only if authenticaded</option>
										</select>
									</td>
								</tr>						
							</table>
						</td>
					</tr>										
					<tr>
						<td align="center" colspan="2">
							<input type="submit" value="Process Payment"/>
						</td>
					</tr>
				</table>
			</form>
			<a href="cielo_test_index.php">Menu</a>
		</center>
	</body>
	
<?php
  break;
  case 'cart_manage_po':
	//Status code descriptions
	$status = array(0 => 'Transaction was created',
			1 => 'In progress',
			2 => 'Authenticated',
			3 => 'Not Authenticated',
			4 => 'Authorized or still to be Captured',
			5 => 'Not Authorized',
			6 => 'Captured',
			8 => 'Not Captured',
			9 => 'Voided (Cancelada)',
			10 => 'Being Authenticated',
		       );
  
	$rows = array();
	/** Query our cookie database **/
	foreach($_COOKIE as $rowId => $rowData){
		if(is_numeric($rowId)){
			$rowData = explode(':', $rowData);
			
			foreach($rowData as $fieldName){
				//separete field name from field value
				$field  		   = explode('=', $fieldName);
				$rows[$rowId][$field[0]] = $field[1];
				
				//Status Description
				if($field[0] == 'status'){
					$rows[$rowId]['status_description'] = $status[$field[1]];
				}
			}
		}
	}
  ?>
  
    <!-- /** Managing Purchase Orders **/ -->
    
	<head>
		<title>Shopping Cart : Purchase Orders</title>
		<script type="text/javascript">
			function executar() {
				window.open("", "tela_operacao", "toolbar=0,location=0,directories=0,status=1,menubar=0,scrollbars=1,resizable=0,screenX=0,screenY=0,left=0,top=0,width=625,height=725");				
				return true;
			}		
		</script>
	</head>
	<center>
		<h2>Placed Orders</h2>
		
		<table border="1">
			<tr>				
				<th>PO #</th>
				<th>Amount</th>
				<th>Mode</th>
				<th>Installments</th>
				<th>TID</th>
				<th>Status</th>
				<th>Amount to Capture</th>
				<th>Action</th>
				<th></th>
			</tr>
			<form action="cielo_test_process_payment.php?order=manager" target="tela_operacao" onsubmit="javascript:executar();" method="post">

		<?php
		foreach($rows as $rowId => $data){
		?>
			<input type="hidden" name="order" value="<?php echo $rowId; ?>"/>
			<input type="hidden" name="key" value="' . $iterator->key() . '"/>
			<tr>
			<td><?php echo $rowId; ?></td>
			<td align="right"><?php echo $data['totalAmount']; ?></td>
			<td><?php echo ($data['CardType'] == 1)?'Credit':'Debit'; ?></td>
			<td><?php echo $data['Installments']; ?></td>
			<td><?php echo $data['tid']; ?></td>
			<td style="color: red;"><?php echo $data['status_description']; ?></td>
			<td align="right">
				<select name="percentualCaptura">
					<option value="100">100%</option>
					<option value="90">90%</option>
					<option value="30">30%</option>
				</select>
			</td>
			<td align="right">
				<select name="acao">
					<option value="autorizar">Authorize</option>
					<option value="capturar">Capture</option>
					<option value="cancelar">Void (Cancelar)</option>
					<option value="consultar">Follow Up (Consultar)</option>
				</select>
				</td>
			<td><input type="submit" value="Run" /></td> 
			</tr>
			</form>
		<?php
		}
		if(empty($rows)){
		?>
			<tr>
			<td colspan="10" style="text-align: center;">No purcharse order has been found.</td>
			</tr>
		<?php
		}
		?>

		</table>		
		<p>
			<a href="cielo_test_index.php">Menu</a>
		</p>
	</center>
<?php
  break;
} ?>


<br><br><br><br>
<center>
<a rel="license" href="http://creativecommons.org/licenses/by-nc/3.0/br/"><img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/by-nc/3.0/br/88x31.png" /></a><br /><span xmlns:dct="http://purl.org/dc/terms/" property="dct:title">BrazilCards PHP Library</span> by <a xmlns:cc="http://creativecommons.org/ns#" href="http://drupalista-br.github.com/BrazilCards/" property="cc:attributionName" rel="cc:attributionURL">Francisco Luz</a> is licensed under a <a rel="license" href="http://creativecommons.org/licenses/by-nc/3.0/br/">Creative Commons Attribution-NonCommercial 3.0 Brazil License</a>.
</center>	
</html>