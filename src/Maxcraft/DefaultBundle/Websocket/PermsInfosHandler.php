<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 03/04/16
 * Time: 19:44
 */

namespace Maxcraft\DefaultBundle\Websocket;


class PermsInfosHandler extends MaxcraftHandler
{

    protected function handle($data)
    {
        $userUuid = $data['userUuid'];
        $user = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:User')->findByUuid($userUuid);
        $perm = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:Perms')->findByUser($user);

        return array(
            'userUuid' => $perm->getUser()->getUuid(),
            'groupName' => $perm->getGroupName(),
            'perms' => $perm->getPerms()
        );
    }

    protected function onResponseSent()
    {
    }
}