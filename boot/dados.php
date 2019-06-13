<?php 
//	require "modeloGrafico.html";
	header('Content-type: application/json');
	$curl = curl_init();

	curl_setopt($curl, CURLOPT_URL, "https://balneabilidade.ima.sc.gov.br/relatorio/historico");
	curl_setopt($curl, CURLOPT_POST, 1);
	
	curl_setopt($curl, CURLOPT_POSTFIELDS, "municipioID=23&localID=39&ano=2018&redirect=true");
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);



	$html = curl_exec($curl);

//	echo $html;

	$doc = new DOMDocument(); // xml - php-xml
	# objeto contendo o documento html

	$doc->loadHTML($html);

	$tables = $doc->getElementsByTagName('table');
//echo '<pre>';
	$todos = [];
	foreach ($tables as $i => $table) {
		if($i %2 !=0){//impares - cabecahos
			$pontos = [];
			$labels = $table->getElementsByTagName('label');
			$pontocoleta = [];
			foreach ($labels as $label) {
				$partes = explode(': ', $label->nodeValue) ;
				$pontocoleta[str_replace(' ', '_', $partes[0])] = $partes[1];
				//array_push($pontos, $pontocoleta);
					
			}
			$todos[$i]['descricao'] = $pontocoleta;			
		}else{
			$coletas= [];
			if ($i != 0){ //desconsidera a tabela 0 (cabecaho da pagina)
		//echo "<pre>";
				$trs = $table->getElementsByTagName('tr');
				foreach ($trs as $tr) {
					$coleta = [];
					$tds = $tr->getElementsByTagName('td');
					foreach ($tds as $td) {
						$coleta[$td->getAttribute('class')] = $td->nodeValue;
					}
					$coletas[] = $coleta;
				}
				$todos[$i-1]['coletas'] = $coletas;			
				
			}
		}

	}

	// $finder = new DomXPath($html);
	// $classname="";
	// $nodes = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");


	//var_dump($todos);
	// $label ->nodeValue    'Municipio: Itajai'
	// $partes    array('Municipio', 'Itajai')
	
	// pontocoleta    array ('Municipio' => 'Itajai')

	echo  json_encode($todos);
	// echo ('Chart');
	?>

	
	