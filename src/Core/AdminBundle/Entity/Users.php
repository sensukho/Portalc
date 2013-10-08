<?php

namespace Core\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Users
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Core\AdminBundle\Entity\UsersRepository")
 */
class Users
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=64)
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="secondname", type="string", length=64)
     */
    private $secondname;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="genpass", type="string", length=255)
     */
    private $genpass;

    /**
     * @var string
     *
     * @ORM\Column(name="newpass", type="string", length=255)
     */
    private $newpass;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="date")
     */
    private $fecha;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return radcheck
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     * @return radcheck
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    
        return $this;
    }

    /**
     * Get firstname
     *
     * @return string 
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set secondname
     *
     * @param string $secondname
     * @return radcheck
     */
    public function setSecondname($secondname)
    {
        $this->secondname = $secondname;
    
        return $this;
    }

    /**
     * Get secondname
     *
     * @return string 
     */
    public function getSecondname()
    {
        return $this->secondname;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return Users
     */
    public function setUsername($username)
    {
        $this->username = $username;
    
        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set genpass
     *
     * @param string $genpass
     * @return Users
     */
    public function setGenpass($genpass)
    {
        $this->genpass = $genpass;
    
        return $this;
    }

    /**
     * Get genpass
     *
     * @return string 
     */
    public function getGenpass()
    {
        return $this->genpass;
    }

    /**
     * Set newpass
     *
     * @param string $newpass
     * @return Users
     */
    public function setNewpass($newpass)
    {
        $this->newpass = $newpass;
    
        return $this;
    }

    /**
     * Get newpass
     *
     * @return string 
     */
    public function getNewpass()
    {
        return $this->newpass;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return Users
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    
        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime 
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Users
     */
    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }
}
