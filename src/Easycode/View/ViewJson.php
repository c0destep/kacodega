<?php

declare(strict_types=1);

namespace Easycode\View;

class ViewJson extends View
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct(View::JSON);
    }

    /**
     * @return string
     */
    public function toJson(): string
    {
        return json_encode([
            'status' => [
                'type' => $this->getStatusText(),
                'code' => $this->getStatusCode()
            ],
            'message' => $this->getMessage(),
            'response' => $this->getParameters()
        ]);
    }
}