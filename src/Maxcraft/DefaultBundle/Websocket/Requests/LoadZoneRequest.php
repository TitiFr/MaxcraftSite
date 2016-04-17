<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 07/04/16
 * Time: 20:21
 */

namespace Maxcraft\DefaultBundle\Websocket\Requests;


use Maxcraft\DefaultBundle\Entity\Zone;

class LoadZoneRequest extends MaxcraftRequest
{

    private $zone;

    public function __construct(Zone $zone){
        parent::__construct();
        $this->$zone = $zone;
    }

    public function getType()
    {
        return "LOADZONE";
    }

    public function buildData()
    {
        $zone = $this->zone;

        return array(
            'parentId' => $zone->getParent()->getId(),
            'owner' => $zone->getOwner()->getUuid(),
            'name' => $zone->getName(),
            'points' => $zone->getPoints(),
            'world' => $zone->getWorld(),
            'tags' => $zone->getTags(),
            'shopDemande' => $zone->isShopDemand(),
        );
    }

    public function onResponse($data)
    {
        if ($data['error'] != null && $data['error'] != ""){
            //TODO générer une erreur :$this->getServer()->getContainer()...
        }    }
}