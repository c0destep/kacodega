<?php

declare(strict_types=1);

namespace Easycode\Http;

enum Method: string
{
    case ALL = 'ALL';
    case GET = 'GET';
    case POST = 'POST';
    case PUT = 'PUT';
    case DELETE = 'DELETE';
    case PATCH = 'PATCH';
    case OPTIONS = 'OPTIONS';
    case HEAD = 'HEAD';
}
