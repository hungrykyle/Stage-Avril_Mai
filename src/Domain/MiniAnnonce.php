<?php

	class MiniAnnonce{
        
        private $title;
        private $desc;
		private $id_annonce;
		private $id;
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
		//Description 
		public function getDesc() {
			return $this->desc;
		}
		public function setDesc($desc) {
			$this->desc = $desc;
			return $this;
		}
		//Id de l'annonce associée
		public function getIdAnnonce() {
			return $this->id_annonce;
		}
		public function setIdAnnonce($id_annonce) {
		$this->id_annonce = $id_annonce;
			return $this;
		}
		//Id de la mini annonce
		public function getId() {
			return $this->id;
		}
		public function setId($id) {
			$this->id = $id;
			return $this;
		}
}