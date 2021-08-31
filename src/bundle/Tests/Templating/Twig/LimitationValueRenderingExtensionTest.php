<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUiBundle\Tests\Templating\Twig;

use Exception;
use eZ\Publish\API\Repository\Values\User\Limitation;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\Core\MVC\Symfony\Templating\Tests\Twig\Extension\FileSystemTwigIntegrationTestCase;
use EzSystems\EzPlatformAdminUi\Limitation\LimitationValueMapperInterface;
use EzSystems\EzPlatformAdminUi\Limitation\LimitationValueMapperRegistryInterface;
use EzSystems\EzPlatformAdminUi\Limitation\Templating\LimitationBlockRenderer;
use EzSystems\EzPlatformAdminUiBundle\Templating\Twig\LimitationValueRenderingExtension;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionProperty;
use Twig\Environment;
use Twig\Error\Error;
use Twig\Loader\ArrayLoader;
use Twig\Loader\ChainLoader;
use Twig\Loader\FilesystemLoader;

class LimitationValueRenderingExtensionTest extends FileSystemTwigIntegrationTestCase
{
    public function getExtensions(Environment $twig = null): array
    {
        $limitationBlockRenderer = new LimitationBlockRenderer(
            $this->createLimitationValueMapperRegistryMock(),
            $twig,
            $this->createConfigResolverMock()
        );

        return [
            new LimitationValueRenderingExtension($limitationBlockRenderer),
        ];
    }

    private function createLimitationValueMapperRegistryMock(): MockObject
    {
        $mapperMock = $this->createMock(LimitationValueMapperInterface::class);
        $mapperMock
            ->expects($this->atLeastOnce())
            ->method('mapLimitationValue')
            ->willReturnCallback(static function (Limitation $limitation) {
                return $limitation->limitationValues;
            });

        $registryMock = $this->createMock(LimitationValueMapperRegistryInterface::class);
        $registryMock
            ->expects($this->atLeastOnce())
            ->method('getMapper')
            ->willReturn($mapperMock);

        return $registryMock;
    }

    public function getLimitation($identifier, array $values): LimitationMock
    {
        return new LimitationMock($identifier, $values);
    }

    /**
     * @see \eZ\Publish\Core\MVC\Symfony\Templating\Tests\Twig\Extension\FileSystemTwigIntegrationTestCase::doIntegrationTest
     */
    protected function doIntegrationTest($file, $message, $condition, $templates, $exception, $outputs, $deprecation = ''): void
    {
        if (!$outputs) {
            $this->markTestSkipped('no legacy tests to run');
        }

        if ($condition) {
            eval('$ret = ' . $condition . ';');
            if (!$ret) {
                $this->markTestSkipped($condition);
            }
        }

        $loader = new ChainLoader([
            new ArrayLoader($templates),
            new FilesystemLoader($this->getFixturesDir()),
        ]);

        foreach ($outputs as $i => $match) {
            $config = array_merge([
                'cache' => false,
                'strict_variables' => true,
            ], $match[2] ? eval($match[2] . ';') : []);

            $twig = new Environment($loader, $config);
            $twig->addGlobal('global', 'global');
            // (!) Twig\Environment is dependency of LimitationBlockRenderer
            foreach ($this->getExtensions($twig) as $extension) {
                $twig->addExtension($extension);
            }

            foreach ($this->getTwigFilters() as $filter) {
                $twig->addFilter($filter);
            }

            foreach ($this->getTwigTests() as $test) {
                $twig->addTest($test);
            }

            foreach ($this->getTwigFunctions() as $function) {
                $twig->addFunction($function);
            }

            // avoid using the same PHP class name for different cases
            $p = new ReflectionProperty($twig, 'templateClassPrefix');
            $p->setAccessible(true);
            $p->setValue($twig, '__TwigTemplate_' . hash('sha256', uniqid(mt_rand(), true), false) . '_');

            try {
                $template = $twig->load('index.twig');
            } catch (Exception $e) {
                if (false !== $exception) {
                    $message = $e->getMessage();
                    $this->assertSame(trim($exception), trim(sprintf('%s: %s', \get_class($e), $message)));
                    $last = substr($message, \strlen($message) - 1);
                    $this->assertTrue('.' === $last || '?' === $last, $message, 'Exception message must end with a dot or a question mark.');

                    return;
                }

                throw new Error(sprintf('%s: %s', \get_class($e), $e->getMessage()), -1, $file, $e);
            }

            try {
                $output = trim($template->render(eval($match[1] . ';')), "\n ");
            } catch (Exception $e) {
                if (false !== $exception) {
                    $this->assertSame(trim($exception), trim(sprintf('%s: %s', \get_class($e), $e->getMessage())));

                    return;
                }

                $e = new Error(sprintf('%s: %s', \get_class($e), $e->getMessage()), -1, $file, $e);

                $output = trim(sprintf('%s: %s', \get_class($e), $e->getMessage()));
            }

            if (false !== $exception) {
                list($class) = explode(':', $exception);
                $constraintClass = class_exists('PHPUnit\Framework\Constraint\Exception') ? 'PHPUnit\Framework\Constraint\Exception' : 'PHPUnit_Framework_Constraint_Exception';
                $this->assertThat(null, new $constraintClass($class));
            }

            $expected = trim($match[3], "\n ");

            if ($expected !== $output) {
                printf("Compiled templates that failed on case %d:\n", $i + 1);

                foreach (array_keys($templates) as $name) {
                    echo "Template: $name\n";
                    echo $twig->compile($twig->parse($twig->tokenize($twig->getLoader()->getSourceContext($name))));
                }
            }
            $this->assertEquals($expected, $output, $message . ' (in ' . $file . ')');
        }
    }

    protected function getFixturesDir()
    {
        return __DIR__ . '/_fixtures/ez_render_limitation_value/';
    }

    private function createConfigResolverMock(): ConfigResolverInterface
    {
        $mock = $this->createMock(ConfigResolverInterface::class);
        $mock
            ->method('getParameter')
            ->willReturn([
                [
                    'template' => 'templates/limitation_value_1.html.twig',
                    'priority' => 10,
                ],
                [
                    'template' => 'templates/limitation_value_2.html.twig',
                    'priority' => 0,
                ],
                [
                    'template' => 'templates/limitation_value_3.html.twig',
                    'priority' => 20,
                ],
            ])
        ;

        return $mock;
    }
}
