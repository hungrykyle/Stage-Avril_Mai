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
        $annonce->setIdUser(2);
        $annonceData = array(
            'user_id' => $annonce->getIdUser(),
            'keyword_id' => $annonce->getIdkeyword(),
            'ann_title' => $annonce->getTitle(),
            'ann_link' => $annonce->getLink(),
            'ann_desc' => $annonce->getDesc(),
            'ann_nav' => $annonce->getNav(),
            'ann_date' => $annonce->getDate()
            );
     
           // insert 
            $this->getDb()->insert('annonce', $annonceData);
            // Get the id of the newly created comment and set it on the entity.
            $id = $this->getDb()->lastInsertId();
            $annonce->setId($id);
           
    }


    public function allAnnonce($id_word) {
        $sql = 'select * from annonce where user_id =2 AND keyword_id='.$id_word.' order by ann_id desc';

        $result = $this->getDb()->fetchAll($sql);
        $annonces =array();
        foreach ($result as $row) {
            $annonceId = $row['ann_id'];
            $annonce = new Annonce();
            $annonce->setId($row['ann_id']);
            $annonce->setIdUser($row['user_id']);
            $annonce->setTitle($row['ann_title']);
            $annonce->setLink($row['ann_link']);
            $annonce->setDesc($row['ann_desc']);
            $annonce->setNav($row['ann_nav']);
            $annonce->setDate($row['ann_date']);
            $annonce->setIdKeyword($row['keyword_id']);
            $annonces[$annonceId] = $annonce;
        }
     
        return $annonces;
    }
     public function allAnnonceByDate($id_word,$date) {
        $sql = 'select * from annonce where user_id =2 AND ann_date > '.$date.' AND keyword_id='.$id_word.' order by ann_id desc';

        $result = $this->getDb()->fetchAll($sql);
        $annonces =array();
        foreach ($result as $row) {
            $annonceId = $row['ann_id'];
            $annonce = new Annonce();
            $annonce->setId($row['ann_id']);
            $annonce->setIdUser($row['user_id']);
            $annonce->setTitle($row['ann_title']);
            $annonce->setLink($row['ann_link']);
            $annonce->setDesc($row['ann_desc']);
            $annonce->setNav($row['ann_nav']);
            $annonce->setDate($row['ann_date']);
            $annonce->setIdKeyword($row['keyword_id']);
            $annonces[$annonceId] = $annonce;
        }
     
        return $annonces;
    }
    
}