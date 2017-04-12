<?php



    class Score{
        
        private $id;
		private $id_annonce;
        private $score;
        

		//Titre
		public function getScore() {
			return $this->score;
		}
		public function setScore($score) {
			$this->score = $score;
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