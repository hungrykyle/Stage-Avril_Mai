<?php



    class Annonce{
        
        private $title;
        private $link;
        private $desc;
     
	 	public function __construct($title, $link, $desc) {
		$this->title = $title;
		$this->link = $link;
		$this->desc = $desc;
		}
		//Titre
		public function getTitle() {
			return $this->title;
		}
		//Lien donné sur une annonce
		public function getLink() {
			return $this->link;
		}
		//Description de l'annonce
		public function getDesc() {
			return $this->desc;
		}
}