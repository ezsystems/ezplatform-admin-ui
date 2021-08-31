<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\ParamConverter;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\SectionService;
use eZ\Publish\API\Repository\Values\Content\Section;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SectionParamConverter implements ParamConverterInterface
{
    const PARAMETER_SECTION_ID = 'sectionId';

    /**
     * @var \eZ\Publish\API\Repository\SectionService
     */
    private $sectionService;

    /**
     * SectionParamConverter constructor.
     *
     * @param \eZ\Publish\API\Repository\SectionService $sectionService
     */
    public function __construct(SectionService $sectionService)
    {
        $this->sectionService = $sectionService;
    }

    /**
     * @inheritdoc
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        if (!$request->get(self::PARAMETER_SECTION_ID)) {
            return false;
        }

        $id = (int)$request->get(self::PARAMETER_SECTION_ID);

        try {
            $section = $this->sectionService->loadSection($id);
        } catch (NotFoundException $e) {
            throw new NotFoundHttpException("Section $id not found.");
        }

        $request->attributes->set($configuration->getName(), $section);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function supports(ParamConverter $configuration)
    {
        return Section::class === $configuration->getClass();
    }
}
