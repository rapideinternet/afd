<?php

namespace SIVI\AFD\Services;

use SIVI\AFD\Services\Contracts\FilenameService as FilenameServiceContract;

class FilenameService implements FilenameServiceContract
{
    public function getExtensionFormFilename($filename): string
    {
        return substr(strrchr($filename, '.'), 1);
    }
}
