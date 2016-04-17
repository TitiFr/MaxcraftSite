<?php

namespace Maxcraft\DefaultBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WebsocketServerCommand extends ContainerAwareCommand{

    protected function configure()
    {
        $this
            ->setName('websocket:start')
            ->setDescription('Start the websockets servers');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $server = $this->getContainer()->get('maxcraft.websocket_server')->start();

    }

}