<?php

namespace Maxcraft\DefaultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Maxcraft\DefaultBundle\Entity\Album;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Image
 *
 * @ORM\Table(name="image")
 * @ORM\Entity()
 */
class Image
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Album
     *
     * @ORM\ManyToOne(targetEntity="Maxcraft\DefaultBundle\Entity\Album", inversedBy="images")
     * @ORM\JoinColumn(name="album", nullable=false, onDelete="CASCADE")
     */
    private $album;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255)
     */
    private $path; //chemin vers dossier web

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="alt", type="string", length=255)
     */
    private $alt;

    /**
     * @var UploadedFile
     * @Assert\File(maxSize="6000000")
     */
    private $file;

    /**
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param mixed $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set path
     *
     * @param string $path
     *
     * @return Image
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Image
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

    public function getAbsolutePath() //chemin absolu de l'img
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir().'/'.$this->path;
    }

    public function getWebPath() //chemin de asset() (depuis web)
    {
        return null === $this->path
            ? null
            : $this->getUploadDir().'/'.$this->path;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir() //dossier où est stocké img (dans web)
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'images/uploaded';
    }

    public function upload(){
        if (null === $this->file) {
            return;
        }
        $name1 = $this->getFile()->getClientOriginalName();
        $name = implode('', explode(' ', $name1));
        $name = implode('', explode('.', $name));
        $name2 = implode('', array(implode('_', array(uniqid('img', true), $name)), '.png'));

        // On récupère le nom original du fichier de l'internaute
        $this->setName($name2);

        // On déplace le fichier envoyé dans le répertoire de notre choix
        $this->file->move($this->getUploadRootDir(), $name2);

        // On sauvegarde le nom de fichier dans notre attribut $path
        $this->path = $this->getName();

        // On crée également le futur attribut alt de notre balise <img>
        $this->alt = $this->name;
    }

    /**
     * Set alt
     *
     * @param string $alt
     *
     * @return Image
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;

        return $this;
    }

    /**
     * Get alt
     *
     * @return string
     */
    public function getAlt()
    {
        return $this->alt;
    }

    public function remove(){
        unlink($this->getAbsolutePath());
    }

    /**
     * Set album
     *
     * @param \Maxcraft\DefaultBundle\Entity\Album $album
     *
     * @return Image
     */
    public function setAlbum(Album $album)
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
}
