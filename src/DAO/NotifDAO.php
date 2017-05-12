<?php

require_once __DIR__.'/../DAO/DAO.php';
require_once __DIR__.'/../Domain/Notif.php';

class NotifDAO extends DAO 
{
    /**
     * @var 
     */
    private $notifDAO;
    /**
     * @var 
     */
    private $userDAO;
    public function setNotifDAO(NotifDAO $notifDAO) {
        $this->notifDAO = $notifDAO;
    }
    public function setUserDAO(UserDAO $userDAO) {
        $this->userDAO = $userDAO;
    }
    /**
    * Enregistre l'objet Notif dans la base de donnée.
    *
    * @param Notif $notifDAO.
    */  
    public function save(Notif $notif) {
       $notifData = array(
            'user_id' => $notif->getIdUser(),
            'not_date' => $notif->getDate()->format('Y-m-d'),
            'not_link' => $notif->getLinkNotif()
            );
     
           // insert 
            $this->getDb()->insert('notif', $notifData);
            // Get the id of the newly created comment and set it on the entity.
            $id = $this->getDb()->lastInsertId();
            $notif->setId($id);
    }

    /**
    * Renvoit un tableau d'objet Notif en fonction de la date.
    *
    * @param $date Date.
    */  
     public function allNotifByDate($date) {
        #Requête SQL
        $sql = 'select * from notif where  not_date = \''.$date->format('Y-m-d H:i:s').'\' order by not_id desc';
        $result = $this->getDb()->fetchAll($sql);
        #Tableau qui va contenir toutes les annonces
        $notifs =array();
        foreach ($result as $row) {
            $notifId = $row['not_id'];
            $notif = new Notif();
            $notif->setId($row['not_id']);
            $notif->setIdUser($row['user_id']);
            $notif->setDate($row['not_date']);
            $notif->setLinkNotif($row['not_link']);
            $notifs[] = $notif;
        }
     
        return $notifs;
    }
   
}
