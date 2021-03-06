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
  
    public function setLienAnnonceDAO(LienAnnonceDAO $lienAnnonceDAO) {
        $this->lienAnnonceDAO = $lienAnnonceDAO;
    }
    /**
    * Enregistre un objet LienAnnonce dans la base de donnée.
    *
    * @param LienAnnonce $lienAnnonce.
    */ 
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
    /**
    * Retourne un tableau d'objet LienAnnonce en fonction de l'id de l'annonce.
    *
    * @param $id_ann L'id de l'annonce.
    */
    public function idAnnonceLien($id_ann) {
        #Requête SQL
        $sql = 'select * from lien_annonce where lien_id_annonce ='.$id_ann.' order by lien_id desc ';
        $result = $this->getDb()->fetchAll($sql);
        $allLienAnnonce = array();
        foreach ($result as $row) {
            $lienAnnonce = new LienAnnonce();
            $lienAnnonce->setId($row['lien_id']);
            $lienAnnonce->setIdAnnonce($row['lien_id_annonce']);
            $lienAnnonce->setTitle($row['lien_title']);
            $lienAnnonce->setLink($row['lien_link']);
            $allLienAnnonce[] = $lienAnnonce;
        }
        return $allLienAnnonce;
    }

    // #Renvoit tous les instances de LienAnnonce en fonction de l'idée d'une annonce sans les id des LienAnnonce pour comparer
    // public function idAnnonceLienCompare($id_ann) {
    //     #Requête SQL
    //     $sql = 'select * from lien_annonce where lien_id_annonce ='.$id_ann.' order by lien_id desc ';
    //     $result = $this->getDb()->fetchAll($sql);
    //     $allLienAnnonce = array();
    //     foreach ($result as $row) {
    //         $lienAnnonce = new LienAnnonce();
    //         $lienAnnonce->setIdAnnonce($row['lien_id_annonce']);
    //         $lienAnnonce->setTitle($row['lien_title']);
    //         $lienAnnonce->setLink($row['lien_link']);
    //         $allLienAnnonce[] = $lienAnnonce;
    //     }
    //     return $allLienAnnonce;
    // }
    
}