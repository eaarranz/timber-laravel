<?php

namespace Rebing\Timber\Requests\Events;

use Auth;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Monolog\Logger;
use Rebing\Timber\Requests\Contexts\HttpContext;
use Rebing\Timber\Requests\Contexts\SystemContext;
use Rebing\Timber\Requests\Contexts\UserContext;
use Rebing\Timber\Requests\LogLine;

abstract class AbstractEvent implements ShouldQueue
{
    use Queueable, InteractsWithQueue;

    protected $logLevel = Logger::INFO;

    public function handle()
    {
        return $this->send();
    }

    public function send(bool $queue = false)
    {
        $log = new LogLine(
            $this->getMessage(),
            $this->getContext(),
            $this->getEvent(),
            $this->getLogLevel()
        );
        if ($queue) {
            return dispatch($log);
        }

        return $log->json();
    }

    public function queue()
    {
        return $this->send(true);
    }

    public function getLogLevel(): string
    {
        if ($this->logLevel <= Logger::DEBUG) {
            return LogLine::LOG_LEVEL_DEBUG;
        } elseif ($this->logLevel <= Logger::NOTICE) {
            return LogLine::LOG_LEVEL_INFO;
        } elseif ($this->logLevel <= Logger::WARNING) {
            return LogLine::LOG_LEVEL_WARN;
        }

        return LogLine::LOG_LEVEL_ERROR;
    }

    abstract public function getMessage(): string;

    abstract public function getEvent(): array;

    public function getContext(): array
    {
        $httpContext = new HttpContext();
        $systemContext = new SystemContext();
        $userContext = new UserContext();

        $data = array_merge(
            $httpContext->getData(),
            $systemContext->getData()
        );

        $user = $userContext->getData();
        if (count($user)) {
            $data = array_merge($data, $user);
        }

        return $data;
    }
}