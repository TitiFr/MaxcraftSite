<?php

namespace Maxcraft\DefaultBundle\Entity;
use Doctrine\ORM\EntityRepository;

/**
 * PlayerRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PlayerRepository extends EntityRepository
{
    public function haveVisited($playerName){
        if ($this->getEntityManager()->getRepository('MaxcraftDefaultBundle:Player')->findOneByPseudo($playerName)){
            return true;
        }
        else{
            return false;
        }
    }
}
