<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Controller\BulkOperation;

use EzSystems\EzPlatformAdminUi\REST\Value\BulkOperationResponse;
use EzSystems\EzPlatformAdminUi\REST\Value\Operation;
use EzSystems\EzPlatformAdminUi\REST\Value\OperationResponse;
use EzSystems\EzPlatformRest\Message;
use EzSystems\EzPlatformRest\Server\Controller as RestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class BulkOperationController extends RestController
{
    /** @var \Symfony\Component\HttpKernel\HttpKernelInterface */
    private $httpKernel;

    /**
     * @param \Symfony\Component\HttpKernel\HttpKernelInterface $httpKernel
     */
    public function __construct(
        HttpKernelInterface $httpKernel
    ) {
        $this->httpKernel = $httpKernel;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \EzSystems\EzPlatformAdminUi\REST\Value\BulkOperationResponse
     *
     * @throws \Exception
     */
    public function bulkAction(Request $request): BulkOperationResponse
    {
        /** @var \EzSystems\EzPlatformAdminUi\REST\Value\BulkOperation $operationList */
        $operationList = $this->inputDispatcher->parse(
            new Message(
                ['Content-Type' => $request->headers->get('Content-Type')],
                $request->getContent()
            )
        );

        $responses = [];
        foreach ($operationList->operations as $operationId => $operation) {
            $response = $this->httpKernel->handle(
                $this->buildSubRequest($request, $operation),
                HttpKernelInterface::SUB_REQUEST
            );

            $responses[$operationId] = new OperationResponse(
                $response->getStatusCode(),
                $response->headers->all(),
                $response->getContent()
            );
        }

        return new BulkOperationResponse($responses);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \EzSystems\EzPlatformAdminUi\REST\Value\Operation $operation
     *
     * @return \Symfony\Component\HttpFoundation\Request
     */
    private function buildSubRequest(Request $request, Operation $operation): Request
    {
        $subRequest = Request::create(
            $operation->uri,
            $operation->method,
            $operation->parameters,
            [],
            [],
            [
                'HTTP_X-CSRF-Token' => $request->headers->get('X-CSRF-Token'),
                'HTTP_SiteAccess' => $request->headers->get('SiteAccess'),
            ],
            $operation->content
        );
        $subRequest->setSession($request->getSession());
        foreach ($operation->headers as $name => $value) {
            $subRequest->headers->set($name, $value);
        }

        return $subRequest;
    }
}
