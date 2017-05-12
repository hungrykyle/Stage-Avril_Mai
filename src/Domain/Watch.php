<?php


class Watch {
        
    /**
    * User id.
    *
    * @var integer
    */
    private $id;
    /**
    * User name.
    *
    * @var string
    */
    private $userid;
    /**
    * User password.
    *
    * @var string
    */
    private $adminid;
    
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
    public function getAdminId() {
        return $this->adminid;
    }
    public function setAdminId($adminid) {
        $this->adminid = $adminid;
        return $adminid;
    }
   

}