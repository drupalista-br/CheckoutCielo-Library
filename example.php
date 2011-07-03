<?php

$client = new SoapClient('http://footballpool.dataaccess.eu/data/info.wso?wsdl');


$result = $client->TopGoalScorers(array('iTopN'=>5));

echo '<pre>';
print_r($client);

?>