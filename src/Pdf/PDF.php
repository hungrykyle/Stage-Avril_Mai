<?php
require('fpdf.php');

class PDF extends FPDF
{
function Header()
{
    global $titre;
    $titre = utf8_decode('Rapport d\'activité');
    // Logo
    //$this->Image('img/logo-okki.png',10,6,30,0,'','http://www.okki.fr');
    // Arial gras 15
    $this->SetFont('Arial','B',15);
    // Calcul de la largeur du titre et positionnement
    $w = $this->GetStringWidth($titre)+6;
    $this->SetX((210-$w)/2);
    // Couleurs du cadre, du fond et du texte

    // Epaisseur du cadre (1 mm)
    $this->SetLineWidth(1);
    // Titre
    $this->Cell($w,9,$titre,1,1,'C');
    // Saut de ligne
    $this->Ln(40);
}

function Footer()
{
    // Positionnement à 1,5 cm du bas
    $this->SetY(-15);
    // Arial italique 8
    $this->SetFont('Arial','I',8);
    // Couleur du texte en gris
    $this->SetTextColor(128);
    // Numéro de page
    $this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C');
}
function InfoAnnonce(Keyword $keyword){
    $word = $keyword->getKeyword();
    // Arial 12
    $this->SetFont('Arial','',12);
    // Infos
    $this->Cell(0,6,utf8_decode('Mot clé : '.$word));
    $this->Ln();
    $this->Cell(0,6,utf8_decode('Utilisateur : '));
    $this->Ln();
    $this->Cell(0,6,utf8_decode('Date : '));
    // Saut de ligne
    $this->Ln(15);
}
function TitreAnnonce(Annonce $annonce)
{
    $title = $annonce->getTitle();
    // Arial 12
    $this->SetFont('Arial','',12);
    // Couleur de fond
    $this->SetFillColor(200,0,0);
    // Titre
    $this->Cell(0,6,'Titre de l\'annonce : '.utf8_decode($title),0,1,'L',true);
    // Saut de ligne
    $this->Ln(4);
}

function CorpsAnnonce(Annonce $annonce)
{
    // Lecture du fichier texte
    
    $link = $annonce->getLink();
    $desc = $annonce->getDesc();
    $score = $annonce->getScore();
    $date = $annonce->getDate();
    $research = $annonce->getResearch();
    // Times 12
    $this->SetFont('Times','',12);
    // Sortie du texte justifié
    $this->MultiCell(0,5,utf8_decode('Date de l\'annonce : '.$date.' - Moteur de recherche où a été trouvé l\'annonce : '.$research));
    $this->Ln();
    $this->SetFont('Times','I');
    $this->Write(5,$link,$link);
    $this->Ln();
    $this->SetFont('Times','',12);
    $this->Ln();
    $this->MultiCell(0,5,utf8_decode($desc));
    if (isset($annonce->getScore)){
         $this->Ln();
         $this->MultiCell(0,5,utf8_decode($score));
    }
    $extra = $annonce->getExtra();
     if (!empty($extra)){
         $this->Ln();
         $all_extra = '';
         foreach ($extra as $key => $value) {
             $all_extra .= $value->getText().' ';
             
           
         }
          $this->MultiCell(0,5,utf8_decode('Informations supplémentaires : '.$all_extra));
    }
    $miniannonce = $annonce->getMiniAnnonce();
     if (!empty($miniannonce)){
         $this->Ln(5);
         $this->Cell(20,10,'Minis annonce sous l\'annonce :',0,1);
         $this->Ln(5);
         foreach ($miniannonce as $key => $value) {
             $this->MultiCell(0,5,utf8_decode('Titre :  '.$value->getTitle()));
             if (strlen($value->getLink()) > 30) {
                 $this->Write(5,'Lien de cette mini annonce',$value->getLink());
                 $this->Ln();
             } else {
                 $this->Write(5,$value->getLink(),$value->getLink());
                 $this->Ln();
             }
             
             
             $this->MultiCell(0,5,utf8_decode('Description : '.$value->getDesc()));
        }
    }
    $lienannonce = $annonce->getLienAnnonce();
     if (!empty($lienannonce)){
         $this->Ln(5);
         $this->Cell(20,10,'Liens sous l\'annonce :',0,1);
         $this->Ln(5);
         foreach ($lienannonce as $key => $value) {
             $this->MultiCell(0,5,utf8_decode('Titre : '.$value->getTitle()));
             $this->MultiCell(0,5,utf8_decode('Lien : '.$value->getLink()));

        }
    }
    
}
function AjouterAnnonce(Annonce $annonce)
{
    
    $this->TitreAnnonce($annonce);
    $this->CorpsAnnonce($annonce);
    $this->Ln(10);
}
}

