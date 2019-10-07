<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Mapper;

use eZ\Publish\API\Repository\Values\User\PolicyDraft;
use eZ\Publish\API\Repository\Values\ValueObject;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Role\PolicyCreateData;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\Role\PolicyUpdateData;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PolicyMapper implements FormDataMapperInterface
{
    /**
     * Maps a ValueObject from eZ content repository to a data usable as underlying form data (e.g. create/update struct).
     *
     * @param \eZ\Publish\API\Repository\Values\User\PolicyDraft|ValueObject $policyDraft
     * @param array $params
     *
     * @return PolicyCreateData|PolicyUpdateData
     */
    public function mapToFormData(ValueObject $policyDraft, array $params = [])
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $params = $resolver->resolve($params);

        if (!$this->isPolicyNew($policyDraft)) {
            $data = new PolicyUpdateData([
                'policyDraft' => $policyDraft,
                'roleDraft' => $params['roleDraft'],
                'initialRole' => $params['initialRole'],
                'moduleFunction' => "{$policyDraft->module}|{$policyDraft->function}",
                'limitationsData' => $this->generateLimitationList($policyDraft->getLimitations(), $params['availableLimitationTypes']),
            ]);
        } else {
            $data = new PolicyCreateData([
                'policyDraft' => $policyDraft,
                'roleDraft' => $params['roleDraft'],
                'initialRole' => $params['initialRole'],
            ]);
        }

        return $data;
    }

    private function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver
            ->setRequired(['roleDraft', 'initialRole', 'availableLimitationTypes'])
            ->setAllowedTypes('roleDraft', '\eZ\Publish\API\Repository\Values\User\RoleDraft')
            ->setAllowedTypes('initialRole', '\eZ\Publish\API\Repository\Values\User\Role');
    }

    private function isPolicyNew(PolicyDraft $policy)
    {
        return $policy->id === null;
    }

    /**
     * Generates the limitation list from existing limitations (already configured for current policy) and
     * available limitation types available for current policy (i.e. current module/function combination).
     *
     * @param \eZ\Publish\API\Repository\Values\User\Limitation[] $existingLimitations
     * @param \eZ\Publish\SPI\Limitation\Type[] $availableLimitationTypes
     *
     * @return array|\eZ\Publish\API\Repository\Values\User\Limitation[]
     */
    private function generateLimitationList(array $existingLimitations, array $availableLimitationTypes)
    {
        $limitations = [];
        foreach ($existingLimitations as $limitation) {
            $limitations[$limitation->getIdentifier()] = $limitation;
        }

        foreach ($availableLimitationTypes as $identifier => $limitationType) {
            if (isset($limitations[$identifier])) {
                continue;
            }

            $limitations[$identifier] = $limitationType->buildValue([]);
        }

        ksort($limitations);

        return $limitations;
    }
}
