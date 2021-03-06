<?php

namespace Rebing\Timber\Requests\Events;

abstract class HttpEvent extends AbstractEvent
{
    protected $outgoing;
    protected $serviceName;
    protected $requestStartTime;

    const DIRECTION_OUT = 'outgoing';
    const DIRECTION_IN = 'incoming';

    public function setRequestStartTime(): float
    {
        $this->requestStartTime = microtime(true);
        return $this->requestStartTime;
    }

    public function getRequestStartTime()
    {
        return $this->requestStartTime;
    }

    /**
     * Get the time from the start of the request
     */
    public function getElapsedTimeInMs(): ?float
    {
        $startTime = $this->getRequestStartTime();
        if (is_null($startTime)) {
            return null;
        }

        $currentTime = microtime(true);
        return ($currentTime - $startTime) * 1000;
    }
}