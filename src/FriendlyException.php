<?php
declare(strict_types=1);

namespace Misico;

use Throwable;

class FriendlyException extends \Exception
{
    private string $safeMessage;

    public function __construct(string $safeMessage, $message = '', $code = 0, Throwable $previous = null)
    {
        $this->safeMessage = $safeMessage;
        if (empty($message)) {
            $message = $safeMessage;
        }
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string
     */
    public function getSafeMessage(): string
    {
        return $this->safeMessage;
    }
}
