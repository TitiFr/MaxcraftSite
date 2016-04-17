<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 03/04/16
 * Time: 19:28
 */

namespace Maxcraft\DefaultBundle\Websocket;


class MarkerInfosHandler extends MaxcraftHandler
{

    protected function handle($data)
    {
        $marName = $data['name'];

        $marker = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:Marker')->findByName($marName);

        return array(
            'name' => $marker->getName(),
            'location' => $marker->getLocation(),
            'gps' => $marker->getGps()
        );
    }

    protected function onResponseSent()
    {
    }
}