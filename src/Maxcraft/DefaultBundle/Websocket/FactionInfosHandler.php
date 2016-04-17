<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 03/04/16
 * Time: 16:32
 */

namespace Maxcraft\DefaultBundle\Websocket;


class FactionInfosHandler extends MaxcraftHandler
{

    protected function handle($data)
    {
        $facUuid = $data['facUuid'];
        $faction = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:Faction')->findByUuid($facUuid);
        $facAlliees = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:FactionRole')->findAllies($faction);
        $facEnemies = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:FactionRole')->findEnemies($faction);
        $facOwner = $faction->getOwner();
        $recruits = array();
        $members = array();
        $heads = array();

        foreach ($faction->getMembers() as $member){
                switch ($member->getFactionRole()){
                    case 1 :
                        $recruits[] = $member->getUuid();
                        break;

                    case 2 :
                        $members[] = $member->getUuid();
                        break;

                    case 9 :
                        $heads[] = $member->getUuid();
                        break;

                    case 10:
                        if ($member == $faction->getOwner()){
                            continue;
                        }
                }
        }

        $enemies = array();
        $allies = array();

        foreach ($facAlliees as $f){
            $allies[] = $f->getUuid();
        }

        foreach ($facEnemies as $f){
            $enemies[] = $f->getUuid();
        }

        return array(
            'uuid' => $faction->getUuid(),
            'name' => $faction->getName(),
            'tag'  => $faction->getTag(),
            'balance' => $faction->getBalance(),
            'spawn' => $faction->getSpawn(),
            'jail' => $faction->getJail(),
            'owner' => $facOwner->getUuid(),
            'heads' => $this->arrayToString($heads),
            'members' => $this->arrayToString($members),
            'recruits' => $this->arrayToString($recruits),
            'allies' => $this->arrayToString($allies),
            'enemies' => $this->arrayToString($enemies),
            'icon' => $faction->getIcon(),
            'banner' => $faction->getBanner()
        );
    }

    protected function onResponseSent()
    {
    }


}