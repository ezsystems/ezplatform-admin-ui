<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\ParamConverter;

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
     * @var SectionService
     */
    private $sectionService;

    /**
     * SectionParamConverter constructor.
     *
     * @param SectionService $sectionService
     */
    public function __construct(SectionService $sectionService)
    {
        $this->sectionService = $sectionService;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        if (!$request->get(self::PARAMETER_SECTION_ID)) {
            return false;
        }

        $id = (int)$request->get(self::PARAMETER_SECTION_ID);

        $section = $this->sectionService->loadSection($id);

        if (!$section) {
            throw new NotFoundHttpException("Section $id not found!");
        }

        $request->attributes->set($configuration->getName(), $section);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ParamConverter $configuration)
    {
        return Section::class === $configuration->getClass();
    }
}
