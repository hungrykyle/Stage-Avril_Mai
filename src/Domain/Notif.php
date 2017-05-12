<?php



    class Notif{
        
		private $id;
        private $id_user;
        private $date;
		private $link_notif;
		
	 	public function __construct() {
		$this->id = null;
		$this->id_user = null;
		$this->date = null;
		$this->link_notif = null;
		}
		//Id du rapport
		public function getId() {
			return $this->id;
		}
		public function setId($id) {
			$this->id = $id;
			return $this;
		}
		//Id de l'utilisateur pour qui a été généré cette notification
		public function getIdUser() {
			return $this->id_user;
		}
		public function setIdUser($id_user) {
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
		//Lien où se trouve le pdf sur le serveur
		public function getLinkNotif() {
			return $this->link_notif;
		}
		public function setLinkNotif($link_notif) {
			$this->link_notif = $link_notif;
			return $this;
		}
}