<?php
declare(strict_types=1);

namespace Genkgo\Mail\Protocol\Smtp\Request;

use Genkgo\Mail\Protocol\ConnectionInterface;
use Genkgo\Mail\Protocol\Smtp\RequestInterface;

/**
 * Class AuthPlainCommand
 * @package Genkgo\Mail\Protocol\Smtp\Request
 */
final class AuthPlainCommand implements RequestInterface
{
    /**
     * @param ConnectionInterface $connection
     * @return void
     */
    public function execute(ConnectionInterface $connection)
    {
        $connection->send(sprintf("AUTH PLAIN", RequestInterface::CRLF));
    }
}