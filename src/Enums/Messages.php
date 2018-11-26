<?php

namespace SIVI\AFD\Enums;

use MyCLabs\Enum\Enum;

class Messages extends Enum
{
    public const CONTRACT = 'Contractbericht';
    public const PACKAGE = 'Pakket';
    public const RELATION = 'RelatieDocument';
    public const BATCH = 'Batch';
    public const GROUP = 'Groepsdocument';
    public const DAMAGE = 'Schadedocument';
    public const DAMGEINVOICE = 'Schadefactuur';
    public const PROLONGATION = 'Prolongatie';
    public const MUTATION = 'Mutatie';
}
