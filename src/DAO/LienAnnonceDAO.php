<?php

require_once __DIR__.'/../DAO/DAO.php';
require_once __DIR__.'/../Domain/LienAnnonce.php';

class LienAnnonceDAO extends DAO 
{
    /**
     * @var 
     */
    private $lienAnnonceDAO;
    /**
     * @var 
     */
    //private $userDAO;
    public function setLienAnnonceDAO(LienAnnonceDAO $lienAnnonceDAO) {
        $this->lienAnnonceDAO = $lienAnnonceDAO;
    }
    //public function setUserDAO(UserDAO $userDAO) {
      //  $this->userDAO = $userDAO;
    //}
   
    public function save(LienAnnonce $lienAnnonce) {
        $lienannonceData = array(
            'lien_title' => $lienAnnonce->getTitle(),
            'lien_link' => $lienAnnonce->getLink(),
            'lien_id_annonce' => $lienAnnonce->getIdAnnonce(),
            );
     
           // insert 
            $this->getDb()->insert('lien_annonce', $lienannonceData);
            // Get the id of the newly created comment and set it on the entity.
            $id = $this->getDb()->lastInsertId();
            $lienAnnonce->setId($id);
    }
    
}