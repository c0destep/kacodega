<?php

declare(strict_types=1);

namespace Easycode\View;

class View
{
    /**
     * @const type view html
     */
    public const HTML = 'html';
    /**
     * @const type view json
     */
    public const JSON = 'json';

    /**
     * @var string
     */
    protected string $type;
    /**
     * @var string
     */
    protected string $statusText;
    /**
     * @var int
     */
    protected int $statusCode;
    /**
     * @var string
     */
    protected string $message;
    /**
     * @var array
     */
    protected array $parameters;
    /**
     * @var string
     */
    protected string $view;

    /**
     * @param string $type
     */
    public function __construct(string $type)
    {
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @param int $statusCode
     * @return static
     */
    public function setStatusCode(int $statusCode): static
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return static
     */
    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return static
     */
    public function setMessage(string $message): static
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatusText(): string
    {
        return $this->statusText;
    }

    /**
     * @param string $statusText
     * @return static
     */
    public function setStatusText(string $statusText): static
    {
        $this->statusText = $statusText;
        return $this;
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     * @return static
     */
    public function setParameters(array $parameters): static
    {
        $this->parameters = $parameters;
        return $this;
    }

    /**
     * @return string
     */
    public function getView(): string
    {
        return $this->view;
    }

    /**
     * @param string $view
     * @return static
     */
    public function setView(string $view): static
    {
        $this->view = $view;
        return $this;
    }
}