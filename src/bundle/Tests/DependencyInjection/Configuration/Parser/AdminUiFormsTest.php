<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\Tests\DependencyInjection\Configuration\Parser;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Configuration\Parser\AdminUiForms;
use PHPUnit\Framework\TestCase;

/**
 * Test AdminUiForms SiteAccess-aware Configuration Parser.
 */
class AdminUiFormsTest extends TestCase
{
    /**
     * @var \EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Configuration\Parser\AdminUiForms
     */
    private $parser;

    /**
     * @var \eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $contextualizer;

    protected function setUp(): void
    {
        $this->parser = new AdminUiForms();
        $this->contextualizer = $this->createMock(ContextualizerInterface::class);
    }

    /**
     * Test given Content edit form templates are sorted according to their priority when mapping.
     */
    public function testContentEditFormTemplatesAreMapped()
    {
        $scopeSettings = [
            'admin_ui_forms' => [
                'content_edit_form_templates' => [
                    ['template' => 'my_template-01.html.twig', 'priority' => 1],
                    ['template' => 'my_template-02.html.twig', 'priority' => 0],
                    ['template' => 'my_template-03.html.twig', 'priority' => 2],
                ],
            ],
        ];
        $currentScope = 'admin_group';

        $expectedTemplatesList = [
            'my_template-03.html.twig',
            'my_template-01.html.twig',
            'my_template-02.html.twig',
        ];

        $this->contextualizer
            ->expects($this->once())
            ->method('setContextualParameter')
            ->with(
                AdminUiForms::FORM_TEMPLATES_PARAM,
                $currentScope,
                $expectedTemplatesList
            );

        $this->parser->mapConfig($scopeSettings, $currentScope, $this->contextualizer);
    }
}
