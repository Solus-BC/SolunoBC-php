<?php

declare(strict_types=1);

namespace SolunoBC;

class Message implements MessageInterface
{
    /**
     * @var string
     */
    private $message;

    /**
     * @var string
     */
    private $fromNumber;

    /**
     * @var string[]
     */
    private $toNumbers;

    /**
     * @param string $message
     * @param string $fromNumber
     * @param string[] $toNumbers
     */
    public function __construct(string $message, string $fromNumber, array $toNumbers)
    {
        $this->message = $message;
        $this->fromNumber = $fromNumber;
        $this->toNumbers = $toNumbers;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * {@inheritdoc}
     */
    public function getFromNumber(): string
    {
        return $this->fromNumber;
    }

    /**
     * {@inheritdoc}
     */
    public function getToNumbers(): array
    {
        return $this->toNumbers;
    }
}
