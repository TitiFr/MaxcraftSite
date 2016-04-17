<?php

namespace Maxcraft\DefaultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PageSection
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class PageSection
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
     * @var Page
     *
     * @ORM\ManyToOne(targetEntity="Maxcraft\DefaultBundle\Entity\Page")
     * @ORM\JoinColumn(name="page", nullable=false)
     */
    private $page;

    /**
     * @var integer
     *
     * @ORM\Column(name="ordervalue", type="integer")
     */
    private $ordervalue;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var boolean
     *
     * @ORM\Column(name="display", type="boolean")
     */
    private $display;

    public function __construct(){
        $this->display = false;
        $this->content = 'Section vide !';
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
     * Set page
     *
     * @param Page $page
     *
     * @return PageSection
     */
    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Get page
     *
     * @return Page
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Set ordervalue
     *
     * @param integer $ordervalue
     *
     * @return PageSection
     */
    public function setOrdervalue($ordervalue)
    {
        $this->ordervalue = $ordervalue;

        return $this;
    }

    /**
     * Get ordervalue
     *
     * @return integer
     */
    public function getOrdervalue()
    {
        return $this->ordervalue;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return PageSection
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return PageSection
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set display
     *
     * @param boolean $display
     *
     * @return PageSection
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
}

