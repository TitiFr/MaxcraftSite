<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 08/04/16
 * Time: 19:48
 */

namespace Maxcraft\DefaultBundle\Websocket;


class QuesterInfosHandler extends MaxcraftHandler
{

    protected function handle($data)
    {
        $questerUuid = $data["uuid"];

        $quester = $this->getDoctrine()->getRepository("MaxcraftDefaultBundle:Quester")->findByUuid($questerUuid);

        return array(
            "uuid" => $quester->getUuid(),
            "quest" => $quester->getQuest(),
            "player" => $quester->getPlayer(),
            "stats" => $quester->getStats(),
        );
    }

    protected function onResponseSent()
    {
    }
}