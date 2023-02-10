<?php

// LIBRERIE
    include_once('index.php');

// TABELLA CON LE INFORMAZIONI SULLE CIRCOLARI

    echo "<style>
            table, th, td
            {
            border: 1px solid black;
            border-collapse: collapse;
            padding: 8px;
            }
            </style>";

    // Creo le colonne della tabella
    echo "<table style='width:100%''>";
    echo "<thead><tr><th colspan='4'>ELENCO CIRCOLARI</th></thead>"
        . "<td style='text-align: center; vertical-align: middle; font-size:15px'><b>NUMERO</b></td>"
        . "<td style='text-align: center; vertical-align: middle; font-size:15px'><b>LINK</b></td>"
        . "<td style='text-align: center; vertical-align: middle; font-size:15px'><b>DESCRIZIONE</b></td>"
        . "<td style='text-align: center; vertical-align: middle; font-size:15px'><b>DATA</b></td>";

    // Ciclo per la stampa dell'array
    foreach ($circolare as $item) {
        echo "<tr>";
        echo "<td style='text-align: center; vertical-align: middle; font-size:15px'>$item[numero]</td>";
        echo "<td style='text-align: center; vertical-align: middle; font-size:15px'>$item[link]</td>";
        echo "<td style='text-align: left; vertical-align: middle; font-size:15px'>$item[descrizione]</td>";
        echo "<td style='text-align: center; vertical-align: middle; font-size:15px'>$item[data]</td>";
        echo "</tr>";
    }
    echo "</table>";
?>