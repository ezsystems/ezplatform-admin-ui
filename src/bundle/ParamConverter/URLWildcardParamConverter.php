<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\ParamConverter;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\URLWildcardService;
use eZ\Publish\API\Repository\Values\Content\URLWildcard;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class URLWildcardParamConverter implements ParamConverterInterface
{
    private const PARAMETER_URL_WILDCARD_ID = 'urlWildcardId';

    /** @var \eZ\Publish\API\Repository\URLWildcardService */
    private $urlWildcardService;

    /**
     * @param \eZ\Publish\API\Repository\URLWildcardService $urlWildcardService
     */
    public function __construct(URLWildcardService $urlWildcardService)
    {
        $this->urlWildcardService = $urlWildcardService;
    }

    /**
     * @inheritdoc
     */
    public function apply(Request $request, ParamConverter $configuration): bool
    {
        if (empty($request->get(self::PARAMETER_URL_WILDCARD_ID))) {
            return false;
        }

        $id = (int) $request->get(self::PARAMETER_URL_WILDCARD_ID);

        try {
            $urlWildcard = $this->urlWildcardService->load($id);
        } catch (NotFoundException $e) {
            throw new NotFoundHttpException("URLWildcard {$id} not found.");
        }

        $request->attributes->set($configuration->getName(), $urlWildcard);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function supports(ParamConverter $configuration): bool
    {
        return URLWildcard::class === $configuration->getClass();
    }
}
