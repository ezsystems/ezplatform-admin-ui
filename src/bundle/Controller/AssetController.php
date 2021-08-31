<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Controller;

use Exception;
use eZ\Publish\Core\FieldType\Image\Value as ImageValue;
use eZ\Publish\Core\FieldType\ImageAsset\AssetMapper as ImageAssetMapper;
use EzSystems\EzPlatformAdminUi\Form\Data\Asset\ImageAssetUploadData;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class AssetController extends Controller
{
    const CSRF_TOKEN_HEADER = 'X-CSRF-Token';

    const LANGUAGE_CODE_KEY = 'languageCode';
    const FILE_KEY = 'file';

    /** @var \Symfony\Component\Validator\Validator\ValidatorInterface */
    private $validator;

    /** @var \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface */
    private $csrfTokenManager;

    /** @var \eZ\Publish\Core\FieldType\ImageAsset\AssetMapper */
    private $imageAssetMapper;

    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    /**
     * @param \Symfony\Component\Validator\Validator\ValidatorInterface $validator
     * @param \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface $csrfTokenManager
     * @param \eZ\Publish\Core\FieldType\ImageAsset\AssetMapper $imageAssetMapper
     * @param \Symfony\Contracts\Translation\TranslatorInterface $translator
     */
    public function __construct(
        ValidatorInterface $validator,
        CsrfTokenManagerInterface $csrfTokenManager,
        ImageAssetMapper $imageAssetMapper,
        TranslatorInterface $translator)
    {
        $this->validator = $validator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->imageAssetMapper = $imageAssetMapper;
        $this->translator = $translator;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentType
     */
    public function uploadImageAction(Request $request): Response
    {
        if ($this->isValidCsrfToken($request)) {
            $data = new ImageAssetUploadData(
                $request->files->get(self::FILE_KEY),
                $request->request->get(self::LANGUAGE_CODE_KEY)
            );

            $errors = $this->validator->validate($data);
            if ($errors->count() === 0) {
                try {
                    $file = $data->getFile();

                    $content = $this->imageAssetMapper->createAsset(
                        $file->getClientOriginalName(),
                        new ImageValue([
                            'path' => $file->getRealPath(),
                            'fileSize' => $file->getSize(),
                            'fileName' => $file->getClientOriginalName(),
                            'alternativeText' => $file->getClientOriginalName(),
                        ]),
                        $data->getLanguageCode()
                    );

                    return new JsonResponse([
                        'destinationContent' => [
                            'id' => $content->contentInfo->id,
                            'name' => $content->getName(),
                            'locationId' => $content->contentInfo->mainLocationId,
                        ],
                        'value' => $this->imageAssetMapper->getAssetValue($content),
                    ]);
                } catch (Exception $e) {
                    return $this->createGenericErrorResponse($e->getMessage());
                }
            } else {
                return $this->createInvalidInputResponse($errors);
            }
        }

        return $this->createInvalidCsrfResponse();
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    private function createInvalidCsrfResponse(): JsonResponse
    {
        $errorMessage = $this->translator->trans(
/** @Desc("Missing or invalid CSRF token") */ 'asset.upload.invalid_csrf', [], 'assets'
        );

        return $this->createGenericErrorResponse($errorMessage);
    }

    /**
     * @param \Symfony\Component\Validator\ConstraintViolationListInterface $errors
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    private function createInvalidInputResponse(ConstraintViolationListInterface $errors): JsonResponse
    {
        $errorMessages = [];
        foreach ($errors as $error) {
            $errorMessages[] = $error->getMessage();
        }

        return $this->createGenericErrorResponse(implode(', ', $errorMessages));
    }

    /**
     * @param string $errorMessage
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    private function createGenericErrorResponse(string $errorMessage): JsonResponse
    {
        return new JsonResponse([
            'status' => 'failed',
            'error' => $errorMessage,
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return bool
     */
    private function isValidCsrfToken(Request $request): bool
    {
        $csrfTokenValue = $request->headers->get(self::CSRF_TOKEN_HEADER);

        return $this->csrfTokenManager->isTokenValid(
            new CsrfToken('authenticate', $csrfTokenValue)
        );
    }
}
