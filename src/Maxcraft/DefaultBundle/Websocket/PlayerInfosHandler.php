<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 03/04/16
 * Time: 19:50
 */

namespace Maxcraft\DefaultBundle\Websocket;


class PlayerInfosHandler extends MaxcraftHandler
{

    protected function handle($data)
    {
        $playerUuid = $data['pUuid'];
        $player = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:Player')->findByUuid($playerUuid);

        return array(
            'uuid' =>$player->getUuid(),
            'name' => $player->getPseudo(),
            'balance' => strval($player->getBalance()),
            'actif' => strval($player->getActif()),
            'vanished' => strval($player->getVanished())
        );
    }

    protected function onResponseSent()
    {
    }
}