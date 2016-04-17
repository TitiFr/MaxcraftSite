<?php

namespace Maxcraft\DefaultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Player
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Maxcraft\DefaultBundle\Entity\PlayerRepository")
 */
class Player
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
     * @ORM\Column(name="uuid", type="string", length=255, unique=true)
     */
    private $uuid;

    /**
     * @var string
     * @ORM\Column(name="pseudo", type="string", length=255, unique=true)
     */
    private $pseudo;

    /**
     * @var string
     *
     * @ORM\Column(name="balance", type="decimal", precision=64, scale=2)
     */
    private $balance;

    /**
     * @var boolean
     *
     * @ORM\Column(name="actif", type="boolean")
     */
    private $actif;

    /**
     * @var boolean
     * @ORM\Column(name="vanished", type="boolean")
     */
    private $vanished;

    /**
     * @var integer
     *
     * @ORM\Column(name="gametime", type="integer")
     */
    private $gametime;

    public function __contrsuct(){
        $this->balance = 0;
        $this->actif = true;
        $this->gametime = 0;
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
     * @return Player
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
     * Set pseudo
     *
     * @param string $pseudo
     *
     * @return Player
     */
    public function setPseudo($pseudo)
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    /**
     * Get pseudo
     *
     * @return string
     */
    public function getPseudo()
    {
        return $this->pseudo;
    }

    /**
     * Set balance
     *
     * @param string $balance
     *
     * @return Player
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
     * Set actif
     *
     * @param boolean $actif
     *
     * @return Player
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
     * @return bool
     */
    public function getVanished(){
        return $this->vanished;
    }

    /**
     * @param $vanished
     */
    public function setVanished($vanished){
        $this->vanished = $vanished;
    }

    public function objectToString(Player $player){
        $id = 'id="'.$player->getId().'",';
        $uuid = 'uuid="'.$player->getUuid().'",';
        $pseudo = 'pseudo="'.$player->getPseudo().'",';
        $balance = 'balance="'.$player->getBalance().'",';
        $actif = 'actif="'.$player->getActif().'",';
        $vanished = 'vanished="'.$player->getVanished().'",';
        $gametime = 'gametime="'.$player->getGametime().'",';

        $str = '-player:'.$id.$uuid.$pseudo.$balance.$actif.$vanished.$gametime;
        strval($str);
        if ($str[strlen($str)-1]==',') $str[strlen($str)-1]=null;
        return $str;
    }

    /**
     * Set gametime
     *
     * @param integer $gametime
     *
     * @return Player
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
}
