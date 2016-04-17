<?php

namespace Maxcraft\DefaultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Maxcraft\DefaultBundle\Controller\DefaultController;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Email;

/**
 * User
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Maxcraft\DefaultBundle\Entity\UserRepository")
 */
class User implements UserInterface
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
     * @var Player
     *
     * @ORM\OneToOne(targetEntity="Maxcraft\DefaultBundle\Entity\Player")
     * @ORM\JoinColumn(nullable=false)
     */
    private $player;

    /**
     * @var string
     *
     * @ORM\Column(name="uuid", type="string", length=255, unique=true)
     */
    private $uuid;

    /**
     * @var string
     * @Assert\NotBlank(message = "Vous devez entrer un pseudo !")
     * @Assert\Length(min=2, minMessage="Le pseudo doit contenir au moins 2 caractères !")
     * @ORM\Column(name="username", type="string", length=255, unique=true)
     */
    private $username;

    /**
     * @var Faction
     *
     * @ORM\ManyToOne(targetEntity="Maxcraft\DefaultBundle\Entity\Faction")
     * @ORM\JoinColumn(nullable=true, unique=false)
     */
    private $faction;

    /**
     * @var string
     * @Assert\NotBlank(message = "Vous devez entrer une adresse email !")
     * @Assert\Email(message = "Adresse email invalide !")
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="registerDate", type="datetime")
     */
    private $registerDate;

    /**
     * @var string
     * @Assert\Length(min=8, minMessage = "Votre mot de passe doit au moins contenir 8 caractères !")
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="salt", type="string", length=32)
     */
    private $salt;

    /**
     * @var string
     *
     * @ORM\Column(name="color", type="string", length=6)
     */
    private $color;

    /**
     * @var string
     *
     * @ORM\Column(name="role", type="string", length=255)
     */
    private $role;

    /**
     * @var integer
     *
     * '1' => 'Recrue',
     *'2' => 'Membre',
     *'9' => 'Chef',
     *'10' => 'Fondateur (Attention ! Vous perdrez votre grade !)',
     *
     * @ORM\Column(name="factionrole", type="integer")
     */
    private $factionrole;

    /**
     * @var boolean
     *
     * @ORM\Column(name="banned", type="boolean")
     */
    private $banned;

    /**
     * @var boolean
     *
     * @ORM\Column(name="actif", type="boolean")
     */
    private $actif;

    /**
     * @var boolean
     *
     * @ORM\Column(name="spleeping", type="boolean")
     */
    private $spleeping;

    /**
     * @var integer
     * @ORM\Column(name="naissance", type="integer")
     */
    private $naissance;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=30)
     */
    private $ip;

    /**
     * @var string
     * @Assert\Length(max = 255, maxMessage = "Le champs <loisirs> doit contenir moins de 255 caractères.")
     * @ORM\Column(name="loisirs", type="string", length=300)
     */
    private $loisirs;

    /**
     * @var string
     * @Assert\Length(max = 255, maxMessage = "Le champs <Comment avez-vous connu Maxcraft.fr?> doit contenir moins de 255 caractères.")
     * @ORM\Column(name="fromwhere", type="string", length=255, nullable=true)
     */
    private $fromwhere;

    /**
     * @var string
     *
     * @ORM\Column(name="profil", type="text", nullable = true)
     */
    private $profil;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="text", nullable=true)
     */
    private $note;

    /**
     * @var string
     * @Assert\Length(max = 250, maxMessage = "Le champs <Profession/Etudes> doit contenir moins de 250 caractères.")
     * @ORM\Column(name="activite", type="string", length=255, nullable = true)
     */
    private $activite;

    /**
     * @var integer
     *
     * @ORM\Column(name="gametime", type="integer")
     */
    private $gametime;



    public $controller;

    /**
     * @param DefaultController $controller
     * @param Player $player
     */
    function __construct(DefaultController $controller, Player $player = null){
        $this->controller = $controller;
        $this->registerDate = new \DateTime();
        $this->password = NULL;
        $this->salt = md5(uniqid(null, true));
        $this->role = 'ROLE_USER';
        $this->banned = false;
        $this->color='111111';
        $this->profil = NULL;
        $this->actif = true;
        $this->sleeping = false;
        $this->factionrole = 1;
        $this->gametime = 0;
        $this->note = null;
        $this->player = $player;
    }


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
     * Set uuid
     *
     * @param string $uuid
     *
     * @return User
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * Get uuid
     *
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return User
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
     * Set email
     *
     * @param string $email
     *
     * @return User
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
     * Set registerDate
     *
     * @param \DateTime $registerDate
     *
     * @return User
     */
    public function setRegisterDate(\Datetime $registerDate)
    {
        $this->registerDate = $registerDate;

        return $this;
    }

    /**
     * Get registerDate
     *
     * @return \DateTime
     */
    public function getRegisterDate()
    {
        return $this->registerDate;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set salt
     *
     * @param string $salt
     *
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Get salt
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set color
     *
     * @param string $color
     *
     * @return User
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color
     *
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set role
     *
     * @param string $role
     *
     * @return User
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set factionrole
     *
     * @param integer $factionrole
     *
     * @return User
     */
    public function setFactionrole($factionrole)
    {
        $this->factionrole = $factionrole;

        return $this;
    }

    /**
     * Get factionrole
     *
     * @return integer
     */
    public function getFactionrole()
    {
        return $this->factionrole;
    }

    /**
     * Set banned
     *
     * @param boolean $banned
     *
     * @return User
     */
    public function setBanned($banned)
    {
        $this->banned = $banned;

        return $this;
    }

    /**
     * Get banned
     *
     * @return boolean
     */
    public function getBanned()
    {
        return $this->banned;
    }

    /**
     * Set actif
     *
     * @param boolean $actif
     *
     * @return User
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * Get actif
     *
     * @return boolean
     */
    public function getActif()
    {
        return $this->actif;
    }

    /**
     * Set spleeping
     *
     * @param boolean $spleeping
     *
     * @return User
     */
    public function setSpleeping($spleeping)
    {
        $this->spleeping = $spleeping;

        return $this;
    }

    /**
     * Get spleeping
     *
     * @return boolean
     */
    public function getSpleeping()
    {
        return $this->spleeping;
    }

    /**
     * Set naissance
     *
     * @param integer $naissance
     *
     * @return User
     */
    public function setNaissance($naissance)
    {
        $this->naissance = $naissance;

        return $this;
    }

    /**
     * Get naissance
     *
     * @return integer
     */
    public function getNaissance()
    {
        return $this->naissance;
    }

    /**
     * Set ip
     *
     * @param string $ip
     *
     * @return User
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set loisirs
     *
     * @param string $loisirs
     *
     * @return User
     */
    public function setLoisirs($loisirs)
    {
        $this->loisirs = $loisirs;

        return $this;
    }

    /**
     * Get loisirs
     *
     * @return string
     */
    public function getLoisirs()
    {
        return $this->loisirs;
    }

    /**
     * Set fromwhere
     *
     * @param string $fromwhere
     *
     * @return User
     */
    public function setFromwhere($fromwhere)
    {
        $this->fromwhere = $fromwhere;

        return $this;
    }

    /**
     * Get fromwhere
     *
     * @return string
     */
    public function getFromwhere()
    {
        return $this->fromwhere;
    }

    /**
     * Set profil
     *
     * @param string $profil
     *
     * @return User
     */
    public function setProfil($profil)
    {
        $this->profil = $profil;

        return $this;
    }

    /**
     * Get profil
     *
     * @return string
     */
    public function getProfil()
    {
        return $this->profil;
    }

    /**
     * Set note
     *
     * @param string $note
     *
     * @return User
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set activite
     *
     * @param string $activite
     *
     * @return User
     */
    public function setActivite($activite)
    {
        $this->activite = $activite;

        return $this;
    }

    /**
     * Get activite
     *
     * @return string
     */
    public function getActivite()
    {
        return $this->activite;
    }

    /**
     * Set gametime
     *
     * @param integer $gametime
     *
     * @return User
     */
    public function setGametime($gametime)
    {
        $this->gametime = $gametime;

        return $this;
    }

    /**
     * Get gametime
     *
     * @return integer
     */
    public function getGametime()
    {
        return $this->gametime;
    }

    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return Role[] The user roles
     */
    public function getRoles()
    {
        return array($this->role);
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
    }


    /**
     * @param $password
     */
    public function cryptePassword($password){
        $factory = $this->controller->get('security.encoder_factory');
        $encoder = $factory->getEncoder($this);
        $password = $encoder->encodePassword($password, $this->salt);
        $this->password = $password;
    }

    /**
     * @param $taille
     * @return string
     */
    public function getAvatar($taille){
        return 'https://minotar.net/avatar/'.$this->username.'/'.$taille.'';
    }

    public function isAdmin(){
        if ($this->role == 'ROLE_ADMIN') return true;
        return false;
    }

    public function getPseudo()
    {
        $profilpath = $this->controller->generateUrl('profil', array('pseudo' => $this->username));
        return '<a href="'.$profilpath.'">'.$this->username.'</a>';
    }

    public function getFactionTitle()
    {
        switch($this->factionrole)
        {
            case 1:
                return 'Recrue';
                break;
            case 2:
                return 'Membre';
                break;
            case 9:
                return 'Chef';
                break;
            case 10:
                return 'Fondateur';
                break;
            default:
                return 'Recrue';
                break;

        }
    }

    public function isFactionOwner()
    {
        if($this->faction != NULL){
            if($this->factionrole == 10){
                return true;
            }
            return false;
        }
        return false;
    }


    /**
     * Set player
     *
     * @param \Maxcraft\DefaultBundle\Entity\Player $player
     *
     * @return User
     */
    public function setPlayer(Player $player)
    {
        $this->player = $player;

        return $this;
    }

    /**
     * Get player
     *
     * @return \Maxcraft\DefaultBundle\Entity\Player
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * Set faction
     *
     * @param \Maxcraft\DefaultBundle\Entity\Faction $faction
     *
     * @return User
     */
    public function setFaction(Faction $faction = null)
    {
        $this->faction = $faction;

        return $this;
    }

    /**
     * Get faction
     *
     * @return \Maxcraft\DefaultBundle\Entity\Faction
     */
    public function getFaction()
    {
        return $this->faction;
    }

}
