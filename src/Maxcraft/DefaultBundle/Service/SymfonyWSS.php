<?php


namespace Maxcraft\DefaultBundle\Service;


use Doctrine\Bundle\DoctrineBundle\Registry;
use NathemWS\NathemWSS;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

class SymfonyWSS extends NathemWSS
{
    private  $container;

    /**
     * @param $container
     * @param $key
     */
    public function __construct($container, $key)
    {
        parent::__construct("MaxcraftPhpServer", $key);
        $this->container = $container;

        //Handlers :
        $this->registerHandler("JOB-INFOS", 'Maxcraft\\DefaultBundle\\Websocket\\JobInfosHandler');
        $this->registerHandler("FACTION-INFOS", 'Maxcraft\\DefaultBundle\\WeSocket\\FactionInfosHandler');
        $this->registerHandler("MODERATION-INFOS", 'Maxcraft\\DefaultBundle\\WeSocket\\ModerationInfosHandler');
        $this->registerHandler("MARKER-INFOS", 'Maxcraft\\DefaultBundle\\WeSocket\\MarkerInfosHandler');
        $this->registerHandler("MERCHANT-INFOS", 'Maxcraft\\DefaultBundle\\WeSocket\\MerchantInfosHandler');
        $this->registerHandler("PERMS-INFOS", 'Maxcraft\\DefaultBundle\\WeSocket\\PermsInfosHandler');
        $this->registerHandler("PLAYER-INFOS", 'Maxcraft\\DefaultBundle\\WeSocket\\PlayerInfosHandler');
        $this->registerHandler("QUESTER-INFOS", 'Maxcraft\\DefaultBundle\\WeSocket\\QuesterInfosHandler');
        $this->registerHandler("SALE-INFOS", 'Maxcraft\\DefaultBundle\\WeSocket\\SaleInfosHandler');
    }

    /**
     * @return mixed
     */
    public function getContainer()
    {
        return $this->container;
    }



    public function start()
    {
        $runningServer = IoServer::factory(new HttpServer(new WsServer($this)), 7689);
        $runningServer->run();

    }


}