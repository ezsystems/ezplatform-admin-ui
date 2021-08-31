<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\EventListener;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\Values\Content\Language;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;

class AddLanguageFieldBasedOnContentListener
{
    /** @var \eZ\Publish\API\Repository\LanguageService */
    private $languageService;

    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /**
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \eZ\Publish\API\Repository\LanguageService $languageService
     */
    public function __construct(ContentService $contentService, LanguageService $languageService)
    {
        $this->contentService = $contentService;
        $this->languageService = $languageService;
    }

    /**
     * @param \Symfony\Component\Form\FormEvent $event
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function onPreSetData(FormEvent $event)
    {
        /** @var \EzSystems\EzPlatformAdminUi\Form\Data\Content\CustomUrl\CustomUrlAddData $data */
        $data = $event->getData();
        $location = $data->getLocation();
        if (null === $location) {
            return;
        }
        $contentInfo = $location->getContentInfo();
        $versionInfo = $this->contentService->loadVersionInfo($contentInfo);
        $contentLanguages = $versionInfo->languageCodes;

        $form = $event->getForm();

        $form->add(
            'language',
            ChoiceType::class,
            [
                'multiple' => false,
                'choice_loader' => new CallbackChoiceLoader($this->getCallableFilter($contentLanguages)),
                'choice_value' => 'languageCode',
                'choice_label' => 'name',
            ]
        );
    }

    /**
     * @param array $contentLanguages
     *
     * @return callable
     */
    protected function getCallableFilter(array $contentLanguages): callable
    {
        return function () use ($contentLanguages) {
            return $this->filterLanguages($contentLanguages);
        };
    }

    /**
     * @param array $contentLanguages
     *
     * @return array
     */
    protected function filterLanguages(array $contentLanguages): array
    {
        return array_filter(
            $this->languageService->loadLanguages(),
            static function (Language $language) use ($contentLanguages) {
                return in_array($language->languageCode, $contentLanguages, true);
            }
        );
    }
}
