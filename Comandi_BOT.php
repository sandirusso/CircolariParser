<?php

// LIBRERIE
include_once('Crea_PDF.php');

// GESTIONE BOT TELEGRAM

    /*
            URL per l'attivazione di WEBHOOK
        https://api.telegram.org/bot(BOT_TOKEN)/setWebHook?url=https://tuoHosting/index.php
    */

    // Token e sito web per contattare il bot
    $botToken = "5951246809:AAGvAJU6MRN0JhaTaldU1ggkaJqqAainn5E";
    $website = "https://api.telegram.org/bot" . $botToken;

    // Ottengo le informazioni quando un utente scrive al bot
    $update = file_get_contents('php://input');

    // Aggiorna il documento
    $updateraw = $update;

    // Salva tutto in stile JSON
    $update = json_decode($update, TRUE);

    // Le informazioni che mi estraggo
    $chatID = $update["message"]["chat"]["id"];
    $message = $update["message"]["text"];
    $message_id = $update["message"]["message_id"];
    $nome = $update["message"]["chat"]["first_name"];
    $cognome = $update["message"]["chat"]["last_name"];
    $username = $update["message"]["chat"]["username"];

    // Apro e scrivo sul file le informazioni che mi sono estratto
    $arr = json_decode(file_get_contents("lastupdate.txt"), true);

    // Variabile boolean
    $trovato = 0;

    // Ciclo che controlla se esiste già una determinata ChatID
    foreach ($arr as $item) {
        if ($item["chatID"] === $chatID) {
            //$item["message"] = $message;
            $item["message_id"] = $message_id;
            $item["nome"] = $nome;
            $item["cognome"] = $cognome;
            $item["username"] = $username;
            $trovato = true;
            break;
        }
    }

    // Se la variabile boolean è uguale a 0, vuol dire che la ChatID è nuova e che la deve inserire nell'array
    if ($trovato === 0) {
        $arr[] = array(
            "chatID" => $chatID,
            //"message" => $message,
            "message_id" => $message_id,
            "nome" => $nome,
            "cognome" => $cognome,
            "username" => $username
        );
    }

    // Inserisco i valori dell'array nel file 'lastupdate.txt'
    file_put_contents('lastupdate.txt', json_encode($arr));

    // Per ogni messaggio diverso che ottengo, l'utente riceve una risposta diversa
    switch ($message) {
        case '/start':
            InviaMessaggio(
                $chatID,
                "Benvenuto, " . $nome . "!"
            );
            break;

        case '/info':
            InviaMessaggio(
                $chatID,
                "<b>INFORMAZIONI BOT</b>:"
                    . "\nQuesto BOT invia automaticamente un messaggio contenente le informazioni sulle circolari uscite."
                    . "\nNome: VTBOT"
                    . "\nUsername: @VERONATRENTOBOT"
                    . "\nDescrizione: Circolari Verona Trento"
                    . "\nVersione: 1.0"
            );
            break;

        case '/circolari':
            require 'Crea_PDF.php';
            InviaDocumento($chatID, 'Elenco Circolari.pdf', $botToken);
            break;

        case '/comandi':
            InviaMessaggio(
                $chatID,
                "<b>ELENCO COMANDI</b>:"
                    . "\n/start - Avvia il BOT"
                    . "\n/info - Informazioni generali sul BOT"
                    . "\n/circolari - Elenco PDF di tutte le circolari"
                    . "\n/comandi - Lista comandi"
            );
            break;

        default:
            InviaMessaggio(
                $chatID,
                "Non capisco cosa vuoi fare."
                    . "\nTi ricordo che per visualizzare la lista dei comandi, puoi scrivere '/comandi'"
            );
            break;
    }


// GESTIONE INVIO MESSAGGIO E DOCUMENTO TELEGRAM

    function InviaMessaggio($chatID, $messaggio)
    {
        $url = "$GLOBALS[website]/sendMessage?chat_id=$chatID&parse_mode=HTML&text=" . urlencode($messaggio);
        file_get_contents($url);
    }

    function InviaDocumento($chat_id, $document, $botToken)
    {
        $api_url = "https://api.telegram.org/bot$botToken/sendDocument";
        $data = array(
            'chat_id' => $chat_id,
            'document' => new CURLFile(realpath($document))
        );

        $options = array(
            CURLOPT_URL => $api_url,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => $data
        );

        $curl = curl_init();
        curl_setopt_array($curl, $options);
        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }
?>