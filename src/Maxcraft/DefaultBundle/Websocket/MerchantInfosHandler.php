<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 03/04/16
 * Time: 19:31
 */

namespace Maxcraft\DefaultBundle\Websocket;


class MerchantInfosHandler extends MaxcraftHandler
{

    protected function handle($data)
    {
        $merUuid = $data['uuid'];

        $merchant = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:Merchant')->findByUuid($merUuid);

        return array(
            'uuid' => $merchant->getUuid(),
            'items' => $merchant->getItems(),
            'prices' => $merchant->getPrices(),
            'inv' => $merchant->getInv()
        );
    }

    protected function onResponseSent()
    {
    }
}