<?php

namespace SIVI\AFD\Models\Interfaces;

use SIVI\AFD\Models\CodeList\CodeList;
use SIVI\AFD\Models\Codes\Code;
use SIVI\AFD\Models\Domain\Domain;
use SIVI\AFD\Models\Formats\Format;

interface Attribute
{
    public function getLabel(): string;

    public function setLabel(string $label): self;

    public function getDisplayValue();

    public function getValue();

    public function setValue($value): self;

    public function getDomain(): Domain;

    public function setDomain(Domain $domain): self;

    public function getFormat(): Format;

    public function setFormat(Format $format): self;

    public function getCode(): Code;

    public function setCode(Code $code): self;

    public function getCodeList(): CodeList;

    public function setCodeList(CodeList $codeList): self;

    public function getDescription(): string;

    public function setDescription(string $description): self;

    public function getExplanation(): string;

    public function setExplanation(string $explanation): \SIVI\AFD\Models\Attribute;

    public function getTypeLabel(): string;

    /**
     * @return mixed|null
     */
    public function getCodeListDescription();
}
