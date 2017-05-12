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
            'user_id' => $rapport->getIdUser(),
            'rap_date' => $rapport->getDate()->format('Y-m-d'),
            'rap_link' => $rapport->getLinkRapport()
            );
     
           // insert 
            $this->getDb()->insert('rapport', $rapportData);
            // Get the id of the newly created comment and set it on the entity.
            $id = $this->getDb()->lastInsertId();
            $rapport->setId($id);
    }

    /**
    * Renvoit un tableau d'objet Rapport en fonction de la date.
    *
    * @param $date Date.
    */  
     public function allRapportByDate($date) {
        #Requête SQL
        $sql = 'select * from rapport where  rap_date = \''.$date->format('Y-m-d').'\' order by rap_id desc';
        $result = $this->getDb()->fetchAll($sql);
        #Tableau qui va contenir toutes les annonces
        $rapports =array();
        foreach ($result as $row) {
            $rapportId = $row['rap_id'];
            $rapport = new Rapport();
            $rapport->setId($row['rap_id']);
            $rapport->setIdUser($row['user_id']);
            $rapport->setDate($row['rap_date']);
            $rapport->setLinkRapport($row['rap_link']);
            $rapports[] = $rapport;
        }
     
        return $rapports;
    }
   
}
