<?php
namespace Commons\Exception;

/**
 * Exceção para os serviços.
 */
class ServiceException extends \RuntimeException implements CommonsException
{
    /**
     * Mensagens
     *
     * @var array
     */
    protected $messages;

    /**
     * Construtor padrão.
     *
     * @param string | array $message
     * @param int $code
     * @param \Exception $previous
     */
    public function __construct($message, $code = null, $previous = null)
    {
        if (\is_array($message)) {
            $this->setMessages($message);
            return parent::__construct('Service Exception has returned multiple error messages.', $code, $previous);
        }
        return parent::__construct($message, $code, $previous);
    }

    /**
     * Guarda as mensagens da exceção.
     *
     * @param array $messages
     */
    public function setMessages(array $messages)
    {
        $this->messages = $messages;
    }

    /**
     * Get colections messages
     * @return array
     */
    public function getMessages()
    {
        if ($this->messages) {
            return $this->messages;
        }

        return array($this->getMessage());
    }
}
