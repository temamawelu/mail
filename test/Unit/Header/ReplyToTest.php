<?php

namespace Genkgo\Mail\Unit\Header;

use Genkgo\Mail\AbstractTestCase;
use Genkgo\Mail\Address;
use Genkgo\Mail\AddressList;
use Genkgo\Mail\EmailAddress;
use Genkgo\Mail\Header\ReplyTo;

final class ReplyToTest extends AbstractTestCase
{

    /**
     * @test
     */
    public function it_produces_correct_values()
    {
        $header = new ReplyTo(
            new AddressList([
                new Address(
                    new EmailAddress('me@example.com'),
                    'Name'
                )
            ])
        );

        $this->assertEquals('ReplyTo', (string)$header->getName());
        $this->assertEquals('Name <me@example.com>', (string)$header->getValue());
    }
}