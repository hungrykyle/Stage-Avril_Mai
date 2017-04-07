<?php

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
		//Scrapping des titres des annonces uniquement
		$h3 = $crawler->filter('li.ads-ad > h3');
		foreach ($h3 as $value) {	
			$title[]=$value->textContent;
		}
   	 	//Scrapping des liens
		$cite = $crawler->filter('cite._WGk');
		foreach ($cite as $value) {	
			$link[]=$value->textContent;
		}
		//Scrapping des descriptions. A optimiser.
		$span = $crawler->filter('li.ads-ad > div.ellip');
       	foreach ($span as $value) {	
			$desc[]=$value->textContent;
		}
		//Création d'une classe annonce. A optimiser.
		$max = count($title);
		$cmpt = 0;
		//Création d'une classe Annonce pour chaque annonce
		while ($cmpt <= $max-1)
		{
			$allAnnonce[] = new Annonce ($title[$cmpt],$link[$cmpt],$desc[$cmpt]);
			$cmpt++; // $cmpt = $cmpt + 1
		}
		return $allAnnonce;
	}
	
	}
