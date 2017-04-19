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
    //private $userDAO;
    public function setKeywordDAO(KeywordDAO $keywordDAO) {
        $this->keywordDAO = $keywordDAO;
    }
    //public function setUserDAO(UserDAO $userDAO) {
      //  $this->userDAO = $userDAO;
    //}
   
    public function save(Keyword $keyword) {
        $keyword->setUserId(1);
        $keywordData = array(
            'keyword' => $keyword->getKeyword(),
            'user_id' => $keyword->getUserId(),
            );
     
           // insert 
            $this->getDb()->insert('keyword', $keywordData);
            // Get the id of the newly created comment and set it on the entity.
            $id = $this->getDb()->lastInsertId();
            $keyword->setId($id);
    }
    public function update(Keyword $keyword) {
        $keyword->setUserId(1);
        $keywordData = array(
            'keyword' => $keyword->getKeyword(),
            'user_id' => $keyword->getUserId(),
            );
     
           // update 
            $this->getDb()->update('keyword', $keywordData,array('keyword_id' => $keyword->getId()));
    }
    public function delete(Keyword $keyword) {
        $keyword->setUserId(1);
        $keywordData = array(
            'keyword' => $keyword->getKeyword(),
            'user_id' => $keyword->getUserId(),
            );
     
           // delete 
            $this->getDb()->delete('keyword', $keywordData,array('keyword_id' => $keyword->getId()));
            
            
    }
  
    public function allKeyword() {
        $sql = "select * from keyword where user_id =1 order by keyword_id desc ";

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

    public function idKeyword($id) {
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
}