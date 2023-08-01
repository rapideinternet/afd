<?php

namespace SIVI\AFD\Enums;

use MyCLabs\Enum\Enum;

class EntityTypes extends Enum
{
    public const MESSAGE_DETAILS         = 'AL';
    public const BIJLAGE                 = 'BY';
    public const CONTRACT_POLISONDERDEEL = 'PK';
    public const CONTRACT_PAKKET         = 'PP';
}
