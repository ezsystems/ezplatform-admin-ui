<?php

declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data;

use eZ\Publish\API\Repository\Values\User\Policy;

class PolicyData
{
    /** @var string */
    private $module;

    /** @var string */
    private $function;

    /** @var array */
    private $limitations;

    /**
     * PolicyData constructor.
     *
     * @param string $module
     * @param string $function
     * @param array $limitations
     */
    public function __construct($module = null, $function = null, array $limitations = [])
    {
        $this->module = $module;
        $this->function = $function;
        $this->limitations = $limitations;
    }

    public function getModuleFunction()
    {
        return [
            'module' => $this->getModule(),
            'function' => $this->getFunction()
        ];
    }

    public function setModuleFunction(array $moduleFunction)
    {
        $this->module = $moduleFunction['module'];
        $this->function = $moduleFunction['function'];
    }

    public function getLimitations(): array
    {
        return $this->limitations;
    }

    public function setLimitations(array $limitations)
    {
        $this->limitations = $limitations;
    }

    public function getModule()
    {
        return $this->module;
    }

    public function getFunction()
    {
        return $this->function;
    }

    public static function factory(Policy $policy): PolicyData
    {
        $data = new self();
        $data->module = $policy->module;
        $data->function = $policy->function;
        $data->limitations = $policy->limitations;

        return $data;
    }
}
