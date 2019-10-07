<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\Processor\User;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\UserService;
use EzSystems\EzPlatformAdminUi\Event\FormActionEvent;
use EzSystems\EzPlatformAdminUi\Event\RepositoryFormEvents;
use EzSystems\EzPlatformAdminUi\RepositoryForms\Data\User\UserUpdateData;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Listens for and processes User update events.
 */
class UserUpdateFormProcessor implements EventSubscriberInterface
{
    /** @var UserService */
    private $userService;

    /** @var ContentService */
    private $contentService;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    public function __construct(
        UserService $userService,
        ContentService $contentService,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->userService = $userService;
        $this->contentService = $contentService;
        $this->urlGenerator = $urlGenerator;
    }

    public static function getSubscribedEvents()
    {
        return [
            RepositoryFormEvents::USER_UPDATE => ['processUpdate', 20],
        ];
    }

    public function processUpdate(FormActionEvent $event)
    {
        $data = $event->getData();

        if (!$data instanceof UserUpdateData) {
            return;
        }

        $form = $event->getForm();
        $languageCode = $form->getConfig()->getOption('languageCode');

        $this->setContentFields($data, $languageCode);
        $user = $this->userService->updateUser($data->user, $data);

        $redirectUrl = $form['redirectUrlAfterPublish']->getData() ?: $this->urlGenerator->generate(
            '_ezpublishLocation',
            ['locationId' => $user->contentInfo->mainLocationId],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $event->setResponse(new RedirectResponse($redirectUrl));
    }

    /**
     * @param UserUpdateData $data
     * @param string $languageCode
     */
    private function setContentFields(UserUpdateData $data, string $languageCode): void
    {
        $data->contentUpdateStruct = $this->contentService->newContentUpdateStruct();

        foreach ($data->fieldsData as $fieldDefIdentifier => $fieldData) {
            $data->contentUpdateStruct->setField($fieldDefIdentifier, $fieldData->value, $languageCode);
        }
    }
}
