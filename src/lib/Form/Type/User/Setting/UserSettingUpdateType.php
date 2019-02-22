<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\User\Setting;

use EzSystems\EzPlatformUser\Form\Type\UserSettingUpdateType as BaseUserSettingUpdateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @deprecated since 1.5, to be removed in 2.0. Use \EzSystems\EzPlatformUser\Form\Type\UserSettingUpdateType instead.
 */
class UserSettingUpdateType extends AbstractType
{
    /** @var \EzSystems\EzPlatformUser\Form\Type\UserSettingUpdateType */
    private $userSettingUpdateType;

    /**
     * @param \EzSystems\EzPlatformUser\Form\Type\UserSettingUpdateType $userSettingUpdateType
     */
    public function __construct(BaseUserSettingUpdateType $userSettingUpdateType)
    {
        $this->userSettingUpdateType = $userSettingUpdateType;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->userSettingUpdateType->buildForm($builder, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $this->userSettingUpdateType->configureOptions($resolver);
    }
}
