<?php

namespace Maxcraft\DefaultBundle\Entity;

use Doctrine\DBAL\Types\IntegerType;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Type;

/**
 * WebZone
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Maxcraft\DefaultBundle\Entity\WebZoneRepository")
 */
class WebZone
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
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var Zone
     * @ORM\OneToOne(targetEntity="Maxcraft\DefaultBundle\Entity\Zone", mappedBy="webZone",cascade={"remove", "persist"})
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $servZone;

    /**
     * @var Album
     *
     * @ORM\ManyToOne(targetEntity="Maxcraft\DefaultBundle\Entity\Album")
     * @ORM\JoinColumn(nullable=true)
     */
    private $album;

    /**
     * @var boolean
     *
     * @ORM\Column(name="shopDemand", type="boolean")
     */
    private $shopDemand;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @param Zone $servZone
     */
    public function __construct(Zone $servZone){
        $this->setServZone($servZone);
        $this->setAlbum(null);
        $this->setShopDemand(false);
        $this->setDescription(null);
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
     * Set shopDemand
     *
     * @param boolean $shopDemand
     *
     * @return WebZone
     */
    public function setShopDemand($shopDemand)
    {
        $this->shopDemand = $shopDemand;

        return $this;
    }

    /**
     * Get shopDemand
     *
     * @return boolean
     */
    public function getShopDemand()
    {
        return $this->shopDemand;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return WebZone
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set servZone
     *
     * @param \Maxcraft\DefaultBundle\Entity\Zone $servZone
     *
     * @return WebZone
     */
    public function setServZone(Zone $servZone)
    {
        $this->servZone = $servZone;

        return $this;
    }

    /**
     * Get servZone
     *
     * @return \Maxcraft\DefaultBundle\Entity\Zone
     */
    public function getServZone()
    {
        return $this->servZone;
    }

    /**
     * Set album
     *
     * @param \Maxcraft\DefaultBundle\Entity\Album $album
     *
     * @return WebZone
     */
    public function setAlbum(Album $album = null)
    {
        $this->album = $album;

        return $this;
    }

    /**
     * Get album
     *
     * @return \Maxcraft\DefaultBundle\Entity\Album
     */
    public function getAlbum()
    {
        return $this->album;
    }

    public function getDisplayType()
    {

        if($this->hasDirectlyTag('region')) return 'Region';
        elseif($this->hasDirectlyTag('public')) return 'Zone publique';
        elseif($this->hasDirectlyTag('shop')) return 'Commerce';
        elseif($this->hasDirectlyTag('farm')) return 'Farm';
        else return 'Parcelle';
    }

    public function hasDirectlyTag($tag)
    {
        return in_array($tag, $this->getTagsArray());
    }

    public function getTagsArray()
    {
        if($this->servZone->getTags() == null OR $this->servZone->getTags() == '') return array();
        return explode(';',$this->servZone->getTags());
    }

    public function getCoords()
    {
        //$coords = explode(';',$this->servZone->getPoints());
        list($x,$y,$z) = explode(';',$this->servZone->getPoints());
        /*$pts = array();
        $i = 0;

        foreach($coords as $coord)
        {
            $p_pts = explode(':', $coord);
            if(count($p_pts) != 2) continue;
            $pts[$i]['x'] = $p_pts[0];
            $pts[$i]['z'] = $p_pts[1];
            $i++;
        }*/

        return array(
            'x' => $x,
            'y' => $y,
            'z' => $z
        );

    }

    public function getCenter()
    {
        // Premier point
        return $this->getCoords();
    }

    public function getColor()
    {

        if($this->hasDirectlyTag('region')) return '5E9B59';
        elseif($this->hasDirectlyTag('public')) return '313131';
        elseif($this->hasDirectlyTag('shop')) return '008B9E';
        elseif($this->hasDirectlyTag('farm')) return 'ff0055';
        else return '00AC00';

    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    public function getIcon()
    {
        if($this->hasDirectlyTag('shop')) return 'images/shop.png';
        else return 'images/parcelle.png';

    }
}

