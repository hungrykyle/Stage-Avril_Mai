<?php


require_once __DIR__.'/../DAO/DAO.php';
require_once __DIR__.'/../Domain/User.php';


use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;




class UserDAO extends DAO implements UserProviderInterface

{


    /**
    * Retourne tous les utilisateurs.
    */

    public function findAll() {
        $sql = "select * from user";
        $result = $this->getDb()->fetchAll($sql);
        #Tableau qui va contenir toutes les annonces
        $users =array();
        foreach ($result as $row) {
            $users[] = $this->buildDomainObject($row);
        }
        return $users;
    
    }
    /**
    * Retourne un utilisateur en fonction de son id
    *
    * @param  $id Id de l'utilisateur.
    *
    */

    public function find($id) {
        $sql = "select * from user where usr_id=?";
        $row = $this->getDb()->fetchAssoc($sql, array($id));
        if ($row)
            return $this->buildDomainObject($row);
        else
            throw new \Exception("No user matching id " . $id);
        }

    /**
    * {@inheritDoc}
    */

    public function loadUserByUsername($username){
        $sql = "select * from user where usr_name=?";
        $row = $this->getDb()->fetchAssoc($sql, array($username));
        if ($row)
            return $this->buildDomainObject($row);
        else
            throw new UsernameNotFoundException(sprintf('User "%s" not found.', $username));
        }


    /**
    * {@inheritDoc}
    */

    public function refreshUser(UserInterface $user){

        $class = get_class($user);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $class));
        }
        return $this->loadUserByUsername($user->getUsername());
    }
    
    /**
    * {@inheritDoc}
    */

    public function supportsClass($class){
        return 'User' === $class;
    }
    /**
     * Enregistre un utilisateur dans la base de donnée.
     *
     * @param User $user Utilisateur a enregistrer.
     */
    public function save(User $user) {
        $userData = array(
            'usr_name' => $user->getUsername(),
            'usr_salt' => $user->getSalt(),
            'usr_mail' => $user->getMail(),
            'usr_password' => $user->getPassword(),
            'usr_role' => $user->getRole()
            );
        if ($user->getId()) {
            // The user has already been saved : update it
            $this->getDb()->update('user', $userData, array('usr_id' => $user->getId()));
        } else {
            // The user has never been saved : insert it
            $this->getDb()->insert('user', $userData);
            // Get the id of the newly created user and set it on the entity.
            $id = $this->getDb()->lastInsertId();
            $user->setId($id);
        }
    }
    /**
     * Supprime un utilisateur de la base de donnée.
     *
     * @param integer $id L'id de l'utilisateur.
     */
    public function delete($id) {
        // Delete the user
        $this->getDb()->delete('user', array('usr_id' => $id));
    }
    /**
    * Creates a User object based on a DB row.
    *
    * @param array $row The DB row containing User data.
    * @return \MicroCMS\Domain\User
    */
    protected function buildDomainObject(array $row) {
        $user = new User();
        $user->setId($row['usr_id']);
        $user->setUsername($row['usr_name']);
        $user->setPassword($row['usr_password']);
        $user->setMail($row['usr_mail']);
        $user->setSalt($row['usr_salt']);
        $user->setRole($row['usr_role']);
            return $user;
        }
    }