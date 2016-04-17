<?php

namespace Maxcraft\DefaultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Builder
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Maxcraft\DefaultBundle\Entity\BuilderRepository")
 */
class Builder
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
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Maxcraft\DefaultBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $user;

    /**
     * @var Zone
     *
     * @ORM\ManyToOne(targetEntity="Maxcraft\DefaultBundle\Entity\Zone", inversedBy="builders")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $zone;

    /**
     * ROLES :
     *
     * CUBO
     * BUILD
     *
     * @var string
     *
     * @ORM\Column(name="role", type="string", length=255)
     */
    private $role;


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
     * Set role
     *
     * @param string $role
     *
     * @return Builder
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
     * Set user
     *
     * @param \Maxcraft\DefaultBundle\Entity\User $user
     *
     * @return Builder
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

    /**
     * Set zone
     *
     * @param \Maxcraft\DefaultBundle\Entity\Zone $zone
     *
     * @return Builder
     */
    public function setZone(Zone $zone)
    {
        $this->zone = $zone;

        return $this;
    }

    /**
     * Get zone
     *
     * @return \Maxcraft\DefaultBundle\Entity\Zone
     */
    public function getZone()
    {
        return $this->zone;
    }
}
