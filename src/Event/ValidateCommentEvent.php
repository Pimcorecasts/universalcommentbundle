<?php

namespace MercuryKojo\Bundle\UniversalCommentBundle\Event;

use Symfony\Component\EventDispatcher\GenericEvent;

class ValidateCommentEvent extends GenericEvent
{
    public const EVENT_NAME = 'ucb.validate_comment';

    public function isCommentValid(): bool {
        if (isset($this->arguments['content']) && isset($this->arguments['id']) &&
            $this->arguments['content'] != '' && $this->arguments['id'] != ''
            && (!isset($this->arguments['valid']) || $this->arguments['valid'] === true)
        ) {
            return true;
        }
        return false;
    }
}
