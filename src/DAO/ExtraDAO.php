<?php

require_once __DIR__.'/../DAO/DAO.php';
require_once __DIR__.'/../Domain/MiniAnnonce.php';

class ExtraDAO extends DAO 
{
    /**
     * @var 
     */
    private $ExtraDAO;
    /**
     * @var 
     */
    //private $userDAO;
    public function setExtraDAO(ExtraDAO $ExtraDAO) {
        $this->ExtraDAO = $ExtraDAO;
    }
    //public function setUserDAO(UserDAO $userDAO) {
      //  $this->userDAO = $userDAO;
    //}
   
    public function save(Extra $extra) {
        $extraData = array(
            'extra_text' => $extra->getText(),
            'extra_id_annonce' => $extra->getIdAnnonce(),
            
            );
     
           // insert 
            $this->getDb()->insert('extra', $extraData);
            // Get the id of the newly created comment and set it on the entity.
            $id = $this->getDb()->lastInsertId();
            $extra->setId($id);
    }
    
}