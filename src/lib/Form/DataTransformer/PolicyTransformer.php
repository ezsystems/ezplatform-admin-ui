<?php
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\DataTransformer;

use eZ\Publish\API\Repository\RoleService;
use Symfony\Component\Form\DataTransformerInterface;

class PolicyTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        return null !== $value
            ? implode(':', [$value['id'], $value['module'], $value['function']])
            : null;
    }

    public function reverseTransform($value)
    {
        if (null !== $value) {
            $parts = explode(':', $value);
            return [
                'id' => $parts[0],
                'module' => $parts[1],
                'function' => $parts[2],
            ];
        }

        return null;
    }
}