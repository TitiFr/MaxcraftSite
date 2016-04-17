<?php

namespace Maxcraft\DefaultBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Maxcraft\DefaultBundle\Entity\Image;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Type;

/**
 * Album
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Maxcraft\DefaultBundle\Entity\AlbumRepository")
 */
class Album
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
     * @ORM\ManyToOne(targetEntity="Maxcraft\DefaultBundle\Entity\User")
     * @ORM\JoinColumn(name="user", nullable=false)
     */
    private $user;

    /**
     * @var Image
     *
     * @ORM\OneToOne(targetEntity="Maxcraft\DefaultBundle\Entity\Image")
     * @ORM\JoinColumn(name="albumimage", nullable=true)
     */
    private $albumimage;

    /**
     * @var string
     * @Assert\NotBlank(message = "Vous devez entrer le nom de l'album !")
     * @Assert\Length(min= 1, minMessage = "Le nom de l'album doit contenir plus de 1 caractère.", max = 20, maxMessage= "Le nom de l'album doit contenir moins de 20 caractères.")
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     * @Assert\NotBlank(message = "Veuillez entrer une description de votre album.")
     * @Assert\Length(min="3", max="254", minMessage="La description de l'album doit contenir au moins 3 caractères !", maxMessage="La description de l'album doit contenir moins de 254 caractères !")
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creationDate", type="datetime")
     */
    private $creationDate;

    /**
     * @var boolean
     * @ORM\Column(name="display", type="boolean")
     */
    private $display;

    /**
     * @var array
     *
     * @ORM\OneToMany(targetEntity="Maxcraft\DefaultBundle\Entity\Image",mappedBy="album",cascade={"remove", "persist"})
     * @ORM\JoinColumn(nullable=true, name="images")
     */
    private $images;

    public function __construct(){
        $this->creationDate = new \DateTime();
        $this->display = false;
        $this->albumimage = null;
        $this->images = null;
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
     * @return Album
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
     * Set description
     *
     * @param string $description
     *
     * @return Album
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
     * Set creationDate
     *
     * @param \DateTime $creationDate
     *
     * @return Album
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    /**
     * Get creationDate
     *
     * @return \DateTime
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * Set display
     *
     * @param boolean $display
     *
     * @return Album
     */
    public function setDisplay($display)
    {
        $this->display = $display;

        return $this;
    }

    /**
     * Get display
     *
     * @return boolean
     */
    public function getDisplay()
    {
        return $this->display;
    }

    /**
     * Set user
     *
     * @param \Maxcraft\DefaultBundle\Entity\User $user
     *
     * @return Album
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
     * Set albumimage
     *
     * @param Image $albumimage
     *
     * @return Album
     */
    public function setAlbumimage(Image $albumimage = null)
    {
        $this->albumimage = $albumimage;

        return $this;
    }

    /**
     * Get albumimage
     *
     * @return Image
     */
    public function getAlbumimage()
    {
        return $this->albumimage;
    }

    /**
     * Add image
     *
     * @param Image $image
     *
     * @return Album
     */
    public function addImage(Image $image)
    {
        $this->images[] = $image;


        return $this;
    }

    /**
     * Remove image
     *
     * @param Image $image
     */
    public function removeImage(Image $image)
    {
        $this->images->removeElement($image);
    }

    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImages()
    {
        return $this->images;
    }

    public function getAlbum(){
        return $this;
    }
}
