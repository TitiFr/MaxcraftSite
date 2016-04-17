<?php

namespace Maxcraft\DefaultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Travel
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Travel
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
     * @ORM\Column(name="marker1", type="string", length=255)
     */
    private $marker1;

    /**
     * @var string
     *
     * @ORM\Column(name="tempmarker", type="string", length=255)
     */
    private $tempmarker;

    /**
     * @var string
     *
     * @ORM\Column(name="marker2", type="string", length=255)
     */
    private $marker2;


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
     * Set marker1
     *
     * @param string $marker1
     *
     * @return Travel
     */
    public function setMarker1($marker1)
    {
        $this->marker1 = $marker1;

        return $this;
    }

    /**
     * Get marker1
     *
     * @return string
     */
    public function getMarker1()
    {
        return $this->marker1;
    }

    /**
     * Set tempmarker
     *
     * @param string $tempmarker
     *
     * @return Travel
     */
    public function setTempmarker($tempmarker)
    {
        $this->tempmarker = $tempmarker;

        return $this;
    }

    /**
     * Get tempmarker
     *
     * @return string
     */
    public function getTempmarker()
    {
        return $this->tempmarker;
    }

    /**
     * Set marker2
     *
     * @param string $marker2
     *
     * @return Travel
     */
    public function setMarker2($marker2)
    {
        $this->marker2 = $marker2;

        return $this;
    }

    /**
     * Get marker2
     *
     * @return string
     */
    public function getMarker2()
    {
        return $this->marker2;
    }
}

