<?php

namespace Genkgo\Mail\Unit\Transport;

use Genkgo\Mail\AbstractTestCase;
use Genkgo\Mail\Exception\ConnectionException;
use Genkgo\Mail\GenericMessage;
use Genkgo\Mail\Transport\RetryIfFailedTransport;
use Genkgo\Mail\TransportInterface;

final class RetryIfFailedTransportTest extends AbstractTestCase
{
    /**
     * @test
     */
    public function it_does_not_retry_when_successful()
    {
        $decoratedTransport = $this->createMock(TransportInterface::class);

        $decoratedTransport
            ->expects($this->once())
            ->method('send');

        $sender = new RetryIfFailedTransport($decoratedTransport, 3);
        $sender->send(new GenericMessage());
    }

    /**
     * @test
     */
    public function it_retries_once_when_only_first_time_fails()
    {
        $decoratedTransport = $this->createMock(TransportInterface::class);

        $decoratedTransport
            ->expects($this->at(0))
            ->method('send')
            ->willThrowException(new ConnectionException());

        $decoratedTransport
            ->expects($this->at(1))
            ->method('send');

        $sender = new RetryIfFailedTransport($decoratedTransport, 3);
        $sender->send(new GenericMessage());
    }

    /**
     * @test
     */
    public function it_retries_once_when_first_two_times_fail()
    {
        $decoratedTransport = $this->createMock(TransportInterface::class);

        $decoratedTransport
            ->expects($this->at(0))
            ->method('send')
            ->willThrowException(new ConnectionException());

        $decoratedTransport
            ->expects($this->at(1))
            ->method('send')
            ->willThrowException(new ConnectionException());

        $decoratedTransport
            ->expects($this->at(2))
            ->method('send');

        $sender = new RetryIfFailedTransport($decoratedTransport, 3);
        $sender->send(new GenericMessage());
    }

    /**
     * @test
     */
    public function it_throws_the_last_exception_when_last_retry_fails()
    {
        $this->expectException(ConnectionException::class);

        $decoratedTransport = $this->createMock(TransportInterface::class);

        $decoratedTransport
            ->expects($this->at(0))
            ->method('send')
            ->willThrowException(new ConnectionException());

        $decoratedTransport
            ->expects($this->at(1))
            ->method('send')
            ->willThrowException(new ConnectionException());

        $decoratedTransport
            ->expects($this->at(2))
            ->method('send')
            ->willThrowException(new ConnectionException());

        $sender = new RetryIfFailedTransport($decoratedTransport, 3);
        $sender->send(new GenericMessage());
    }

}