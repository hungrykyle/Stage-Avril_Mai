<?php



    class LienAnnonce{
        
        private $id;
		private $id_annonce;
        private $title;
		private $link;
        

		//Titre
		public function getTitle() {
			return $this->title;
		}
		public function setTitle($title) {
			$this->title = $title;
			return $this;
		}
		//Lien
		public function getLink() {
			return $this->link;
		}
		public function setLink($link) {
			$this->link = $link;
			return $this;
		}
		// Id
		public function getId() {
			return $this->id;
		}
		public function setId($id) {
			$this->id = $id;
			return $this;
		}
		// Id de l'annonce
		public function getIdAnnonce() {
			return $this->id_annonce;
		}
		public function setIdAnnonce($id_annonce) {
			$this->id_annonce = $id_annonce;
			return $this;
		}
		
}