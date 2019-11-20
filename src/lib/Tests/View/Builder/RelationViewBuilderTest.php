<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tests\View\Builder;

use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use eZ\Publish\Core\MVC\Symfony\View\Configurator;
use eZ\Publish\Core\MVC\Symfony\View\ParametersInjector;
use EzSystems\EzPlatformAdminUi\View\Builder\RelationViewBuilder;
use PHPUnit\Framework\TestCase;

class RelationViewBuilderTest extends TestCase
{
    private const CONTEND_ID = 1;

    /** @var \eZ\Publish\Core\MVC\Symfony\View\Configurator|\PHPUnit\Framework\MockObject\MockObject */
    private $viewConfigurator;

    /** @var \eZ\Publish\Core\MVC\Symfony\View\ParametersInjector|\PHPUnit\Framework\MockObject\MockObject */
    private $viewParametersInjector;

    protected function setUp(): void
    {
        $this->viewConfigurator = $this->createMock(Configurator::class);
        $this->viewParametersInjector = $this->createMock(ParametersInjector::class);
    }

    public function testBuildView(): void
    {
        $builder = $this->getRelationViewBuilder();

        $view = $builder->buildView(['contentId' => self::CONTEND_ID]);

        $this->assertSame(1, $view->getContentId());
        $this->assertNull($view->getContent());
        $this->assertNull($view->getContentType());
        $this->assertNull($view->getLocation());
    }

    public function testBuildViewWithoutContentId(): void
    {
        $builder = $this->getRelationViewBuilder();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Argument \'ContentId\' is invalid: No content could be loaded from parameters');

        $builder->buildView([]);
    }

    private function getRelationViewBuilder(): RelationViewBuilder
    {
        return new RelationViewBuilder(
            $this->viewConfigurator,
            $this->viewParametersInjector
        );
    }
}
