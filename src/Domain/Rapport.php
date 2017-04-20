<?php



    class Rapport{
        
		private $id;
        private $id_user;
        private $date;
        private $keywords;
		private $link_rapport;
		
		//Lien qui correspondra au lien de chaque pdf avant d'ajouter son nom pour l'enregistrer
		private $link;
		
	 	public function __construct() {
		$this->id = null;
		$this->id_user = null;
		$this->date = null;
		$this->keywords = Array();
		$this->link_rapport = $link;
		}
		//Id du rapport
		public function getId() {
			return $this->id;
		}
		public function setId($id) {
			$this->id = $id;
			return $this;
		}
		//Id de l'utilisateur pour qui a été généré ce rapport'
		public function getUserId() {
			return $this->id_user;
		}
		public function setUserId($id_user) {
			$this->id_user = $id_user;
			return $this;
		}
		//Date de création
		public function getDate() {
			return $this->date;
		}
		public function setDate($date) {
			$this->date = $date;
			return $this;
		}
		//Ensemble des mots clés qui ont été utilisé pour créer ce rapport
		public function getKeywords() {
			return $this->keywords;
		}
		public function addKeywords($keyword) {
			$this->keywords[] = $keyword;
			return $this;
		}
		//Lien où se trouve le pdf sur le serveur
		public function getLinkRapport() {
			return $this->link_rapport;
		}
		public function setLinkRapport($link) {
			$this->link_rapport = $link;
			return $this;
		}
}