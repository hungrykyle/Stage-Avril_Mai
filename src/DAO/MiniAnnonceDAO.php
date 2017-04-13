<?php

require_once __DIR__.'/../DAO/DAO.php';
require_once __DIR__.'/../Domain/MiniAnnonce.php';

class MiniAnnonceDAO extends DAO 
{
    /**
     * @var 
     */
    private $miniAnnonceDAO;
    /**
     * @var 
     */
    //private $userDAO;
    public function setMiniAnnonceDAO(MiniAnnonceDAO $miniAnnonceDAO) {
        $this->miniAnnonceDAO = $miniAnnonceDAO;
    }
    //public function setUserDAO(UserDAO $userDAO) {
      //  $this->userDAO = $userDAO;
    //}
   
    public function save(MiniAnnonce $miniAnnonce) {
        $miniannonceData = array(
            'mini_title' => $miniAnnonce->getTitle(),
            'mini_id_annonce' => $miniAnnonce->getIdAnnonce(),
            'mini_desc' => $miniAnnonce->getDesc()
            );
     
           // insert 
            $this->getDb()->insert('mini_annonce', $miniannonceData);
            // Get the id of the newly created comment and set it on the entity.
            $id = $this->getDb()->lastInsertId();
            $miniAnnonce->setId($id);
    }
    
}