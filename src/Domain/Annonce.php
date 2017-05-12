<?php



    class Annonce{
        
		private $title;
        private $link;
        private $desc;
        private $user;
        private $id;
		private $idkey;
		private $lien_annonce;
		private $mini_annonce;
		private $extra;
		private $score;
		private $research;
		private $date;
		private $all_extra;


        public function __construct() {
		$this->title = null;
		$this->link = null;
		$this->desc = '';
		$this->user = null;
		$this->id = null;
		$this->idkey = null;
		$this->lien_annonce = null;
		$this->mini_annonce = null;
		$this->extra = Array();
		$this->allextra = '';
		$this->scoreid = null;
		$this->research = null;
		$this->date = null;
	}
		
     	//Titre
		public function getTitle() {
			return $this->title;
		}
		public function setTitle($title) {
			$this->title = $title;
			return $this;
		}
		//Lien donné sur une annonce
		public function getLink() {
			return $this->link;
		}
		public function setLink($link) {
			$this->link = $link;
			return $this;
		}
		//Description de l'annonce
		public function getDesc() {
			return $this->desc;
		}
		public function setDesc($desc) {
			$this->desc .= $desc.' ';
			return $this;
		}
		//Id de l'utilisateur à qui appartient l'annonce
		public function getIdUser() {
			return $this->user;
		}
		public function setIdUser($user) {
			$this->user = $user;
			return $this;
		}
		//Id de l'annonce'
		public function getId() {
			return $this->id;
		}
		public function setId($id) {
			$this->id = $id;
			return $this;
		}
		//Id du mot clé associé à l'annonce'
		public function getIdkeyword() {
			return $this->idkey;
		}
		public function setIdkeyword($idkey) {
			$this->idkey = $idkey;
			return $this;
		}
		//Les liens de l'annonces associés dans l'ordre alphabétique
		public function getLienAnnonce() {
			if (isset($this->lien_annonce)){
				$all_lien_annonce = $this->lien_annonce;
				foreach ($all_lien_annonce as $key => $value) {
					$all_title[] = $value -> getTitle();
				}
				sort($all_title);
				$nbr = count ($all_lien_annonce);
				$i = 0;
				$res = array();
				while ($i < $nbr){
					foreach ($all_lien_annonce as $key => $value) {
						if ($value->getTitle() === $all_title[$i]){
							$res[$i] = $value;
							$i = $i + 1;
						}
					}
				}
				$this->lien_annonce = $res;
				return $this->lien_annonce;
			}else{
				return $this->lien_annonce;
			}
			
		}
		public function setLienAnnonce($lien_annonce) {
			$this->lien_annonce = $lien_annonce;
			return $this;
		}
		//Les minis annonces associées
		public function getMiniAnnonce() {
			if (isset($this->mini_annonce)){
				$all_mini_annonce = $this->mini_annonce;
				foreach ($all_mini_annonce as $key => $value) {
					$all_title[] = $value -> getTitle();
				}
				sort($all_title);
				$nbr = count ($all_mini_annonce);
				$i = 0;
				$res = array();
				while ($i < $nbr){
					foreach ($all_mini_annonce as $key => $value) {
						if ($value->getTitle() === $all_title[$i]){
							$res[$i] = $value;
							$i = $i + 1;
						}
					}
				}
				return $res;
			}else{
				return $this->mini_annonce;
			}
			$this->mini_annonce = $mini_annonce;
			return $this;
		}
		//Compte le nombre de Mini Annonce (pour l'affichage twig')
		public function getCountMiniAnnonce() {
			return (12/count($this->mini_annonce));
		}
		public function setMiniAnnonce($mini_annonce) {
			$this->mini_annonce = $mini_annonce;
			return $this;
		}
		// Les instances de Extra associés
		public function getExtra() {
			return $this->extra;
		}
		// Crée une chaîne de caractère avec toutes les instances de Extra associés
		public function getStringExtra() {
			$all_extra='';
			foreach ($this->extra as $key => $value) {
				$all_extra .= $value->getText().' ';
			}
			$this->all_extra = $all_extra;
			return $this->all_extra;
		}
		public function setExtra($extra) {
			$temp = $this->extra;
			$temp[] = $extra;
			$this->extra = $temp;
			return $this;
		}
		// Le score associé
		public function getScore() {
			return $this->score;
		}
		public function setScore($score) {
			$this->score = $score;
			return $this;
		}
		// Le moteur de recherche qui a trouvé l'annonce
		public function getResearch() {
			return $this->research;
		}
		public function setResearch($research) {
			$this->research = $research;
			return $this;
		}
		// La date associée à l'annonce
		public function getDate() {
			return $this->date;
		}
		public function setDate($date) {
			$this->date = $date;
			return $this;
		}

}