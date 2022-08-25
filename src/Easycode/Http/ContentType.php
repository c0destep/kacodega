<?php

declare(strict_types=1);

namespace Easycode\Http;

enum ContentType: string
{
    case CONTENT_HTML = 'text/html charset=UTF-8';
    case CONTENT_CSS = 'text/css charset=UTF-8';
    case CONTENT_JS = 'application/javascript charset=UTF-8';
    case CONTENT_JSON = 'application/json charset=UTF-8';
    case CONTENT_XML = 'application/xml charset=UTF-8';
    case CONTENT_OCTET_STREAM = 'application/octet-stream charset=UTF-8';
}
