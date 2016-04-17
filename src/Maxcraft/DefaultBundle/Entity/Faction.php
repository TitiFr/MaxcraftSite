<?php

namespace Maxcraft\DefaultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

/**
 * Faction
 *
 * @ORM\Table()
 * @ORM\Entity()
 */
class Faction
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
     * @ORM\Column(name="uuid", type="string", length=255, unique=true, nullable=false)
     */
    private $uuid;

    /**
     * @var string
     * @Assert\NotBlank(message = "Vous devez donner un nom à votre faction !")
     * @Assert\Length(min = 3, minMessage = "Le nom de votre faction doit contenir au minimum 3 caractères !")
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var string
     * @Assert\NotBlank(message = "Vous n'avez pas créé de tag pour votre faction !")
     * @Assert\Length(min = 2, max = 6, minMessage="Le tag doit contenir 2 caractères au minimum.", maxMessage = "Le tag doit contenir moins de 6 caractères.")
     * @ORM\Column(name="tag", type="string", length=255, unique=true)
     */
    private $tag;

    /**
     * @var string
     *
     * @ORM\Column(name="balance", type="decimal", precision=64, scale=2)
     */
    private $balance;

    /**
     * @var string
     *
     * @ORM\Column(name="spawn", type="string", length=255, nullable=true)
     */
    private $spawn;

    /**
     * @var string
     *
     * @ORM\Column(name="jail", type="string", length=255, nullable=true)
     */
    private $jail;

    /**
     * @var User
     *
     * @ORM\OneToOne(targetEntity="Maxcraft\DefaultBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $owner;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Maxcraft\DefaultBundle\Entity\User")
     * @ORM\JoinColumn(name="heads", nullable=true)
     */
    private $heads;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Maxcraft\DefaultBundle\Entity\User")
     * @ORM\JoinColumn(name="members", nullable=true)
     */
    private $members;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Maxcraft\DefaultBundle\Entity\User")
     * @ORM\JoinColumn(name="recruits", nullable=true)
     */
    private $recruits;

    /**
     * @var Album
     *
     * @ORM\ManyToOne(targetEntity="Maxcraft\DefaultBundle\Entity\Album")
     * @ORM\JoinColumn(name="album", nullable=true)
     */
    private $album;

    /**
     * @var string
     *
     * @ORM\Column(name="icon", type="string", length=300, nullable=true)
     */
    private $icon;

    /**
     * @var string
     *
     * @ORM\Column(name="banner", type="text", nullable=true)
     */
    private $banner;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable = true, unique = false)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="joininfo", type="text", nullable=true, unique=false)
     */
    private $joininfo;


    public function __construct(){
        $this->balance = 0;
        $this->uuid = uniqid("fac",false);
        $this->description = null;
        $this->joininfo = "Pas de conditions d'entrée pour cette faction !";
    }

    /**
     * @param Faction $faction
     * @return string
     */
    public  function objectToString(Faction $faction){ //TODO Revoir + gérer alliés et ennemis

        $id = "id=".'"'.$faction->getId().'",';
        $uuid = 'uuid="'.$faction->getUuid().'",';
        if($faction->getName()== null){$name = 'name="null",';} else{$name = "name=".'"'.$faction->getName().'",';}
        $tag="tag=".'"'.$faction->getTag().'",';
        $balance = 'balance="'.$faction->getBalance().'",';
        if ($faction->getSpawn()==null){$spawn = 'spawn="null",';} else{$spawn = 'spawn="'.$faction->getSpawn().'",';}
        if ($faction->getJail() == null){$jail = 'jail="null",';} else {$jail = 'jail:"'.$faction->getJail().'",';}
        $owner = "owner=".'"'.$faction->getOwner()->getUuid().'",';
        if ($faction->getHeads()==null){$heads = 'heads="null",';} else{$heads = "heads=".'"'.$faction->getHeads().'",';}
        if ($faction->getMembers()==null){$members = 'members="null",';} else{$members = "members=".'"'.$faction->getMembers().'",';}
        if ($faction->getRecruits()==null){$recruits = 'recruits="null",';} else{$recruits = "recruits=".'"'.$faction->getRecruits().'",';}
        if ($faction->getEnemies()==null){$enemies = 'enemies="null",';} else{$enemies = "enemies=".'"'.$faction->getEnemies().'",';}
        if ($faction->getAllies()==null){$allies = 'allies="null",';} else{$allies = "allies=".'"'.$faction->getAllies().'",';}
        if ($faction->getIcon()==null){$icon = 'icon="null",';} else{$icon = "icon=".'"'.$faction->getIcon().'",';}
        if ($faction->getBanner()==null){$banner = 'banner="null",';} else{$banner = "banner=".'"'.$faction->getBanner().'",';}

        $str = "-faction:".$id.$uuid.$name.$tag.$balance.$spawn.$jail.$owner.$heads.$members.$recruits.$enemies.$allies.$icon.$banner;
        strval($str);
        if ($str[strlen($str)-1]==',') $str[strlen($str)-1]=null;
        return $str;
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
     * @return Faction
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
     * Set name
     *
     * @param string $name
     *
     * @return Faction
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
     * Set tag
     *
     * @param string $tag
     *
     * @return Faction
     */
    public function setTag($tag)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Get tag
     *
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Set balance
     *
     * @param string $balance
     *
     * @return Faction
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;

        return $this;
    }

    /**
     * Get balance
     *
     * @return string
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * Set spawn
     *
     * @param string $spawn
     *
     * @return Faction
     */
    public function setSpawn($spawn)
    {
        $this->spawn = $spawn;

        return $this;
    }

    /**
     * Get spawn
     *
     * @return string
     */
    public function getSpawn()
    {
        return $this->spawn;
    }

    /**
     * Set jail
     *
     * @param string $jail
     *
     * @return Faction
     */
    public function setJail($jail)
    {
        $this->jail = $jail;

        return $this;
    }

    /**
     * Get jail
     *
     * @return string
     */
    public function getJail()
    {
        return $this->jail;
    }

    /**
     * Set icon
     *
     * @param string $icon
     *
     * @return Faction
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Get icon
     *
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * Set banner
     *
     * @param string $banner
     *
     * @return Faction
     */
    public function setBanner($banner)
    {
        $this->banner = $banner;

        return $this;
    }

    /**
     * Get banner
     *
     * @return string
     */
    public function getBanner()
    {
        return $this->banner;
    }

    /**
     * Set owner
     *
     * @param \Maxcraft\DefaultBundle\Entity\User $owner
     *
     * @return Faction
     */
    public function setOwner(\Maxcraft\DefaultBundle\Entity\User $owner)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \Maxcraft\DefaultBundle\Entity\User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set heads
     *
     * @param \Maxcraft\DefaultBundle\Entity\User $heads
     *
     * @return Faction
     */
    public function setHeads(User $heads = null)
    {
        $this->heads = $heads;

        return $this;
    }

    /**
     * Get heads
     *
     * @return \Maxcraft\DefaultBundle\Entity\User
     */
    public function getHeads()
    {
        return $this->heads;
    }

    /**
     * Set members
     *
     * @param \Maxcraft\DefaultBundle\Entity\User $members
     *
     * @return Faction
     */
    public function setMembers(User $members = null)
    {
        $this->members = $members;

        return $this;
    }

    /**
     * Get members
     *
     * @return \Maxcraft\DefaultBundle\Entity\User
     */
    public function getMembers()
    {
        return $this->members;
    }

    /**
     * Set recruits
     *
     * @param \Maxcraft\DefaultBundle\Entity\User $recruits
     *
     * @return Faction
     */
    public function setRecruits(User $recruits = null)
    {
        $this->recruits = $recruits;

        return $this;
    }

    /**
     * Get recruits
     *
     * @return \Maxcraft\DefaultBundle\Entity\User
     */
    public function getRecruits()
    {
        return $this->recruits;
    }

    public function getAvatar($size)
    {
        if($this->icon == null OR $this->aicon == '')
        {
            return $this->getOwner()->getAvatar($size);
        }
        else
        {
            return $this->icon;
        }
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getJoininfo()
    {
        return $this->joininfo;
    }

    /**
     * @param string $joininfo
     */
    public function setJoininfo($joininfo)
    {
        $this->joininfo = $joininfo;
    }

    /**
     * @return Album
     */
    public function getAlbum()
    {
        return $this->album;
    }

    /**
     * @param Album $album
     */
    public function setAlbum($album)
    {
        $this->album = $album;
    }



}
