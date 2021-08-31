<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tests\Permission;

use eZ\Publish\API\Repository\Values\User\Limitation;
use eZ\Publish\API\Repository\Values\User\Limitation\ContentTypeLimitation;
use eZ\Publish\API\Repository\Values\User\Limitation\SectionLimitation;
use eZ\Publish\API\Repository\Values\User\Limitation\SubtreeLimitation;
use eZ\Publish\API\Repository\Values\User\LookupLimitationResult;
use eZ\Publish\API\Repository\Values\User\LookupPolicyLimitations;
use eZ\Publish\Core\Repository\Values\User\Policy;
use EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException;
use EzSystems\EzPlatformAdminUi\Permission\LookupLimitationsTransformer;
use PHPUnit\Framework\TestCase;

class LookupLimitationsTransformerTest extends TestCase
{
    public function testGetFlattenedLimitationsValues(): void
    {
        $limitations = [new ContentTypeLimitation(['limitationValues' => [2, 3]])];

        $policy = new Policy(['limitations' => $limitations]);

        $lookupLimitations = new LookupLimitationResult(
            true,
            [new ContentTypeLimitation(['limitationValues' => [1, 2]])],
            [new LookupPolicyLimitations($policy, $limitations)]
        );

        $flattenedLimitationsValues = (new LookupLimitationsTransformer())->getFlattenedLimitationsValues($lookupLimitations);

        $this->assertEqualsCanonicalizing([1, 2, 3], $flattenedLimitationsValues, '');
    }

    public function testGetGroupedLimitationValues(): void
    {
        $roleLimitations = [
            new SectionLimitation(['limitationValues' => [1]]),
            new SubtreeLimitation(['limitationValues' => [2]]),
        ];

        $limitations = [
            new SubtreeLimitation(['limitationValues' => [3]]),
            new ContentTypeLimitation(['limitationValues' => [4]]),
        ];

        $policy = new Policy(['limitations' => $limitations]);

        $lookupLimitations = new LookupLimitationResult(
            true,
            $roleLimitations,
            [new LookupPolicyLimitations($policy, $limitations)]
        );

        $flattenedLimitationsValues = (new LookupLimitationsTransformer())->getGroupedLimitationValues(
            $lookupLimitations,
            [Limitation::SUBTREE, Limitation::CONTENTTYPE]
        );
        $expected = [
            Limitation::SUBTREE => [2, 3],
            Limitation::CONTENTTYPE => [4],
        ];

        $this->assertEquals($expected, $flattenedLimitationsValues);
    }

    public function testGetGroupedLimitationValuesThrowException(): void
    {
        $emptyLimitationsIdentifiers = [];

        $limitations = [
            new SubtreeLimitation(['limitationValues' => [3]]),
            new ContentTypeLimitation(['limitationValues' => [4]]),
        ];

        $policy = new Policy(['limitations' => $limitations]);

        $lookupLimitations = new LookupLimitationResult(
            true,
            [new ContentTypeLimitation(['limitationValues' => [1, 2]])],
            [new LookupPolicyLimitations($policy, $limitations)]
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Argument \'limitationsIdentifiers\' is invalid: must contain at least one Limitation identifier');

        (new LookupLimitationsTransformer())->getGroupedLimitationValues($lookupLimitations, $emptyLimitationsIdentifiers);
    }
}
