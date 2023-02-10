<?php

// LIBRERIE

	// Libreria per estrarmi le informazioni da una pagina web
	include_once('simple_html_dom.php');


// ESTRAZIONE E SALVATAGGIO DATI IN UN ARRAY

	// URL del sito web da controllare
	$url = "http://veronatrento.it/circolari-diurno-a-s-2022-23";

	// Analizzo l'URL per poter estrarre il suo schema
	$url_parsed = parse_url($url);

	// Riconosce lo schema del link e che la parte fissa è 'veronatrento.it'
	$domain = $url_parsed['scheme'] . '://' . $url_parsed['host'];

	// Scarica il codice HTML dal sito web
	$html = file_get_html($url);

	// 'div' che contiene l'id della tabella
	$div = $html->find("#attachmentsList_com_content_article_1640", 0);

	// All'interno del div cerco il tag 'table', in questo caso seleziono la prima tabela
	$table = $div->find('table', 0);


	// Analizza ogni riga 'tr' della tabella
	foreach ($table->find('tr') as $row) {
		// Analizza ogni singola cella della riga
		$cells = $row->find('td');

		// Se ci sono più celle, la tabella non è vuota e quindi posso controllare i dati al suo interno
		if (count($cells) > 0) {
			// Nella cella '0' estrae il nummero ('Circolare 1')
			$number = $cells[0]->plaintext;

			// Trasforma  il testo in maiuscolo 
			$number = str_ireplace('circolare', '', $number);
			
			// Rimuovo tutti gli spazi
			$number = str_ireplace(' ', '', $number);

			// Collego il dominio estratto 'http://veronatrento.it', alla cella '0' che contiene 'href'
			$link = $domain . $cells[0]->find('a', 0)->href;

			// Mi rimpiazza lo spazio in '%20' nel link della circolare
			$link = str_replace(" ", "%20", $link);

			//  Nella cella '1' estrae la descrizione
			$description = $cells[1]->plaintext;

			// Rimpiazzo la parola 'oggetto' e anche le varianti del carattere
			$description = str_ireplace('oggetto:', '', $description);

			// Trasforma il testo in maiuscolo, utilizzo 'mb' per avere  anche le lettere accentate in maiuscolo
			$description = mb_strtoupper($description);

			// Nella cella '2' estrae la data
			$date = $cells[2]->plaintext;

			// Creo un array associativo dove salvo tutte le informazioni sopra richieste
			$circolare[] = array(
				"numero" => $number,
				"link" => $link,
				"descrizione" => $description,
				"data" => $date
			);

			// Controllo se esiste la cartella 'circolari'
			if (!file_exists('circolari')) {
				mkdir('circolari', 0777, true);
			}

			foreach ($circolare as $c) {

				// Creo un nome file univoco utilizzando il numero della circolare
				$filename = 'Circolare - ' . $c['numero'] . '.pdf';

				// Verifica se il file esiste già nella cartella 'circolari'
				if (!file_exists('circolari/' . $filename)) {

					// Scarica il file PDF dal link e lo salva nella cartella 'circolari'
					file_put_contents('circolari/' . $filename, file_get_contents($c['link']));
				}
			}
		}
	}
?>