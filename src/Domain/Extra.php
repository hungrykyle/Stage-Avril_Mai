<?php



    class Extra{
        
        private $id;
		private $id_annonce;
        private $text;
        

		//Titre
		public function getText() {
			return $this->text;
		}
		public function setText($text) {
			$this->text = $text;
			return $this;
		}
		public function getId() {
			return $this->id;
		}
		public function setId($id) {
			$this->id = $id;
			return $this;
		}
		public function getIdAnnonce() {
			return $this->id_annonce;
		}
		public function setIdAnnonce($id_annonce) {
			$this->id_annonce = $id_annonce;
			return $this;
		}
		
}