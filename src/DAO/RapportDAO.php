<?php

require_once __DIR__.'/../DAO/DAO.php';
require_once __DIR__.'/../Domain/Rapport.php';

class RapportDAO extends DAO 
{
    /**
     * @var 
     */
    private $rapportDAO;
    /**
     * @var 
     */
    private $userDAO;
    public function setRapportDAO(RapportDAO $rapportDAO) {
        $this->rapportDAO = $rapportDAO;
    }
    public function setUserDAO(UserDAO $userDAO) {
        $this->userDAO = $userDAO;
    }
    /**
    * Enregistre l'objet Rapport dans la base de donnée.
    *
    * @param Rapport $rapport.
    */  
    public function save(Rapport $rapport) {
       $rapportData = array(
            'user_id' => $rapport->getIdUser()->getId(),
            'rap_date' => $rapport->getDate()->format('Y-m-d'),
            'rap_link' => $rapport->getLinkRapport()
            );
     
           // insert 
            $this->getDb()->insert('rapport', $rapportData);
            // Get the id of the newly created comment and set it on the entity.
            $id = $this->getDb()->lastInsertId();
            $rapport->setId($id);
    }
   
}
