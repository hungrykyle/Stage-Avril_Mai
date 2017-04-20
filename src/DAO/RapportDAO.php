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
    //private $userDAO;
    public function setRapportDAO(RapportDAO $rapportDAO) {
        $this->rapportDAO = $rapportDAO;
    }
    //public function setUserDAO(UserDAO $userDAO) {
      //  $this->userDAO = $userDAO;
    //}
   
   
}
