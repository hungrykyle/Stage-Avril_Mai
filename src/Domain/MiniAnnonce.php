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
			$this->desc = $desc;
			return $this;
		}
		public function getIdAnnonce() {
			return $this->id_annonce;
		}
		public function setIdAnnonce($id_annonce) {
		$this->id_annonce = $id_annonce;
			return $this;
		}
		public function getId() {
			return $this->id;
		}
		public function setId($id) {
			$this->id = $id;
			return $this;
		}
}