<?php

require_once __DIR__.'/../DAO/DAO.php';

class ScoreDAO extends DAO 
{
    /**
     * @var 
     */
    private $scoreDAO;
    /**
     * @var 
     */
    //private $userDAO;
    public function setScoreDAO(ScoreDAO $scoreDAO) {
        $this->scoreDAO = $scoreDAO;
    }
    //public function setUserDAO(UserDAO $userDAO) {
      //  $this->userDAO = $userDAO;
    //}
   
    public function save(Score $score) {
        $scoreData = array(
            'score' => $score->getScore(),
            'score_id_annonce' => $score->getIdAnnonce(),
            );
     
           // insert 
            $this->getDb()->insert('lien_annonce', $lienannonceData);
            // Get the id of the newly created comment and set it on the entity.
            $id = $this->getDb()->lastInsertId();
            $score->setId($id);
    }
    
}