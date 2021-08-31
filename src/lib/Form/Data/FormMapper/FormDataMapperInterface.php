<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Data\FormMapper;

use eZ\Publish\API\Repository\Values\ValueObject;

/**
 * A FormDataMapper will convert a value object from eZ content repository to a usable form data.
 */
interface FormDataMapperInterface
{
    /**
     * Maps a ValueObject from eZ content repository to a data usable as underlying form data (e.g. create/update struct).
     *
     * @param \eZ\Publish\API\Repository\Values\ValueObject $repositoryValueObject
     * @param array $params
     *
     * @return mixed
     */
    public function mapToFormData(ValueObject $repositoryValueObject, array $params = []);
}
