<?php

declare(strict_types=1);

namespace SolunoBC;

interface MessageInterface
{
    /**
     * @return string
     */
    public function getMessage(): string;

    /**
     * @return string
     */
    public function getFromNumber(): string;

    /**
     * @return string[]
     */
    public function getToNumbers(): array;
}
