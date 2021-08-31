<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Tests\Event;

use EzSystems\EzPlatformContentForms\Event\FormActionEvent;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

class FormActionEventTest extends TestCase
{
    public function testConstruct()
    {
        $form = $this->createMock(FormInterface::class);
        $data = new stdClass();
        $clickedButton = 'fooButton';
        $options = ['languageCode' => 'eng-GB', 'foo' => 'bar'];

        $event = new FormActionEvent($form, $data, $clickedButton, $options);
        self::assertSame($form, $event->getForm());
        self::assertSame($data, $event->getData());
        self::assertSame($clickedButton, $event->getClickedButton());
        self::assertSame($options, $event->getOptions());
    }

    public function testEventDoesntHaveResponse()
    {
        $event = new FormActionEvent(
            $this->createMock(FormInterface::class),
            new stdClass(), 'fooButton'
        );
        self::assertFalse($event->hasResponse());
        self::assertNull($event->getResponse());
    }

    public function testEventSetResponse()
    {
        $event = new FormActionEvent(
            $this->createMock(FormInterface::class),
            new stdClass(), 'fooButton'
        );
        self::assertFalse($event->hasResponse());
        self::assertNull($event->getResponse());

        $response = new Response();
        $event->setResponse($response);
        self::assertTrue($event->hasResponse());
        self::assertSame($response, $event->getResponse());
    }

    public function testGetOption()
    {
        $objectOption = new stdClass();
        $options = ['languageCode' => 'eng-GB', 'foo' => 'bar', 'obj' => $objectOption];

        $event = new FormActionEvent(
            $this->createMock(FormInterface::class),
            new stdClass(), 'fooButton', $options
        );
        self::assertTrue($event->hasOption('languageCode'));
        self::assertTrue($event->hasOption('foo'));
        self::assertTrue($event->hasOption('obj'));
        self::assertSame('eng-GB', $event->getOption('languageCode'));
        self::assertSame('bar', $event->getOption('foo'));
        self::assertSame($objectOption, $event->getOption('obj'));
    }
}
