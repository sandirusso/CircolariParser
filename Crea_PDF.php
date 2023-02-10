<?php

// LIBRERIE

    require_once('TCPDF/tcpdf.php');
    require_once('index.php');

// CREAZIONE E SALVATAGGIO PDF

    // Creazione PDF

    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetCreator('SANDI');
    $pdf->SetAuthor('Sandi');
    $pdf->SetTitle('ELENCO CIRCOLARI');

    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->AddPage();
    $pdf->setAutoPageBreak(true, 5);
    foreach ($circolare as $item) {
        $y = $pdf->GetY();
        $height = $y + $pdf->getStringHeight(0, 'Numero: ' . $item['numero']) + $pdf->getStringHeight(0, 'Link: ' . $item['link']) + $pdf->getStringHeight(0, 'Descrizione: ' . $item['descrizione']) + $pdf->getStringHeight(0, 'Data: ' . $item['data']);
        if ($height > $pdf->getPageHeight() - $pdf->getBreakMargin()) {
            $pdf->AddPage();
        }
        $pdf->SetFont('helvetica', 'B',  11);
        $pdf->Write(0, 'Numero: ' . $item['numero']);
        $pdf->Ln();
        $pdf->Cell(5);
        $pdf->SetFont('helvetica', '', 10.5);
        $pdf->write(0, 'Link: ');
        $pdf->SetTextColor(0, 0, 255);
        $link = $item['link'];
        $linkWidth = $pdf->GetStringWidth($link);
        if ($linkWidth > 150) {
            $linkWidth = 150;
        }
        $pdf->Cell($linkWidth, 0, $link, 0, 0, 'L', 0, $link);
        $pdf->SetTextColor(0);
        $pdf->Ln();
        $pdf->Cell(5);
        $pdf->MultiCell(0, 0, 'Descrizione: ' . $item['descrizione'], 0, 'L', 0, 1, '', '', true, 0, false, true, 0);
        $pdf->Cell(5);
        $pdf->Write(0, 'Data: ' . $item['data']);
        $pdf->Ln();
        $pdf->Ln();
    }

    $server_directory = getcwd();
    $dir = $server_directory . "/Elenco Circolari.pdf";
    $pdf->Output($dir, 'F');
    if (is_file("Elenco Circolari.pdf")) {
        $iframe = '<iframe src="Elenco Circolari.pdf" style="width:100%; height:95%;"></iframe>';
        echo '<b>Il file è stato creato!</b>' . $iframe;
    } else {
        echo "C'è stato qualche problema nella creazione del file! :(";
    }
?>