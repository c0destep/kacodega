<?php

declare(strict_types=1);

namespace Easycode\Database;

interface DriverImplements
{
    public function createConnection();
}
