<?php

require_once __DIR__.'/../DAO/DAO.php';
require_once __DIR__.'/../Domain/Extra.php';

class ExtraDAO extends DAO 
{
    /**
     * @var 
     */
    private $ExtraDAO;
    /**
     * @var 
     */
    //private $userDAO;
    public function setExtraDAO(ExtraDAO $ExtraDAO) {
        $this->ExtraDAO = $ExtraDAO;
    }
    //public function setUserDAO(UserDAO $userDAO) {
      //  $this->userDAO = $userDAO;
    //}
   #Enregistrement d'une instance de Extra dans la base de données
    public function save(Extra $extra) {
        $extraData = array(
            'extra_text' => $extra->getText(),
            'extra_id_annonce' => $extra->getIdAnnonce(),
            
            );
     
           // insert 
            $this->getDb()->insert('extra', $extraData);
            // Get the id of the newly created comment and set it on the entity.
            $id = $this->getDb()->lastInsertId();
            $extra->setId($id);
    }
    #Renvoit tous les instances de Extra en fonction de l'idée d'une annonce  
    public function idAnnonceExtra($id_ann) {
        
        $sql = 'select * from extra where extra_id_annonce ='.$id_ann.' order by extra_id desc ';

        $result = $this->getDb()->fetchAll($sql);
        $allExtra = array();
        foreach ($result as $row) {
            $extra = new Extra();
            $extra->setId($row['extra_id']);
            $extra->setIdAnnonce($row['extra_id_annonce']);
            $extra->setText($row['extra_text']);
            $allExtra[] = $extra;
        }
     
        return $allExtra;
    }

    #Renvoit tous les instances de Extra en fonction de l'idée d'une annonce sans leur id pour pouvoir comparer les annonces entre elles 
    public function idAnnonceExtraCompare($id_ann) {
        
        $sql = 'select * from extra where extra_id_annonce ='.$id_ann.' order by extra_id desc ';

        $result = $this->getDb()->fetchAll($sql);
        $allExtra = array();
        foreach ($result as $row) {
            $extra = new Extra();
            $extra->setIdAnnonce($row['extra_id_annonce']);
            $extra->setText($row['extra_text']);
            $allExtra[] = $extra;
        }
     
        return $allExtra;
    }
}