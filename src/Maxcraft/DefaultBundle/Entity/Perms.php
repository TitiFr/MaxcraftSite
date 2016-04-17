<?php

namespace Maxcraft\DefaultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Perms
 *
 * @ORM\Table()
 * @ORM\Entity()
 */
class Perms
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /** @var User
     *
     * @ORM\OneToOne(targetEntity="Maxcraft\DefaultBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $user;

    /**
     * @var string
     * @ORM\Column(name="groupName", type="string", length=255, unique=true)
     */
    private $groupName;

    /**
     * @var string
     *
     * @ORM\Column(name="perms", type="text")
     */
    private $perms;


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
     * Set groupName
     *
     * @param string $groupName
     *
     * @return Perms
     */
    public function setGroupName($groupName)
    {
        $this->groupName = $groupName;

        return $this;
    }

    /**
     * Get groupName
     *
     * @return string
     */
    public function getGroupName()
    {
        return $this->groupName;
    }

    /**
     * Set perms
     *
     * @param string $perms
     *
     * @return Perms
     */
    public function setPerms($perms)
    {
        $this->perms = $perms;

        return $this;
    }

    /**
     * Get perms
     *
     * @return string
     */
    public function getPerms()
    {
        return $this->perms;
    }

    public function objectToString(Perms $perm){
        $id = 'id="'.$perm->getId().'",';
        $user = 'uuid="'.$perm->getUser()->getUuid().'",';
        $groupName = 'groupname="'.$perm->getGroupName().'",';
        $perms = 'perms="'.$perm->getPerms().'",';

        $str = '-perms:'.$id.$user.$groupName.$perms;
        strval($str);
        if ($str[strlen($str)-1]==',') $str[strlen($str)-1]=null;
        return $str;
    }

    /**
     * Set user
     *
     * @param \Maxcraft\DefaultBundle\Entity\User $user
     *
     * @return Perms
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Maxcraft\DefaultBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
