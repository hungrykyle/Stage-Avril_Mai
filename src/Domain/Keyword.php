<?php



    class Keyword{
        
        private $id;
		private $user_id;
        private $keyword;
        

	
		public function getId() {
			return $this->id;
		}
		public function setId($id) {
			$this->id = $id;
			return $this;
		}
		public function getUserId() {
			return $this->userid;
		}
		public function setUserId($userid) {
			$this->userid = $userid;
			return $this;
		}
		public function getKeyword() {
			return $this->keyword;
		}
		public function setKeyword($keyword) {
			$this->keyword = $keyword;
			return $this;
		}
		
}