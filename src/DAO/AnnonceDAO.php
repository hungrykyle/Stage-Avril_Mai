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
    #Enregistrement d'une annonce dans la base de données
    public function save(Annonce $annonce) {
        #On simule le user pour le moment
        $annonce->setIdUser(2);
        $annonceData = array(
            'user_id' => $annonce->getIdUser(),
            'keyword_id' => $annonce->getIdkeyword(),
            'ann_title' => $annonce->getTitle(),
            'ann_link' => $annonce->getLink(),
            'ann_desc' => $annonce->getDesc(),
            'ann_research' => $annonce->getResearch(),
            'ann_date' => $annonce->getDate()
            );
     
           // insert 
            $this->getDb()->insert('annonce', $annonceData);
            // Get the id of the newly created comment and set it on the entity.
            $id = $this->getDb()->lastInsertId();
            $annonce->setId($id);
    }
    #Renvoit tous les annonces en fonction de l'idée d'un mot    
    public function allAnnonce($id_word) {
        #Requête SQL
        $sql = 'select * from annonce where user_id =2 AND keyword_id='.$id_word.' order by ann_id desc';
        $result = $this->getDb()->fetchAll($sql);
        #Tableau qui va contenir toutes les annonces
        $annonces =array();
        foreach ($result as $row) {
            $annonceId = $row['ann_id'];
            $annonce = new Annonce();
            $annonce->setId($row['ann_id']);
            $annonce->setIdUser($row['user_id']);
            $annonce->setTitle($row['ann_title']);
            $annonce->setLink($row['ann_link']);
            $annonce->setDesc($row['ann_desc']);
            $annonce->setResearch($row['ann_research']);
            $annonce->setDate($row['ann_date']);
            $annonce->setIdKeyword($row['keyword_id']);
            $annonces[$annonceId] = $annonce;
        }
        return $annonces;
    }
    #Renvoit tous les annonces en fonction de l'idée d'un mot et de la date
     public function allAnnonceByDate($id_word,$date) {
        #Requête SQL
        $sql = 'select * from annonce where user_id =2 AND ann_date = \''.$date->format('Y-m-d').'\'  AND keyword_id='.$id_word.' order by ann_id desc';
        $result = $this->getDb()->fetchAll($sql);
        #Tableau qui va contenir toutes les annonces
        $annonces =array();
        foreach ($result as $row) {
            $annonceId = $row['ann_id'];
            $annonce = new Annonce();
            $annonce->setId($row['ann_id']);
            $annonce->setIdUser($row['user_id']);
            $annonce->setTitle($row['ann_title']);
            $annonce->setLink($row['ann_link']);
            $annonce->setDesc($row['ann_desc']);
            $annonce->setResearch($row['ann_research']);
            $annonce->setDate($row['ann_date']);
            $annonce->setIdKeyword($row['keyword_id']);
            $annonces[$annonceId] = $annonce;
        }
     
        return $annonces;
    }
    
}