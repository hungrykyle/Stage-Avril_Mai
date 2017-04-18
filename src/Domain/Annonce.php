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
		$this->scoreid = null;
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
		public function getIdUser() {
			return $this->user;
		}
		public function setIdUser($user) {
			$this->user = $user;
			return $this;
		}
		public function getId() {
			return $this->id;
		}
		public function setId($id) {
			$this->id = $id;
			return $this;
		}
		public function getIdkeyword() {
			return $this->idkey;
		}
		public function setIdkeyword($idkey) {
			$this->idkey = $idkey;
			return $this;
		}
		public function getLienAnnonce() {
			return $this->lien_annonce;
		}
		public function setLienAnnonce($lien_annonce) {
			$this->lien_annonce = $lien_annonce;
			return $this;
		}
		public function getMiniAnnonce() {
			return $this->mini_annonce;
		}
		public function setMiniAnnonce($mini_annonce) {
			$this->mini_annonce = $mini_annonce;
			return $this;
		}
		public function getExtra() {
			return $this->extra;
		}
		public function setExtra($extra) {
			$temp = $this->extra;
			$temp[] = $extra;
			$this->extra = $temp;
			return $this;
		}
		public function getScore() {
			return $this->score;
		}
		public function setScore($score) {
			$this->score = $score;
			return $this;
		}
}