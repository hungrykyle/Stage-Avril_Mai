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
		$url = 'http://www.google.fr/search?q='.$this->keyword.'/';
		$this->url = $url;
		return $this;
	}
	public function setUrlBing() {
		$url = 'http://www.bing.com/search?q='.$this->keyword.'/';
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
                    $title = $crawler->filter('._gBb > *:nth-child('.$cmpt_node.') > * > *')->extract(array('_text'));
					$all_minilinks = $crawler->filter('._gBb > *:nth-child('.$cmpt_node.') > * > * > * > *')->extract(array('href'));
					foreach ($all_minilinks as $value) {
						$next = 'http://www.googleadservices.com/pagead'.$value;
						$next_crawler = $client->request('GET', $next);
						$links = $next_crawler->filter('* > a:first-child')->extract(array('_text'));
						$link = $links[0];
						$pos = explode("ds_dest_url=", $link);
						if (isset($pos[1])) {
							$pre_link = $pos[1];
							$pos = explode("?sitelink", $pre_link);
							$post_link = $pos[0];
							$minilinks[] = $post_link;
						}else{
							$minilinks[] = $link;

						}
						
					}
					$cmpt_links = 0;
					$nodes = Array();
					while ($cmpt_links < count($title)) {
						$nodes[$title[$cmpt_links]] = $minilinks[$cmpt_links];
						$cmpt_links = $cmpt_links +1;
					}
					//Tableau de lien
                    foreach ($nodes as $key => $value) {
						$lien = new LienAnnonce();
						$lien->setTitle($key);
						$lien->setLink($value);
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
					$all_minilinks = $crawler->filter('._Ctg > * > * > * > * > h3 > *')->extract(array('href'));
					foreach ($all_minilinks as $value) {
						$next = 'http://www.googleadservices.com/pagead'.$value;
						$next_crawler = $client->request('GET', $next);
						$links = $next_crawler->filter('* > a:first-child')->extract(array('_text'));
						$link = $links[0];
						$pos = explode("ds_dest_url=", $link);
						$pre_link = $pos[1];
						$pos = explode("?sitelink", $pre_link);
						$post_link = $pos[0];
						$minilinks[] = $post_link;
					}
                    $nbr_title = count($title);
                    while ($cmpt_miniAnnonce < $nbr_title){
						//On ajoute chaque objet MiniAnnonce à une tableau
						$miniAnnonce = new MiniAnnonce();
                        $miniAnnonce->setTitle($title[$cmpt_miniAnnonce]);
						$miniAnnonce->setDesc($desc[$cmpt_miniAnnonce]);
						$miniAnnonce->setLink($minilinks[$cmpt_miniAnnonce]);
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
	
	public function parseKeywordBing() {

		//Instanciation d'une classe de goutte
		$client = new Client();
		$crawler = $client->request('GET', $this->url);
		//Récupération de tous le code qui concerne les annonces
		$text = $crawler->filter('.sb_add.sb_adTA > .b_caption > *')->extract(array('_text'));
		//Récupération de tous les classes qui concerne les balises des annonces
		$class = $crawler->filter('.sb_add.sb_adTA > .b_caption > *')->extract(array('class'));
		$max = $crawler->filter('.sb_add.sb_adTA > .b_caption')->extract(array('class'));
		if (empty($text)){
			$annonce = new Annonce();
			$annonce->setTitle("Aucune annonce");
			$annonce->setLink("Aucune lien");	
			$annonce->setDesc("Aucune description");
			$allAnnonce[] = $annonce;
		}else{
			$elements = count($text);
			$nbr_annonces = count($max);
			$cmpt = 0;
			$allAnnonce = array();
			while ($cmpt < $nbr_annonces) {
				$annonce = New Annonce();
				$titles = $crawler->filter('.sb_add.sb_adTA > h2 > a')->extract(array('_text'));
				$annonce->setTitle($titles[$cmpt]);
				$links = $crawler->filter('.sb_add.sb_adTA > div > div > cite')->extract(array('_text'));
				$annonce->setLink($links[$cmpt]);
				$allAnnonce[] = $annonce;
				$cmpt = $cmpt +1;
			}
			$cmpt_elements = 0;
			$cmpt_annonces = -1;
			while ($cmpt_elements < $elements){
				if ($class[$cmpt_elements] === 'b_attribution') { 	
					$cmpt_annonces = $cmpt_annonces+1;
				}
				elseif($class[$cmpt_elements] === ''){
					$allAnnonce[$cmpt_annonces]->setDesc($text[$cmpt_elements]);
				}
				elseif ($class[$cmpt_elements] === 'b_secondaryText') {
					$new_extra = New Extra();
					$new_extra->setText($text[$cmpt_elements]);
					$allAnnonce[$cmpt_annonces]->setExtra($new_extra);
				}
				
			$cmpt_elements = $cmpt_elements +1;
			}




		}
		return $allAnnonce;
	}
	}