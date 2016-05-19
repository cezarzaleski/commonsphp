<?php

namespace Commons\Pattern\Log;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Psr\Log\InvalidArgumentException;
use Zend\Log\Logger;
use Zend\Log\LoggerInterface as ZendLoggerInterface;

/**
 * Adaptador para o Zend.
 * Nas versões mais novas do Zend >=2.6.* existe um adaptador \Zend\Log\PsrLoggerAdapter que pode ser utilizado
 * em substituição a essa versão.
 */
class DefaultZendPsrLoggerAdapter implements LoggerInterface
{
    /**
     * Zend\Log logger
     *
     * @var ZendLoggerInterface
     */
    protected $logger;

    /**
     * Map PSR-3 LogLevels to priority
     *
     * @var array
     */
    protected $psrPriorityMap = [
        LogLevel::EMERGENCY => Logger::EMERG,
        LogLevel::ALERT     => Logger::ALERT,
        LogLevel::CRITICAL  => Logger::CRIT,
        LogLevel::ERROR     => Logger::ERR,
        LogLevel::WARNING   => Logger::WARN,
        LogLevel::NOTICE    => Logger::NOTICE,
        LogLevel::INFO      => Logger::INFO,
        LogLevel::DEBUG     => Logger::DEBUG,
    ];

    /**
     * Constructor
     *
     * @param LoggerInterface $logger
     */
    public function __construct(ZendLoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Returns composed LoggerInterface instance.
     *
     * @return ZendLoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     * @return null
     * @throws InvalidArgumentException if log level is not recognized
     */
    public function log($level, $message, array $context = [])
    {
        if (! array_key_exists($level, $this->psrPriorityMap)) {
            throw new InvalidArgumentException(sprintf(
                '$level must be one of PSR-3 log levels; received %s',
                var_export($level, 1)
                ));
        }

        $priority = $this->psrPriorityMap[$level];
        $this->logger->log($priority, $message, $context);
    }

    /**
     * System is unusable.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function emergency($message, array $context = array())
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function alert($message, array $context = array())
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function critical($message, array $context = array())
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function error($message, array $context = array())
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function warning($message, array $context = array())
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function notice($message, array $context = array())
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function info($message, array $context = array())
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function debug($message, array $context = array())
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }
}
