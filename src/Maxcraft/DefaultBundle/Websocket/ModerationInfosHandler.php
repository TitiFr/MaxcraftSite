<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 03/04/16
 * Time: 19:13
 */

namespace Maxcraft\DefaultBundle\Websocket;


class ModerationInfosHandler extends MaxcraftHandler
{

    protected function handle($data)
    {
        $userUuid = $data['userUuid'];
        $user = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:User')->findByUuid($userUuid);
        $md = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:Moderation')->findByUser($user);

        return array(
            'userUuid' => $md->getUser()->getUuid(),
            'ismute' => $md->getIsmute(),
            'muteend' => $md->getMuteent(),
            'isjail' => $md->getIsjail(),
            'jailend' => $md->getJailend(),
            'isban' => $md->getIsban(),
            'banend' =>$md->getBanend(),
            'banreason' => $md->getBanreason()
        );
    }

    protected function onResponseSent()
    {
    }
}