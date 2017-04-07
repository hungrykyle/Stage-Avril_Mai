<?php



    class Candidat{
        
        private $nom;
        private $prenom;
        private $parti;
        private $img;
        private $pourcentage;


        public function __construct($id, $nom, $prenom, $parti, $img, $pourcentage) {
		$this->id = $id;
		$this->nom = $nom;
		$this->prenom = $prenom;
		$this->parti = $parti;
		$this->img = $img;
		$this->pourcentage = $pourcentage;
	}
	

	public function getId() {
		return $this->id;
	}

	public function setId() {
		$this->id = $id;
		return $this;
	}

	public function getNom() {
		return $this->nom;
	}

	public function setNom($nom) {

        $this->nom = $nom;
	    return $this;
	}

	
	public function getPrenom() {
		return $this->prenom;
	}

	public function setPrenom($prenom) {

        $this->prenom = $prenom;
	    return $this;
	}

    public function getCompleteNom() {
		return $this->prenom.' '.$this->nom;
	}
	
	public function getParti() {
		return $this->parti;
	}
	
	public function setParti($parti) {

        $this->parti = $parti;
	    return $this;
	}
	
	public function getImg() {
		return $this->img;
	}

	public function setImg($img) {

        $this->img = $img;
	    return $this;
	}

	public function getPourcentage() {
		return $this->pourcentage;
	}

	public function setPourcentage($pourcentage) {

        $this->pourcentage = $pourcentage;
	    return $this;
	}



}