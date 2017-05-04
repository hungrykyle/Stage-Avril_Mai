<?php

require_once __DIR__.'/../DAO/DAO.php';
require_once __DIR__.'/../Domain/Score.php';

class ScoreDAO extends DAO 
{
    /**
     * @var 
     */
    private $scoreDAO;
    /**
     * @var 
     */
    private $userDAO;
    public function setScoreDAO(ScoreDAO $scoreDAO) {
        $this->scoreDAO = $scoreDAO;
    }
    public function setUserDAO(UserDAO $userDAO) {
        $this->userDAO = $userDAO;
    }
    /**
    * Enregistre l'objet Score dans la base de donnée.
    *
    * @param Score $score.
    */  
    public function save(Score $score) {
        $scoreData = array(
            'score_note' => $score->getScore(),
            'score_id_annonce' => $score->getIdAnnonce(),
            );
     
           // insert 
            $this->getDb()->insert('score', $scoreData);
            // Get the id of the newly created comment and set it on the entity.
            $id = $this->getDb()->lastInsertId();
            $score->setId($id);
    }
    /**
    * Retourne un objet Score en fonction de l'id de l'annonce.
    *
    * @param $id_ann L'id de l'annonce.
    */
     public function idAnnonceScore($id_ann) {
        #Requête SQL
        $sql = 'select * from score where score_id_annonce ='.$id_ann.' order by score_id desc ';
        $result = $this->getDb()->fetchAll($sql);
        $score = new Score();
        foreach ($result as $row) {
            $score->setId($row['score_id']);
            $score->setIdAnnonce($row['score_id_annonce']);
            $score->setScore($row['score_note']);
        }
        return $score;
        }

    //  #Renvoit tous l'instance de Score en fonction de l'idée d'une annonce  
    //  public function idAnnonceScoreCompare($id_ann) {
    //     #Requête SQL
    //     $sql = 'select * from score where score_id_annonce ='.$id_ann.' order by score_id desc ';
    //     $result = $this->getDb()->fetchAll($sql);
    //     $score = new Score();
    //     foreach ($result as $row) {
    //         $score->setIdAnnonce($row['score_id_annonce']);
    //         $score->setScore($row['score_note']);
    //     }
    //     return $score;
    //     }
    
}
