<?php

namespace EzPlatformAdminUi\Form\DataMapper;

use eZ\Publish\API\Repository\Values\ValueObject;

interface DataMapperInterface
{
    public function map(ValueObject $value);

    public function reverseMap($data);
}