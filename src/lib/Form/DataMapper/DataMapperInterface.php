<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\DataMapper;

use eZ\Publish\API\Repository\Values\ValueObject;

/**
 * Data Mapper provide interface to bidirectional transfer of data between a Struct objects and a Data objects.
 */
interface DataMapperInterface
{
    /**
     * Maps Struct object to Data object.
     *
     * @param \eZ\Publish\API\Repository\Values\ValueObject $value
     *
     * @return mixed
     */
    public function map(ValueObject $value);

    /**
     * Maps Data object to Struct object.
     *
     * @param mixed $data
     *
     * @return mixed
     */
    public function reverseMap($data);
}
