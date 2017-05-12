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
    * frequency
    * Values : QUOTIDIEN or HEBDOMADAIRE or MENSUEL.
    *
    * @var string
    */
    private $frequency;
    /**
    * Mail
    * Values : E-mail.
    *
    * @var string
    */
    private $mail;
    /**
    * Avatar
    * Values : Lien vers une image.
    *
    * @var string
    */
    private $avatar;
    /**
    * Watch
    * Values : Objet Watch.
    *
    * @var Watch
    */
    private $watch;
    
    
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
    public function getAvatar()
    {
        return $this->avatar;
    }
    public function setAvatar($avatar) {
        $this->avatar = $avatar;
        return $this;
    }
    public function getWatch()
    {
        return $this->watch;
    }
    public function setWatch($watch) {
        $this->watch = $watch;
        return $this;
    }
    public function getRoles()
    {
        return array($this->getRole());
    }
    public function getFrequency()
    {
        return $this->frequency;
    }
    public function setFrequency($frequency)
    {
        $this->frequency = $frequency;
        return $this;
    }
    public function eraseCredentials() {
        // Nothing to do here
    }

}