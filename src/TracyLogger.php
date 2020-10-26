<?php

declare(strict_types = 1);

namespace Graphpinator\Nette;

final class TracyLogger extends \Psr\Log\AbstractLogger
{
    public function log($level, $message, array $context = array()) : void
    {
        \Tracy\Debugger::log($message, $level);
    }
}
