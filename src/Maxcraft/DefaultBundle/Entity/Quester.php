<?php

namespace Maxcraft\DefaultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Quester
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Quester
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
     * @ORM\Column(name="uuid", type="string", length=255)
     */
    private $uuid;

    /**
     * @var string
     *
     * @ORM\Column(name="quest", type="string", length=255)
     */
    private $quest;

    /**
     * @var string
     *
     * @ORM\Column(name="player", type="text")
     */
    private $player;

    /**
     * @var string
     *
     * @ORM\Column(name="stats", type="string", length=10000)
     */
    private $stats;


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
     * @return Quester
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
     * Set quest
     *
     * @param string $quest
     *
     * @return Quester
     */
    public function setQuest($quest)
    {
        $this->quest = $quest;

        return $this;
    }

    /**
     * Get quest
     *
     * @return string
     */
    public function getQuest()
    {
        return $this->quest;
    }

    /**
     * Set player
     *
     * @param string $player
     *
     * @return Quester
     */
    public function setPlayer($player)
    {
        $this->player = $player;

        return $this;
    }

    /**
     * Get player
     *
     * @return string
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * Set stats
     *
     * @param string $stats
     *
     * @return Quester
     */
    public function setStats($stats)
    {
        $this->stats = $stats;

        return $this;
    }

    /**
     * Get stats
     *
     * @return string
     */
    public function getStats()
    {
        return $this->stats;
    }
}

