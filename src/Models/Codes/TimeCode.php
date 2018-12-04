<?php

namespace SIVI\AFD\Models\Codes;

use SIVI\AFD\Enums\Codes;

class TimeCode extends DateCode
{
    protected static $code = Codes::TIME;

    protected $description = 'Tijd formaat UUMM';

    protected $format = 'Hi';

    protected $displayFormat = 'H:i';

}
