<?php

namespace Maxcraft\DefaultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;



/**
 * Jobs
 *
 * @ORM\Table()
 * @ORM\Entity()
 */
class Jobs
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
     * @ORM\OneToOne(targetEntity="Maxcraft\DefaultBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $user;

    /**
     * @var string
     * @ORM\Column(name="metier", type="string", length=255)
     */
    private $metier;

    /**
     * @var string
     * @ORM\Column(name="xp", type="decimal", precision=64, scale=11)
     */
    private $xp;

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
     * Set metier
     *
     * @param string $metier
     *
     * @return Jobs
     */
    public function setMetier($metier)
    {
        $this->metier = $metier;

        return $this;
    }

    /**
     * Get metier
     *
     * @return string
     */
    public function getMetier()
    {
        return $this->metier;
    }

    /**
     * Set xp
     *
     * @param string $xp
     *
     * @return Jobs
     */
    public function setXp($xp)
    {
        $this->xp = $xp;

        return $this;
    }

    /**
     * Get xp
     *
     * @return string
     */
    public function getXp()
    {
        return $this->xp;
    }

    /**
     * @param Jobs $job
     * @return string
     */
    public function objectToString(Jobs $job){

        $id = 'id="'.$job->getId().'",';
        $user = 'uuid="'.$job->getUser()->getUuid().'",';
        $metier = 'metier="'.$job->getMetier().'",';
        $xp = 'xp="'.$job->getXp().'",';

        $str = '-jobs:'.$id.$user.$metier.$xp;
        strval($str);
        if ($str[strlen($str)-1]==',') $str[strlen($str)-1]=null;
        return $str;
    }

    

    /**
     * Set user
     *
     * @param \Maxcraft\DefaultBundle\Entity\User $user
     *
     * @return Jobs
     */
    public function setUser(\Maxcraft\DefaultBundle\Entity\User $user)
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
}
