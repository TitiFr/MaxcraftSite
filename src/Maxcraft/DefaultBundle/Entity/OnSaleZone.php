<?php

namespace Maxcraft\DefaultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * OnSaleZone
 *
 * @ORM\Table()
 * @ORM\Entity()
 */
class OnSaleZone
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
     * @var Zone
     * @ORM\OneToOne(targetEntity="Maxcraft\DefaultBundle\Entity\Zone")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $zone;

    /**
     * @var string
     * @ORM\Column(name="price", type="decimal", precision=64, scale=2)
     */
    private $price;

    /**
     * @var boolean
     * @ORM\Column(name="forRent", type="boolean")
     */
    private $forRent;

    /**
     * @var string
     * 
     * @ORM\Column(name="location", type="string", length=50, nullable=true)
     */
    private $location;


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
     * Set price
     *
     * @param string $price
     *
     * @return OnSaleZone
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set forRent
     *
     * @param boolean $forRent
     *
     * @return OnSaleZone
     */
    public function setForRent($forRent)
    {
        $this->forRent = $forRent;

        return $this;
    }

    /**
     * Get forRent
     *
     * @return boolean
     */
    public function getForRent()
    {
        return $this->forRent;
    }

    /**
     * Set location
     *
     * @param string $location
     *
     * @return OnSaleZone
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    public function objectToString(OnSaleZone $saleZone){
        $id = 'id="'.$saleZone->getId().'",';
        $zoneId = 'zoneid="'.$saleZone->getZone()->getId().'",';
        $price = 'price="'.$saleZone->getPrice().'",';
        $forrent = 'forrent="'.$saleZone->getForRent().'",';
        if ($saleZone->getLocation()==null){$location = 'location="null",';}else{$location = 'location="'.$saleZone->getLocation().'",';}

        $str = '-onsalezone:'.$id.$zoneId.$price.$forrent.$location;
        strval($str);
        if ($str[strlen($str)-1]==',') $str[strlen($str)-1]=null;
        return $str;
    }

    /**
     * Set zone
     *
     * @param \Maxcraft\DefaultBundle\Entity\Zone $zone
     *
     * @return OnSaleZone
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
