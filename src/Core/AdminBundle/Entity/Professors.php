<?php

namespace Core\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Professors
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Core\AdminBundle\Entity\ProfessorsRepository")
 */
class Professors
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
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=255)
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="secondname", type="string", length=255)
     */
    private $secondname;

    /**
     * @var string
     *
     * @ORM\Column(name="matricula", type="string", length=255)
     */
    private $matricula;

    /**
     * @var string
     *
     * @ORM\Column(name="newpasssecond", type="string", length=255)
     */
    private $newpasssecond;


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
     * Set username
     *
     * @param string $username
     * @return Professors
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
     * @return Professors
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
     * @return Professors
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
     * Set email
     *
     * @param string $email
     * @return Professors
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

    /**
     * Set firstname
     *
     * @param string $firstname
     * @return Professors
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
     * @return Professors
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
     * Set matricula
     *
     * @param string $matricula
     * @return Professors
     */
    public function setMatricula($matricula)
    {
        $this->matricula = $matricula;
    
        return $this;
    }

    /**
     * Get matricula
     *
     * @return string 
     */
    public function getMatricula()
    {
        return $this->matricula;
    }

    /**
     * Set newpasssecond
     *
     * @param string $newpasssecond
     * @return Professors
     */
    public function setNewpasssecond($newpasssecond)
    {
        $this->newpasssecond = $newpasssecond;
    
        return $this;
    }

    /**
     * Get newpasssecond
     *
     * @return string 
     */
    public function getNewpasssecond()
    {
        return $this->newpasssecond;
    }
}
