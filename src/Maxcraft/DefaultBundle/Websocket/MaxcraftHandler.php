<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 03/12/15
 * Time: 20:50
 */

namespace Maxcraft\DefaultBundle\Websocket;


use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Collections\ArrayCollection;
use NathemWS\NathemWSHandler;


abstract class MaxcraftHandler extends NathemWSHandler
{


    /**
     * @return Registry
     */
    public function getDoctrine() {


        return $this->client->getServer()->getContainer()->get('doctrine');
    }

    protected function arrayToString(Array $array){

        $result = "";

        foreach($array as $a){
            $result = $a.';';
        }

        strval($result);
        if ($result[strlen($result)-1]==';') $result[strlen($result)-1]=null;

        return $result;
    }

}