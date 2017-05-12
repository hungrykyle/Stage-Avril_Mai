<?php

require_once __DIR__.'/../DAO/DAO.php';
require_once __DIR__.'/../Domain/Watch.php';

class WatchDAO extends DAO 
{
    /**
     * @var 
     */
    private $watchDAO;
    /**
     * @var 
     */
    private $userDAO;
    public function setWatchDAO(WatchDAO $watchDAO) {
        $this->watchDAO = $watchDAO;
    }
    public function setUserDAO(UserDAO $userDAO) {
        $this->userDAO = $userDAO;
    }
    /**
    * Enregistre l'objet Watch dans la base de donnée.
    *
    * @param Watch $watch.
    */  
    public function save(Watch $watch) {
        $watchData = array(
            'watch_admin' => $watch->getAdminId(),
            'watch_user' => $watch->getUserId(),
            );
     
           // insert 
            $this->getDb()->insert('watch', $watchData);
            // Get the id of the newly created comment and set it on the entity.
            $id = $this->getDb()->lastInsertId();
            $watch->setId($id);
    }

    public function allWatch() {
        #Requête SQL
        $sql = 'select * from watch';
        $result = $this->getDb()->fetchAll($sql);
        #Tableau qui va contenir toutes les watch
        $allwatch =array();
        foreach ($result as $row) {
            $watchId = $row['watch_id'];
            $watch = new Watch();
            $watch->setId($row['watch_id']);
            $watch->setUserId($row['watch_user']);
            $watch->setAdminId($row['watch_admin']);
            $allwatch[$watchId] = $watch;
        }
        return $allwatch;
    }
    public function allWatchByAdmin(User $user) {
        #Requête SQL
        $sql = 'select * from watch where watch_admin='.$user->getId().' order by watch_id';
        $result = $this->getDb()->fetchAll($sql);
        #Tableau qui va contenir toutes les watch
        $allwatch =array();
        foreach ($result as $row) {
            $watchId = $row['watch_id'];
            $watch = new Watch();
            $watch->setId($row['watch_id']);
            $watch->setUserId($row['watch_user']);
            $watch->setAdminId($row['watch_admin']);
            $allwatch[$watchId] = $watch;
        }
        return $allwatch;
    }
    public function allWatchByUser(User $user) {
        #Requête SQL
        $sql = 'select * from watch where watch_user='.$user->getId().' order by watch_id';
        $result = $this->getDb()->fetchAll($sql);
        #Tableau qui va contenir toutes les watch
        $allwatch =array();
        foreach ($result as $row) {
            $watchId = $row['watch_id'];
            $watch = new Watch();
            $watch->setId($row['watch_id']);
            $watch->setUserId($row['watch_user']);
            $watch->setAdminId($row['watch_admin']);
            $allwatch[$watchId] = $watch;
        }
        return $allwatch;
    }
     /**
     * Supprime un watch de la base de donnée.
     *
     * @param integer $id L'id du watch.
     */
    public function delete($id) {
        // Delete the user
        $this->getDb()->delete('watch', array('watch_id' => $id));
    }
    
}
