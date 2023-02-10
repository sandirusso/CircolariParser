<?php

// LIBRERIE

	include_once('index.php');


// CONTROLLO CIRCOLARI

	// Controllo se la richiesta viene fatta dal server o dall'utente

	if ($_SERVER['REQUEST_METHOD'] === 'GET') {
		session_start();
		if (!isset($_SESSION['message_sent'])) {
			$_SESSION['message_sent'] = false;
		}

		if (!$_SESSION['message_sent']) {
			// Legge il contenuto del file "lastupdate.txt"
			$chatID = json_decode(file_get_contents("lastupdate.txt"), true);
			$botToken = "5951246809:AAGvAJU6MRN0JhaTaldU1ggkaJqqAainn5E";
			$website = "https://api.telegram.org/bot" . $botToken;

			// Apro e decodifico il file 'circolari.txt'
			$file_data = json_decode(file_get_contents('circolari.txt'), true);

			// Creo due array, uno per le circolari aggiunte e uno per quelle rimosse
			$circolari_aggiunte = array();
			$circolari_rimosse = array();

			// Se una circolare si trova sul sito ma non sul file txt, lo aggiunge nell'arrey delle circolari aggiunte
			foreach ($circolare as $c) {
				if (!in_array($c, $file_data)) {
					$circolari_aggiunte[] = $c;
				}
			}

			// Se una circolare si trova sul file ma non sul sito, lo aggiunge nell'array delle circolari rimosse
			foreach ($file_data as $c) {
				if (!in_array($c, $circolare)) {
					$circolari_rimosse[] = $c;
				}
			}


			// Stampo il messaggio per le nuove circolari
			if (!empty($circolari_aggiunte)) {
				$messaggio = "";
				foreach ($circolari_aggiunte as $c) {
					$messaggio = "<b>Nuova circolare</b>: "
						. "\nNumero: " . $c["numero"]
						. "\n\nDescrizione: " . $c["descrizione"]
						. "\n\nData: " . $c["data"] . "\n\n";

					echo "<b>Nuova circolare</b>: "
						. "<br>Numero: " . $c["numero"]
						. "<br>Link: " . $c["link"]
						. "<br>Descrizione: " . $c["descrizione"]
						. "<br>Data: " . $c["data"] . "<br><br>";;


					foreach ($chatID as $elemento) {
						$path_to_pdf = "circolari/Circolare - " . $c["numero"] . ".pdf"; // percorso al file PDF
						$curl = curl_init();
						curl_setopt_array($curl, array(
							CURLOPT_URL => $website . "/sendDocument",
							CURLOPT_POST => true,
							CURLOPT_POSTFIELDS => array(
								"chat_id" => $elemento['chatID'],
								"document" => new CURLFile($path_to_pdf),
								"caption" => $messaggio,
								"parse_mode" => "HTML"
							),
							CURLOPT_RETURNTRANSFER => true
						));
						$response = curl_exec($curl);
						$err = curl_error($curl);

						curl_close($curl);

						if ($err) {
							echo "cURL Error #:" . $err;
						} else {
							echo $response;
						}
					}
				}
			}


			// Stampo il messaggio per le circolari rimosse
			if (!empty($circolari_rimosse)) {
				foreach ($circolari_rimosse as $rc) {
					$messaggio = "<b>Circolare rimossa</b>:"
                    . "\nNumero: <b>" . $rc["numero"] . "</b>\n";

					echo "<b>Circolare rimossa</b>:<br>Numero: <b>" . $rc["numero"] . "</b><br>";

                    $messaggio = urlencode($messaggio); // codifica il messaggio in formato URL

                    foreach ($chatID as $elemento){
                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                    CURLOPT_URL => $website . "/sendMessage?chat_id=" . $elemento['chatID'] . "&text=" . $messaggio . "&parse_mode=HTML",
                    CURLOPT_RETURNTRANSFER => true
                    ));
                    $response = curl_exec($curl);
                    $err = curl_error($curl);
                    curl_close($curl);

                    if ($err) {
                    echo "cURL Error #:" . $err;
                    } else {
                    echo $response;
                    }
				}
			}
        }

			// Se non sono state aggiunte circolari, mi mostra un messaggio con l'ultima uscita
			 if (empty($circolari_aggiunte)) {
						$last_circular = reset($circolare);
						echo "Nessuna nuova circolare.<br>L'ultima rimane la numero: <b>" . $last_circular["numero"] . "</b>";
					}

			file_put_contents('circolari.txt', json_encode($circolare));
		}
	}

?>