<?php

namespace Maxcraft\DefaultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Merchant
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Merchant
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
     * @ORM\Column(name="items", type="string", length=100000)
     */
    private $items;

    /**
     * @var string
     *
     * @ORM\Column(name="prices", type="string", length=255)
     */
    private $prices;

    /**
     * @var string
     *
     * @ORM\Column(name="inv", type="text")
     */
    private $inv;


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
     * @return Merchant
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
     * Set items
     *
     * @param string $items
     *
     * @return Merchant
     */
    public function setItems($items)
    {
        $this->items = $items;

        return $this;
    }

    /**
     * Get items
     *
     * @return string
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Set prices
     *
     * @param string $prices
     *
     * @return Merchant
     */
    public function setPrices($prices)
    {
        $this->prices = $prices;

        return $this;
    }

    /**
     * Get prices
     *
     * @return string
     */
    public function getPrices()
    {
        return $this->prices;
    }

    /**
     * Set inv
     *
     * @param string $inv
     *
     * @return Merchant
     */
    public function setInv($inv)
    {
        $this->inv = $inv;

        return $this;
    }

    /**
     * Get inv
     *
     * @return string
     */
    public function getInv()
    {
        return $this->inv;
    }
}

