<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 03/04/16
 * Time: 13:41
 */

namespace Maxcraft\DefaultBundle\Websocket;


class JobInfosHandler extends MaxcraftHandler
{


    protected function handle($data)
    {
        $userUuid = $data['userUuid'];
        $user = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:User')->findByUuid($userUuid);
        $job = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:Jobs')->findByUser($user);

        return array(
          'uuid' => $job->getUser()->getUuid(),
          'metier' => $job->getMetier(),
          'xp'=>$job->getXp()
        );
    }

    protected function onResponseSent()
    {

    }
}