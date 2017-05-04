<?php

require_once __DIR__.'/../DAO/DAO.php';
require_once __DIR__.'/../Domain/Keyword.php';

class KeywordDAO extends DAO 
{
    /**
     * @var 
     */
    private $lienAnnonceDAO;
    /**
     * @var 
     */
    private $userDAO;
    public function setKeywordDAO(KeywordDAO $keywordDAO) {
        $this->keywordDAO = $keywordDAO;
    }
    public function setUserDAO(UserDAO $userDAO) {
        $this->userDAO = $userDAO;
    }
    /**
    * Enregistrement l'objet Keyword dans la base de donnée.
    *
    * @param Keyword $keyword.
    */  
    public function save(Keyword $keyword) {
        $keywordData = array(
            'keyword' => $keyword->getKeyword(),
            'user_id' => $keyword->getUserId()->getId(),
            );
     
           // insert 
            $this->getDb()->insert('keyword', $keywordData);
            // Get the id of the newly created comment and set it on the entity.
            $id = $this->getDb()->lastInsertId();
            $keyword->setId($id);
    }
    /**
    * Modifie un mot clé dans la base de donnée.
    *
    * @param Keyword $keyword.
    */ 
    public function update(Keyword $keyword) {
        $keywordData = array(
            'keyword' => $keyword->getKeyword(),
            'user_id' => $keyword->getUserId()->getId(),
            );
     
           // update 
            $this->getDb()->update('keyword', $keywordData,array('keyword_id' => $keyword->getId()));
    }
    /**
    * Supprime un mot clé dans la base de donnée.
    *
    * @param Keyword $keyword.
    */ 
    public function delete(Keyword $keyword) {
        $keywordData = array(
            'keyword' => $keyword->getKeyword(),
            'user_id' => $keyword->getUserId()->getId(),
            );
            // delete 
            $this->getDb()->delete('keyword', $keywordData,array('keyword_id' => $keyword->getId()));
    }
    /**
    * Retourne tous les mots clés d'un utilisateur.
    *
    * @param User $user.
    */ 
    public function allKeywordByUser(User $user) {
        #Requête SQL
        $sql = 'select * from keyword where user_id ='.$user->getId().' order by keyword_id desc';
        $result = $this->getDb()->fetchAll($sql);
        $keywords =array();
        foreach ($result as $row) {
            $keywordId = $row['keyword_id'];
            $keyword = new Keyword();
            $keyword->setId($row['keyword_id']);
            $keyword->setUserId($row['user_id']);
            $keyword->setKeyword($row['keyword']);
            $keywords[$keywordId] = $keyword;
        }
     return $keywords;
    }
    /**
    * Retourne un mot clé en fonction de l'id.
    *
    * @param $id Id du mot clé demandé.
    */ 
    public function idKeyword($id) {
        #Requête SQL
        $sql = 'select * from keyword where keyword_id ='.$id.' order by keyword_id desc ';
        $result = $this->getDb()->fetchAll($sql);
        $keyword = new Keyword();
        foreach ($result as $row) {
            $keyword->setId($row['keyword_id']);
            $keyword->setUserId($row['user_id']);
            $keyword->setKeyword($row['keyword']);
        }
        return $keyword;
    }
    /**
    * Supprime tous les mots clés d'un utilisateur.
    *
    * @param integer $userId Id de l'utilisateur.
    */
    public function deleteAllByUser($userId) {
        $this->getDb()->delete('keyword', array('user_id' => $userId));
    }
}