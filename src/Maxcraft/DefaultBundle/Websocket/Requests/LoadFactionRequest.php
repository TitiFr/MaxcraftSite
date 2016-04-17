<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 06/04/16
 * Time: 18:53
 */

namespace Maxcraft\DefaultBundle\Websocket\Requests;


use Maxcraft\DefaultBundle\Entity\Faction;

class LoadFactionRequest extends MaxcraftRequest
{

    private $faction;

    public function __construct(Faction $faction){
        parent::__construct();
        $this->$faction = $faction;
    }

    public function getType()
    {
       return "LOADFACTION";
    }

    public function buildData()
    {
        $f = $this->faction;

        $facAlliees = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:FactionRole')->findAllies($f);
        $facEnemies = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:FactionRole')->findEnemies($f);
        $facOwner = $f->getOwner();
        $recruits = array();
        $members = array();
        $heads = array();

        foreach ($f->getMembers() as $member){
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
                    if ($member == $f->getOwner()){
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
            'uuid' => $f->getUuid(),
            'name' => $f->getName(),
            'tag'  => $f->getTag(),
            'balance' => $f->getBalance(),
            'spawn' => $f->getSpawn(),
            'jail' => $f->getJail(),
            'owner' => $facOwner->getUuid(),
            'heads' => $this->arrayToString($heads),
            'members' => $this->arrayToString($members),
            'recruits' => $this->arrayToString($recruits),
            'allies' => $this->arrayToString($allies),
            'enemies' => $this->arrayToString($enemies),
            'icon' => $f->getIcon(),
            'banner' => $f->getBanner()
        );
    }

    public function onResponse($data)
    {
        if ($data['error'] != null && $data['error'] != ""){
            //TODO générer une erreur :$this->getServer()->getContainer()...
        }
    }
}