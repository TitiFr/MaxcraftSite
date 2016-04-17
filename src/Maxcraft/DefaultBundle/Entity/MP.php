<?php

namespace Maxcraft\DefaultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * MP
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class MP
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
     *
     * @ORM\ManyToOne(targetEntity="Maxcraft\DefaultBundle\Entity\User")
     * @ORM\JoinColumn(name="sender", nullable=false)
     */
    private $sender;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Maxcraft\DefaultBundle\Entity\User")
     * @ORM\JoinColumn(name="target", nullable=false)
     */
    private $target;

    /**
     * TYPES :
     * NORMAL
     * FACTION_REQUEST
     * REQUESTALLIE
     */

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var string
     *
     * @Assert\NotBlank(message = "Vous devez entrer un sujet !")
     * @Assert\Length(min = "3", minMessage = "Le sujet doit contenir plus de 3 caractères.", max="100", maxMessage= "Le sujet doit contenir moins de 100 caractères.")
     * @ORM\Column(name="topic", type="string", length=300)
     */
    private $topic;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var string
     *
     * @Assert\NotBlank(message = "Vous devez entrer un message !")
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var boolean
     *
     * @ORM\Column(name="view", type="boolean")
     */
    private $view;

    private $pseudo;


    public function __construct(){
        $this->date = new \DateTime();
        $this->view = false;
        $this->type = 'NORMAL';
    }

    public function getPseudo()
    {
        return $this->pseudo;
    }

    public function setPseudo($pseudo)
    {
        $this->pseudo = $pseudo;

        return $this;
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
     * Set sender
     *
     * @param string $sender
     *
     * @return MP
     */
    public function setSender($sender)
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * Get sender
     *
     * @return string
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Set target
     *
     * @param string $target
     *
     * @return MP
     */
    public function setTarget($target)
    {
        $this->target = $target;

        return $this;
    }

    /**
     * Get target
     *
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return MP
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set topic
     *
     * @param string $topic
     *
     * @return MP
     */
    public function setTopic($topic)
    {
        $this->topic = $topic;

        return $this;
    }

    /**
     * Get topic
     *
     * @return string
     */
    public function getTopic()
    {
        return $this->topic;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return MP
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return MP
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
     * Set view
     *
     * @param boolean $view
     *
     * @return MP
     */
    public function setView($view)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * Get view
     *
     * @return boolean
     */
    public function getView()
    {
        return $this->view;
    }
}

