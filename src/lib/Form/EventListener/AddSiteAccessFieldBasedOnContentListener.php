<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\EventListener;

use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use EzSystems\EzPlatformAdminUi\Form\Data\Content\CustomUrl\CustomUrlAddData;
use EzSystems\EzPlatformAdminUi\Siteaccess\NonAdminSiteaccessResolver;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;

class AddSiteAccessFieldBasedOnContentListener
{
    /** @var \EzSystems\EzPlatformAdminUi\Siteaccess\NonAdminSiteaccessResolver */
    private $nonAdminSiteaccessResolver;

    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    /**
     * @param \EzSystems\EzPlatformAdminUi\Siteaccess\NonAdminSiteaccessResolver $nonAdminSiteaccessResolver
     * @param \eZ\Publish\Core\MVC\ConfigResolverInterface $configResolver
     */
    public function __construct(
        NonAdminSiteaccessResolver $nonAdminSiteaccessResolver,
        ConfigResolverInterface $configResolver
    ) {
        $this->nonAdminSiteaccessResolver = $nonAdminSiteaccessResolver;
        $this->configResolver = $configResolver;
    }

    public function onPreSetData(FormEvent $event): void
    {
        /** @var CustomUrlAddData $data */
        $data = $event->getData();
        $location = $data->getLocation();

        $form = $event->getForm();

        $form->add(
            'root_location_id',
            ChoiceType::class,
            [
                'required' => false,
                'choice_loader' => new CallbackChoiceLoader($this->getCallableData($location)),
            ]
        );
    }

    protected function getCallableData(?Location $location): callable
    {
        return function () use ($location): array {
            return $this->loadSiteAccesses($location);
        };
    }

    /**
     * Provides data in format:
     * [
     *     site_access => location_id,
     *     ...
     * ].
     *
     * @return int[]
     */
    private function loadSiteAccesses(?Location $location): array
    {
        $siteAccesses = $location instanceof Location
            ? $this->nonAdminSiteaccessResolver->getSiteaccessesForLocation($location)
            : $this->nonAdminSiteaccessResolver->getSiteaccesses();

        $parameterName = 'content.tree_root.location_id';
        $defaultTreeRootLocationId = $this->configResolver->getParameter($parameterName);

        $data = [];
        foreach ($siteAccesses as $siteAccess) {
            $treeRootLocationId = $this->configResolver->hasParameter($parameterName, null, $siteAccess)
                ? $this->configResolver->getParameter($parameterName, null, $siteAccess)
                : $defaultTreeRootLocationId;

            $data[$siteAccess] = $treeRootLocationId;
        }

        return $data;
    }
}
