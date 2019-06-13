<?php

header('Cache-Control: no-cache');
header('Content-type: application/json; charset="utf-8"', true);

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL,'https://balneabilidade.ima.sc.gov.br/relatorio/historico');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS,"municipioID=23&localID=39&ano=2018&redirect=true");

curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$conteudo = curl_exec($ch);

$doc = new DOMDocument();
$doc->loadHTML($conteudo);

$tables = $doc->getElementsByTagName('table');
// echo '<pre>';
$dados = [];

foreach ($tables as $key => $table) {
	$pontos = [];
	// if ($key != 0) { 
		// ignora a tabela inicial de cabecalho
		if ($key % 2 != 0) { 
			// tabelas impares contem dados do ponto de coleta
			$labels = $table->getElementsByTagName('label');
			foreach ($labels as $label) {
				$partes = explode(':', $label->textContent);

				$title = str_replace(" ", "_", preg_replace("/&([a-z])[a-z]+;/i", "$1", htmlentities(trim($partes[0]))));
				$value = $partes[1];

				$pontos[$title] = $value;

				// $pontocoleta[str_replace(' ', '_', $partes[0])] = $partes[1];
			}

			// $pontocoleta = [];
			// array_push($pontos, $pontocoleta);
			// array_push($dados, $pontos);
		} 
		else { 
			// tabelas pares contem os dados das coletas
			$coletas = [];
			// cho "<pre>";
			$trs = $table->getElementsByTagName('tr');
			foreach ($trs as $tr) {
				$tds = $tr->getElementsByTagName('td');
				foreach ($tds as $td) {
					$atributo = $td->getAttribute('class');
					if ($atributo != null) $coletas[$atributo] = $td->textContent;
					// $coleta[$atributo] = $td->nodeValue;
				}

				// array_push($coletas, $coleta);
				$pontos[] = array_filter($coletas);
			}

			// array_push($dados, $coletas);
		}

		$dados[$key] = array_filter($pontos);
	// }
}

// echo '<pre>';
// print_r($dados);
echo json_encode(array_filter($dados));

// print(json_encode ($dados));

?>
