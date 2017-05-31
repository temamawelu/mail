<?php
declare(strict_types=1);

namespace Genkgo\Mail\Protocol\Smtp\Request;

use Genkgo\Mail\Protocol\ConnectionInterface;
use Genkgo\Mail\Protocol\Smtp\RequestInterface;

final class QuitCommand implements RequestInterface
{
    /**
     * @param ConnectionInterface $connection
     * @return void
     */
    public function execute(ConnectionInterface $connection)
    {
        $connection->send(sprintf("QUIT", RequestInterface::CRLF));
    }
}