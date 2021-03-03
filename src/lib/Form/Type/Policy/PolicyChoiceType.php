<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\Policy;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class PolicyChoiceType extends AbstractType
{
    const MESSAGE_DOMAIN = 'forms';
    const MESSAGE_ID_PREFIX = 'role.policy.';
    const ALL_MODULES = 'all_modules';
    const ALL_FUNCTIONS = 'all_functions';
    const ALL_MODULES_ALL_FUNCTIONS = 'all_modules_all_functions';

    /** @var array */
    private $policyChoices;

    /**
     * PolicyChoiceType constructor.
     *
     * @param array $policyMap
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator, array $policyMap)
    {
        $this->policyChoices = $this->buildPolicyChoicesFromMap($policyMap);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new class() implements DataTransformerInterface {
            public function transform($value)
            {
                if ($value) {
                    return $value['module'] . '|' . $value['function'];
                }

                return null;
            }

            public function reverseTransform($value)
            {
                $module = null;
                $function = null;

                if ($value) {
                    list($module, $function) = explode('|', $value);
                }

                return [
                    'module' => $module,
                    'function' => $function,
                ];
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => $this->policyChoices,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): ?string
    {
        return ChoiceType::class;
    }

    /**
     * Returns a usable hash for the policy choice widget.
     * Key is the translation key based on "module" name.
     * Value is a hash with translation key based on "module" and "function as a key and "<module>|<function"> as a value.
     *
     * @param array $policyMap
     *
     * @return array
     */
    private function buildPolicyChoicesFromMap(array $policyMap): array
    {
        $policyChoices = [
            self::MESSAGE_ID_PREFIX . self::ALL_MODULES => [
                self::MESSAGE_ID_PREFIX . self::ALL_MODULES_ALL_FUNCTIONS => '*|*',
             ],
        ];

        foreach ($policyMap as $module => $functionList) {
            $moduleKey = self::MESSAGE_ID_PREFIX . $module;
            // For each module, add possibility to grant access to all functions.
            $policyChoices[$moduleKey] = [
                $moduleKey . '.' . self::ALL_FUNCTIONS => "$module|*",
            ];

            foreach ($functionList as $function => $limitationList) {
                $moduleFunctionKey = self::MESSAGE_ID_PREFIX . "{$module}.{$function}";
                $policyChoices[$moduleKey][$moduleFunctionKey] = "$module|$function";
            }
        }

        return $policyChoices;
    }
}
