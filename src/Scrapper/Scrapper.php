<?php

	require_once __DIR__.'/../Domain/LienAnnonce.php';
	require_once __DIR__.'/../Domain/MiniAnnonce.php';
	require_once __DIR__.'/../Domain/Score.php';
	require_once __DIR__.'/../Domain/Extra.php';
	
	use Goutte\Client;
    class Scrapper{
        
    private $keyword;
       
	public function __construct($keyword) {
	$this->keyword = $keyword;
	$this->url = '';
	}
	
	public function getKeyword() {
		return $this->keyword;
	}
	
	public function getUrl() {
		return $this->url;
	}
	//Préparation du lien
	public function setUrl() {
		$url = 'http://www.google.fr/search?q='.$this->keyword.'&ie=utf-8&oe=utf-8&client=firefox-b-ab&gfe_rd=cr&ei=8FHjWL6aBOva8AfKjaDACg/';
		$this->url = $url;
		return $this;
	}
	//Scrapping du lien demandé
	public function parseKeyword() {
		//Instanciation d'une classe de goutte
		$client = new Client();
		$crawler = $client->request('GET', $this->url);
		//Récupération de tous le code qui concerne les annonces
		$text = $crawler->filter('.ads-ad > *')->extract(array('_text'));
		//Récupération de tous les classes qui concerne les balises des annonces
		$class = $crawler->filter('.ads-ad > *')->extract(array('class'));
		//Pointeur des deux tableaux
       	$cmpt = 0;
		//Tableau contenant toutes les annonces
        $allAnnonce = array();
		//Test de l'existence d'une annonce
		if (!empty($text)){
			$max = count($text);
			//Test de la présence d'une note
            if (in_array('_Bu', $class)) {
                $cmpt_note = 0;
				//Si oui, on crée un tableau qui recense toutes les notes (rarement plus d'une mais on ne sait jamais)
                $temp = $crawler->filter('.ads-ad > * > ._kgd')->extract(array('_text'));
                foreach ($temp as $key => $value) {
                    $note[$key] = $value;
                    }
            }
			//On recense tous les titres
			$h3 = $crawler->filter('.ads-ad > h3')->extract(array('_text'));
			//On recense tous les liens
			$cmpt_cite = 0;
			$cite = $crawler->filter('cite._WGk')->extract(array('_text'));
			//Compteur pour naviguer entre les liens en bas de l'annonce
            $cmpt_node = 1;
			//Compteur pour naviguer entre les minis annonces en bas de l'annonce
            $cmpt_miniAnnonce = 0;
            while ($cmpt < $max){
				//On regarde si la balise où on se trouve est de classe ellip, si oui cela signifie qu'on passe à une autre annonce
                if ($class[$cmpt] === 'ellip' and (in_array($text[$cmpt], $h3))) {
				
					if(isset($annonce)){
						$allAnnonce[] = $annonce;
					}
					$annonce = New Annonce();
	
					$annonce->setTitle($text[$cmpt]);
                }
				//Lien
				elseif ($class[$cmpt] === 'ads-visurl') {
					$annonce->setLink($cite[$cmpt_cite]);
					$cmpt_cite = $cmpt_cite +1;
                }
				//Description
				elseif ($class[$cmpt] === 'ads-creative ellip') {
					$annonce->setDesc($text[$cmpt]);
                }
				//Classe css qui indique si des liens en dessous sont présent ou non 
                elseif ($class[$cmpt] === '_gBb'){
                    $node = $crawler->filter('._gBb > *:nth-child('.$cmpt_node.') > * > *');
					//Tableau de lien
                    foreach ($node as $value) {
						$lien = new LienAnnonce();
						$lien->setTitle($value->textContent);
                        $allnode[] = $lien;
						$annonce->setLienAnnonce($allnode);
                    }
                $cmpt_node = $cmpt_node +1;
                }
				//Classe css qui indique si des minis annonces en dessous sont présent ou non 
                elseif ($class[$cmpt] === '_Ctg'){
					//On récupère tout les titres
                    $title = $crawler->filter('._Ctg > * > * > * > * > h3')->extract(array('_text'));
					//On récupère tout les paragraphes
                    $desc = $crawler->filter('._Ctg > * > * > * > * > div')->extract(array('_text'));
                    $nbr_title = count($title);
                    while ($cmpt_miniAnnonce < $nbr_title){
						//On ajoute chaque objet MiniAnnonce à une tableau
						$miniAnnonce = new MiniAnnonce();
                        $miniAnnonce->setTitle($title[$cmpt_miniAnnonce]);
						$miniAnnonce->setDesc($desc[$cmpt_miniAnnonce]);
						$allMiniAnnonce[] = $miniAnnonce;
						$cmpt_miniAnnonce = $cmpt_miniAnnonce +1;
                    }
					$annonce->setMiniAnnonce($allMiniAnnonce); 
                }
				//Classe css qui indique si une note est présente ou non
                elseif ($class[$cmpt] === '_Bu'){
					$score = new Score();
                    $score->setScore($note[$cmpt_note]);
					$annonce->setScore($score);
                    $cmpt_note = $cmpt_note +1;
                }
                else{ 
					$new_extra = New Extra();
					$new_extra->setText($text[$cmpt]);
					$annonce->setExtra($new_extra);
				
                }
                $cmpt++;}
				$allAnnonce[] = $annonce;
		
		}else{
			$annonce = new Annonce();
			$annonce->setTitle("Aucune annonce");
			$annonce->setLink("Aucune lien");	
			$annonce->setDesc("Aucune description");
			$allAnnonce[] = $annonce;
			
		}
		
		
		
		return $allAnnonce;
	}
	
	}