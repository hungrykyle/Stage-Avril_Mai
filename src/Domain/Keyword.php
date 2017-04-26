<?php



    class Keyword{
        
        private $id;
		private $user_id;
        private $keyword;
        

		//Id
		public function getId() {
			return $this->id;
		}
		public function setId($id) {
			$this->id = $id;
			return $this;
		}
		//Id de l'utilisateur qui possède le mot clé
		public function getUserId() {
			return $this->userid;
		}
		public function setUserId($userid) {
			$this->userid = $userid;
			return $this;
		}
		//Le mot clé
		public function getKeyword() {
			return $this->keyword;
		}
		public function setKeyword($keyword) {
			$this->keyword = $keyword;
			return $this;
		}
		
}