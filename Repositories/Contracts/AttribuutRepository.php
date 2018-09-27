<?php
/**
 * Created by PhpStorm.
 * User: mebus
 * Date: 27-9-18
 * Time: 14:32
 */

namespace SKPClient\Repositories\Contracts;


use SKPClient\Models\Entiteit;

interface AttribuutRepository
{
    public function getByLabel($label);

    public function getByEntiteit(Entiteit $entiteit);
}