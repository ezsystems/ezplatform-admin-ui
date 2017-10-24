<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\Policy;

use EzSystems\EzPlatformAdminUi\Form\Data\OnFailureRedirect;
use EzSystems\EzPlatformAdminUi\Form\Data\OnFailureRedirectTrait;
use EzSystems\EzPlatformAdminUi\Form\Data\OnSuccessRedirect;
use EzSystems\EzPlatformAdminUi\Form\Data\OnSuccessRedirectTrait;

class PolicyCreateData implements OnSuccessRedirect, OnFailureRedirect
{
    use OnSuccessRedirectTrait;
    use OnFailureRedirectTrait;

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
     *
     * @return PolicyCreateData
     */
    public function setModule(string $module): PolicyCreateData
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
     *
     * @return PolicyCreateData
     */
    public function setFunction(string $function): PolicyCreateData
    {
        $this->function = $function;

        return $this;
    }

    /**
     * @param array $policy
     *
     * @return PolicyCreateData
     */
    public function setPolicy(array $policy): PolicyCreateData
    {
        $this->setModule($policy['module']);
        $this->setFunction($policy['function']);

        return $this;
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
