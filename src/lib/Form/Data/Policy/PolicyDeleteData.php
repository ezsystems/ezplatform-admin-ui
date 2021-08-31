<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\Policy;

use eZ\Publish\API\Repository\Values\User\Policy;

class PolicyDeleteData
{
    /** @var int */
    private $id;

    /** @var string */
    private $module;

    /** @var string */
    private $function;

    /** @var array */
    private $limitations;

    public function __construct(?Policy $policy = null)
    {
        if ($policy) {
            $this->id = $policy->id;
            $this->module = $policy->module;
            $this->function = $policy->function;
            $this->limitations = $policy->limitations;
        }
    }

    /**
     * @return string
     */
    public function getModule(): ?string
    {
        return $this->module;
    }

    /**
     * @param string $module
     */
    public function setModule(string $module)
    {
        $this->module = $module;
    }

    /**
     * @return string
     */
    public function getFunction(): ?string
    {
        return $this->function;
    }

    /**
     * @param string $function
     */
    public function setFunction(string $function)
    {
        $this->function = $function;
    }

    /**
     * @return array
     */
    public function getLimitations(): ?array
    {
        return $this->limitations;
    }

    /**
     * @param array $limitations
     */
    public function setLimitations(array $limitations)
    {
        $this->limitations = $limitations;
    }

    /**
     * @param array $policy
     */
    public function setPolicy(array $policy)
    {
        $this->setModule($policy['module']);
        $this->setFunction($policy['function']);

        if (isset($policy['id'])) {
            $this->setId((int)$policy['id']);
        }
    }

    /**
     * @return array
     */
    public function getPolicy(): ?array
    {
        return [
            'id' => $this->getId(),
            'module' => $this->getModule(),
            'function' => $this->getFunction(),
        ];
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }
}
