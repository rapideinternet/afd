<?php

namespace SIVI\AFD\Services\Contracts;

interface FilenameService
{
    public function getExtensionFormFilename($filename): string;
}
