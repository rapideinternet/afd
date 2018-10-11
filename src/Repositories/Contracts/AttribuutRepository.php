<?php

namespace SIVI\ADN\Repositories\Contracts;

use SIVI\ADN\Models\Entiteit;

interface AttribuutRepository
{
    public function getByLabel($label);

    public function getByEntiteit(Entiteit $entiteit);
}
