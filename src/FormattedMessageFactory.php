<?php
declare(strict_types=1);

namespace Genkgo\Mail;

use Genkgo\Mail\Header\ContentType;
use Genkgo\Mail\Mime\MultiPart;
use Genkgo\Mail\Mime\Boundary;
use Genkgo\Mail\Mime\EmbeddedImage;
use Genkgo\Mail\Mime\HtmlPart;
use Genkgo\Mail\Mime\PartInterface;
use Genkgo\Mail\Mime\PlainTextPart;

/**
 * Class FormattedMessageFactory
 * @package Genkgo\Email
 */
final class FormattedMessageFactory
{
    /**
     * @var array
     */
    private $attachments = [];
    /**
     * @var array
     */
    private $embedImages = [];
    /**
     * @var string
     */
    private $html;
    /**
     * @var string
     */
    private $text;

    /**
     * @param string $html
     * @return FormattedMessageFactory
     */
    public function withHtml(string $html): FormattedMessageFactory
    {
        $clone = clone $this;
        $clone->html = $html;
        $clone->text = $this->generateTextFromHtml($html);
        return $clone;
    }

    /**
     * @param string $html
     * @return FormattedMessageFactory
     */
    public function withHtmlAndNoGeneratedAlternativeText(string $html): FormattedMessageFactory
    {
        $clone = clone $this;
        $clone->html = $html;
        return $clone;
    }

    /**
     * @param string $text
     * @return FormattedMessageFactory
     */
    public function withAlternativeText(string $text): FormattedMessageFactory
    {
        $clone = clone $this;
        $clone->text = $text;
        return $clone;
    }

    /**
     * @param PartInterface $part
     * @return FormattedMessageFactory
     */
    public function withAttachment(PartInterface $part): FormattedMessageFactory
    {
        try {
            $disposition = (string) $part->getHeader('Content-Disposition')->getValue();
            if (substr($disposition, 0, strpos($disposition, ';')) !== 'attachment') {
                throw new \InvalidArgumentException(
                    'An attachment must have Content-Disposition header with value `attachment`'
                );
            }
        } catch (\UnexpectedValueException $e) {
            throw new \InvalidArgumentException(
                'An attachment must have an Content-Disposition header'
            );
        }

        $clone = clone $this;
        $clone->attachments[] = $part;
        return $clone;
    }

    /**
     * @param EmbeddedImage $embeddedImage
     * @return FormattedMessageFactory
     */
    public function withEmbeddedImage(EmbeddedImage $embeddedImage): FormattedMessageFactory
    {
        $clone = clone $this;
        $clone->embedImages[] = $embeddedImage;
        return $clone;
    }

    /**
     * @param string $html
     * @return string
     */
    private function generateTextFromHtml(string $html): string
    {
        return strip_tags($html);
    }

    /**
     * @return MessageInterface
     */
    public function createMessage(): MessageInterface
    {
        return (new MimeMessageFactory($this->createMessageRoot()))->createMessage();
    }

    /**
     * @return PartInterface
     */
    private function createMessageRoot(): PartInterface
    {
        if ($this->attachments) {
            return (new MultiPart(
                Boundary::newRandomBoundary(),
                new ContentType('multipart/mixed')
            ))
                ->withPart($this->createMessageHumanReadable())
                ->withParts($this->attachments)
            ;
        }

        return $this->createMessageHumanReadable();
    }

    /**
     * @return PartInterface
     */
    private function createMessageHumanReadable(): PartInterface
    {
        if ($this->embedImages) {
            return (new MultiPart(
                Boundary::newRandomBoundary(),
                new ContentType('multipart/related')
            ))
                ->withPart($this->createMessageText())
                ->withParts($this->embedImages);
        }

        return $this->createMessageText();
    }

    /**
     * @return PartInterface
     */
    private function createMessageText(): PartInterface
    {
        if ($this->text === null && $this->html === null) {
            return new PlainTextPart('');
        }

        if ($this->text === null) {
            return new HtmlPart($this->html);
        }

        return (new MultiPart(
            Boundary::newRandomBoundary(),
            new ContentType('multipart/alternative')
        ))
            ->withPart(new PlainTextPart($this->text))
            ->withPart(new HtmlPart($this->html));
    }
}