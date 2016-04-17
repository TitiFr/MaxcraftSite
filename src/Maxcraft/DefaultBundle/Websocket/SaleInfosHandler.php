<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 08/04/16
 * Time: 19:56
 */

namespace Maxcraft\DefaultBundle\Websocket;


class SaleInfosHandler extends MaxcraftHandler
{

    protected function handle($data)
    {
        $saleUuid = $data["uuid"];

        $sale = $this->getDoctrine()->getRepository('MaxcraftDefaultBundle:Sale')->findByUuid($saleUuid);

        return array(
            "uuid" => $sale->getUuid(),
            "price" => $sale->getPrice(),
            "type" => $sale->getType(),
            "sign" => $sale->getSign(),
        );
    }

    protected function onResponseSent()
    {
    }
}