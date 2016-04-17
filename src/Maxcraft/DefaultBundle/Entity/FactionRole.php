<?php

namespace Maxcraft\DefaultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FactionRole
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Maxcraft\DefaultBundle\Entity\FactionRoleRepository")
 */
class FactionRole
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
     * @var Faction
     *
     * @ORM\ManyToOne(targetEntity="Maxcraft\DefaultBundle\Entity\Faction")
     * @ORM\JoinColumn(name="faction", nullable=false)
     */
    private $faction1;

    /**
     * @var Faction
     *
     * @ORM\ManyToOne(targetEntity="Maxcraft\DefaultBundle\Entity\Faction")
     * @ORM\JoinColumn(name="tothisfaction", nullable=false)
     */
    private $faction2;

    /**
     * @var string
     *
     * FRIEND
     * NEUTRE
     * ENEMY
     *
     * @ORM\Column(name="hasRole", type="string", length=255)
     */
    private $hasRole;

    /**
     * @var datetime
     * @ORM\Column(name="since", type="datetime", nullable=false)
     */
    private $since;

    public function __controller(){
        $this->since = new \DateTime();
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
     * Set faction1
     *
     * @param Faction $faction
     *
     * @return FactionRole
     */
    public function setFaction1($faction)
    {
        $this->faction1 = $faction;

        return $this;
    }

    /**
     * Get faction1
     *
     * @return Faction
     */
    public function getFaction1()
    {
        return $this->faction1;
    }

    /**
     * Set faction2
     *
     * @param  $faction
     *
     * @return FactionRole
     */
    public function setFaction2($faction)
    {
        $this->faction2 = $faction;

        return $this;
    }

    /**
     * Get faction2
     *
     * @return integer
     */
    public function getFaction2()
    {
        return $this->faction2;
    }

    /**
     * Set hasRole
     *
     * @param string $hasRole
     *
     * @return FactionRole
     */
    public function setHasRole($hasRole)
    {
        $this->hasRole = $hasRole;

        return $this;
    }

    /**
     * Get hasRole
     *
     * @return string
     */
    public function getHasRole()
    {
        return $this->hasRole;
    }

    /**
     * Set since
     *
     * @param \DateTime $since
     *
     * @return FactionRole
     */
    public function setSince($since)
    {
        $this->since = $since;

        return $this;
    }

    /**
     * Get since
     *
     * @return \DateTime
     */
    public function getSince()
    {
        return $this->since;
    }
}
