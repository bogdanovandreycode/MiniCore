<?php

namespace Vendor\Undermarket\Core\UI;

enum AlertType: string
{
    case SUCCESS = 'success';
    case ERROR = 'error';
    case WARNING = 'warning';
    case INFO = 'info';
}
