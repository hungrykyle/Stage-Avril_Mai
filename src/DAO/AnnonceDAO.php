<?php

require_once __DIR__.'/../DAO/DAO.php';
require_once __DIR__.'/../Domain/Annonce.php';

class AnnonceDAO extends DAO 
{
    /**
     * @var 
     */
    private $annonceDAO;
    /**
     * @var 
     */
    //private $userDAO;
    public function setAnnonceDAO(ArticleDAO $annonceDAO) {
        $this->annonceDAO = $annonceDAO;
    }
    //public function setUserDAO(UserDAO $userDAO) {
      //  $this->userDAO = $userDAO;
    //}
   
    public function save(Annonce $annonce) {
        $annonceData = array(
            'user_id' => $annonce->getIdUser(),
            'keyword_id' => $annonce->getIdkeyword(),
            'ann_title' => $annonce->getTitle(),
            'ann_link' => $annonce->getLink(),
            'ann_desc' => $annonce->getDesc()
            );
     
           // insert 
            $this->getDb()->insert('annonce', $annonceData);
            // Get the id of the newly created comment and set it on the entity.
            $id = $this->getDb()->lastInsertId();
            $annonce->setId($id);
    }
    
    
}