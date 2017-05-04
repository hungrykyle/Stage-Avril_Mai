<?php



use Symfony\Component\Security\Core\User\UserInterface;


class User implements UserInterface{
        
    /**
    * User id.
    *
    * @var integer
    */
    private $id;
    /**
    * User name.
    *
    * @var string
    */
    private $username;
    /**
    * User password.
    *
    * @var string
    */
    private $password;
    /**
    * Salt that was originally used to encode the password.
    *
    * @var string
    */
    private $salt;
    /**
    * Role.
    * Values : ROLE_USER or ROLE_ADMIN.
    *
    * @var string
    */
    private $role;
    /**
    * Mail
    * Values : E-mail.
    *
    * @var string
    */
    private $mail;
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function getUsername() {
        return $this->username;
    }
    public function setUsername($username) {
        $this->username = $username;
        return $this;
    }
    public function getPassword() {
        return $this->password;
    }
    public function setPassword($password) {
        $this->password = $password;
        return $this;
    }
    public function getSalt()
    {
        return $this->salt;
    }
    public function setSalt($salt)
    {
        $this->salt = $salt;
        return $this;
    }
    public function getRole()
    {
        return $this->role;
    }
    public function setRole($role) {
        $this->role = $role;
        return $this;
    }
    public function getMail()
    {
        return $this->mail;
    }
    public function setMail($mail) {
        $this->mail = $mail;
        return $this;
    }
    public function getRoles()
    {
        return array($this->getRole());
    }
    public function eraseCredentials() {
        // Nothing to do here
    }

}