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
    #Enregistrement d'une instance de MiniAnnonce dans la base de données
    public function save(MiniAnnonce $miniAnnonce) {
        $miniannonceData = array(
            'mini_title' => $miniAnnonce->getTitle(),
            'mini_id_annonce' => $miniAnnonce->getIdAnnonce(),
            'mini_link' => $miniAnnonce->getLink(),
            'mini_desc' => $miniAnnonce->getDesc()
            );
     
           // insert 
            $this->getDb()->insert('mini_annonce', $miniannonceData);
            // Get the id of the newly created comment and set it on the entity.
            $id = $this->getDb()->lastInsertId();
            $miniAnnonce->setId($id);
    }
    #Renvoit tous les instances de MiniAnnonce en fonction de l'idée d'une annonce  
    public function idAnnonceMini($id_ann) {
        #Requête SQL
        $sql = 'select * from mini_annonce where mini_id_annonce ='.$id_ann.' order by mini_id desc ';
        $result = $this->getDb()->fetchAll($sql);
        $allMiniAnnonce = array();
        foreach ($result as $row) {
            $miniAnnonce = new MiniAnnonce();
            $miniAnnonce->setId($row['mini_id']);
            $miniAnnonce->setIdAnnonce($row['mini_id_annonce']);
            $miniAnnonce->setTitle($row['mini_title']);
            $miniAnnonce->setLink($row['mini_link']);
            $miniAnnonce->setDesc($row['mini_desc']);
            $allMiniAnnonce[] = $miniAnnonce;
        }
        return $allMiniAnnonce;
    }

     #Renvoit tous les instances de MiniAnnonce en fonction de l'idée d'une annonce  
    public function idAnnonceMiniCompare($id_ann) {
        #Requête SQL
        $sql = 'select * from mini_annonce where mini_id_annonce ='.$id_ann.' order by mini_id desc ';
        $result = $this->getDb()->fetchAll($sql);
        $allMiniAnnonce = array();
        foreach ($result as $row) {
            $miniAnnonce = new MiniAnnonce();
            $miniAnnonce->setIdAnnonce($row['mini_id_annonce']);
            $miniAnnonce->setTitle($row['mini_title']);
            $miniAnnonce->setLink($row['mini_link']);
            $miniAnnonce->setDesc($row['mini_desc']);
            $allMiniAnnonce[] = $miniAnnonce;
        }
        return $allMiniAnnonce;
    }
}



