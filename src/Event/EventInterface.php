<?php

namespace MiniCore\Event;

interface EventInterface
{
    public function getName(): string;
    public function getData(): array;
}
