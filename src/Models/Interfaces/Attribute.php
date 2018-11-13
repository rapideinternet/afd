<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 5-11-18
 * Time: 13:48
 */

namespace SIVI\AFD\Models\Interfaces;

use SIVI\AFD\Models\Codes\Code;
use SIVI\AFD\Models\CodesList\CodeList;
use SIVI\AFD\Models\Domain\Domain;
use SIVI\AFD\Models\Formats\Format;

interface Attribute
{

    /**
     * @return string
     */
    public function getLabel(): string;

    /**
     * @param string $label
     * @return \SIVI\AFD\Models\Attribute
     */
    public function setLabel(string $label): \SIVI\AFD\Models\Attribute;

    /**
     * @return string
     */
    public function getValue();

    /**
     * @param mixed $value
     * @return \SIVI\AFD\Models\Attribute
     */
    public function setValue($value): \SIVI\AFD\Models\Attribute;

    /**
     * @return Domain
     */
    public function getDomain(): Domain;

    /**
     * @param Domain $domain
     * @return \SIVI\AFD\Models\Attribute
     */
    public function setDomain(Domain $domain): \SIVI\AFD\Models\Attribute;

    /**
     * @return Format
     */
    public function getFormat(): Format;

    /**
     * @param Format $format
     * @return \SIVI\AFD\Models\Attribute
     */
    public function setFormat(Format $format): \SIVI\AFD\Models\Attribute;

    /**
     * @return Code
     */
    public function getCode(): Code;

    /**
     * @param Code $code
     * @return \SIVI\AFD\Models\Attribute
     */
    public function setCode(Code $code): \SIVI\AFD\Models\Attribute;

    /**
     * @return CodeList
     */
    public function getCodeList(): CodeList;

    /**
     * @param CodeList $codeList
     * @return \SIVI\AFD\Models\Attribute
     */
    public function setCodeList(CodeList $codeList): \SIVI\AFD\Models\Attribute;

    /**
     * @return string
     */
    public function getDescription(): string;

    /**
     * @param string $description
     * @return \SIVI\AFD\Models\Attribute
     */
    public function setDescription(string $description): \SIVI\AFD\Models\Attribute;

    /**
     * @return string
     */
    public function getExplanation(): string;

    /**
     * @param string $explanation
     * @return \SIVI\AFD\Models\Attribute
     */
    public function setExplanation(string $explanation): \SIVI\AFD\Models\Attribute;

    /**
     * @return mixed
     */
    public function getTypeLabel();

    /**
     * @return mixed|null
     */
    public function getCodeListDescription();
}