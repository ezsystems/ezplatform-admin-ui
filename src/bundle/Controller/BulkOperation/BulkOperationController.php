<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Controller\BulkOperation;

use eZ\Publish\Core\REST\Common\Input\Dispatcher;
use eZ\Publish\Core\REST\Common\Message;
use EzSystems\EzPlatformAdminUi\REST\Value\BulkOperationResponse;
use EzSystems\EzPlatformAdminUi\REST\Value\Operation;
use EzSystems\EzPlatformAdminUi\REST\Value\OperationResponse;
use EzSystems\EzPlatformAdminUiBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class BulkOperationController extends Controller
{
    /** @var \eZ\Publish\Core\REST\Common\Input\Dispatcher */
    private $inputDispatcher;

    /** @var \Symfony\Component\HttpKernel\HttpKernel */
    private $httpKernel;

    /**
     * @param \eZ\Publish\Core\REST\Common\Input\Dispatcher $inputDispatcher
     * @param \Symfony\Component\HttpKernel\HttpKernel $httpKernel
     */
    public function __construct(
        Dispatcher $inputDispatcher,
        HttpKernel $httpKernel
    ) {
        $this->inputDispatcher = $inputDispatcher;
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
        $request->attributes->set('is_rest_request', true);

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
