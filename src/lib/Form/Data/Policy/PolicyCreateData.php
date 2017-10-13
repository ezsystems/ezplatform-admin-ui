<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\Policy;

class PolicyCreateData
{
    /** @var string */
    private $module;

    /** @var string */
    private $function;

    /**
     * @return string
     */
    public function getModule(): ?string
    {
        return $this->module;
    }

    /**
     * @param string $module
     * @return $this
     */
    public function setModule(string $module)
    {
        $this->module = $module;

        return $this;
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
     * @return $this
     */
    public function setFunction(string $function)
    {
        $this->function = $function;

        return $this;
    }

    /**
     * @param array $policy
     */
    public function setPolicy(array $policy)
    {
        $this->setModule($policy['module']);
        $this->setFunction($policy['function']);
    }

    /**
     * @return array
     */
    public function getPolicy(): ?array
    {
        return [
            'module' => $this->getModule(),
            'function' => $this->getFunction(),
        ];
    }
}
