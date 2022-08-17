<?php

declare(strict_types=1);

namespace Easycode\Http;

enum ContentType: string
{
    case CONTENT_HTML = 'text/html';
    case CONTENT_CSS = 'text/css';
    case CONTENT_JS = 'application/javascript';
    case CONTENT_JSON = 'application/json';
    case CONTENT_XML = 'application/xml';
    case CONTENT_OCTET_STREAM = 'application/octet-stream';
}
