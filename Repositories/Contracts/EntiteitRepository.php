<?php
/**
 * Created by PhpStorm.
 * User: mebus
 * Date: 27-9-18
 * Time: 14:32
 */

namespace SKPClient\Repositories\Contracts;


interface EntiteitRepository
{
    public function getByLabel($label);
}