<?php

namespace Maxcraft\DefaultBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Zone
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Maxcraft\DefaultBundle\Entity\ZoneRepository")
 */
class Zone
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /*/**
     * @var WebZone
     *
     * @ORM\OneToOne(targetEntity="Maxcraft\DefaultBundle\Entity\WebZone", inversedBy="servZone", cascade={"remove", "persist"})
     * @ORM\JoinColumn(nullable=false, name="webZone", onDelete="CASCADE")

    private $webZone;*/

    /**
     * @var string
     *
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var Zone
     *
     * @ORM\ManyToOne(targetEntity="Maxcraft\DefaultBundle\Entity\Zone")
     * @ORM\JoinColumn(nullable=true)
     */
    private $parent;

    /**
     * @var string
     * @ORM\Column(name="points", type="string", length=255)
     */
    private $points;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Maxcraft\DefaultBundle\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     */
    private $owner;

    /**
     * @var World
     * @ORM\ManyToOne(targetEntity="Maxcraft\DefaultBundle\Entity\World")
     * @ORM\Column(nullable=false)
     */
    private $world;

    /**
     * @var string
     *
     * @ORM\Column(name="tags", type="string", length=300, nullable=true)
     */
    private $tags;

    /**
     * @var Builder
     *
     * @ORM\OneToMany(targetEntity="Maxcraft\DefaultBundle\Entity\Builder", mappedBy="zone", cascade={"persist"})
     */
    private $builders;

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

    public function __construct(){
        $this->builders = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     *
     * @return Zone
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
     * Set points
     *
     * @param string $points
     *
     * @return Zone
     */
    public function setPoints($points)
    {
        $this->points = $points;

        return $this;
    }

    /**
     * Get points
     *
     * @return string
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * Set tags
     *
     * @param string $tags
     *
     * @return Zone
     */
    public function setTags($tags)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Get tags
     *
     * @return string
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set parent
     *
     * @param \Maxcraft\DefaultBundle\Entity\Zone $parent
     *
     * @return Zone
     */
    public function setParent(Zone $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \Maxcraft\DefaultBundle\Entity\Zone
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add builder
     *
     * @param \Maxcraft\DefaultBundle\Entity\Builder $builder
     *
     * @return Zone
     */
    public function addBuilder(Builder $builder)
    {
        $this->builders[] = $builder;
        $builder->setZone($this);

        return $this;
    }

    /**
     * Remove builder
     *
     * @param \Maxcraft\DefaultBundle\Entity\Builder $builder
     * @return $this
     */
    public function removeBuilder(Builder $builder)
    {
        $this->builders->removeElement($builder);

    }

    /**
     * Get builders
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBuilders()
    {
        return $this->builders;
    }

    /**
     * Set world
     *
     * @param string $world
     *
     * @return Zone
     */
    public function setWorld($world)
    {
        $this->world = $world;

        return $this;
    }

    /**
     * Get world
     *
     * @return string
     */
    public function getWorld()
    {
        return $this->world;
    }

    /**
     * Set owner
     *
     * @param \Maxcraft\DefaultBundle\Entity\User $owner
     *
     * @return Zone
     */
    public function setOwner(User $owner = null)
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

    /*/**
     * @return WebZone

    public function getWebZone()
    {
        return $this->webZone;
    }

    /**
     * @param WebZone $webZone

    public function setWebZone($webZone)
    {
        $this->webZone = $webZone;
    }*/

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

    /**
     * @return boolean
     */
    public function isShopDemand()
    {
        return $this->shopDemand;
    }

    /**
     * @param boolean $shopDemand
     */
    public function setShopDemand($shopDemand)
    {
        $this->shopDemand = $shopDemand;
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
        if($this->tags == null OR $this->tags == '') return array();
        return explode(';',$this->tags);
    }

    public function getCoords()
    {
        //$coords = explode(';',$this->servZone->getPoints());
        list($x,$y,$z) = explode(';',$this->points);
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

    public function getIcon()
    {
        if($this->hasDirectlyTag('shop')) return 'images/shop.png';
        else return 'images/parcelle.png';

    }

}
