<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type\Policy;

use EzSystems\EzPlatformAdminUi\Translation\Extractor\PolicyTranslationExtractor;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class PolicyChoiceType extends AbstractType
{
    /** @var array */
    private $policyChoices;

    /**
     * PolicyChoiceType constructor.
     *
     * @param array $policyMap
     * @param \Symfony\Contracts\Translation\TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator, array $policyMap)
    {
        $this->policyChoices = $this->buildPolicyChoicesFromMap($policyMap);
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => $this->policyChoices,
        ]);
    }

    /**
     * @inheritdoc
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
            PolicyTranslationExtractor::MESSAGE_ID_PREFIX . PolicyTranslationExtractor::ALL_MODULES => [
                PolicyTranslationExtractor::MESSAGE_ID_PREFIX . PolicyTranslationExtractor::ALL_MODULES_ALL_FUNCTIONS => '*|*',
             ],
        ];

        foreach ($policyMap as $module => $functionList) {
            $moduleKey = PolicyTranslationExtractor::MESSAGE_ID_PREFIX . $module;
            // For each module, add possibility to grant access to all functions.
            $policyChoices[$moduleKey] = [
                $moduleKey . '.' . PolicyTranslationExtractor::ALL_FUNCTIONS => "$module|*",
            ];

            foreach ($functionList as $function => $limitationList) {
                $moduleFunctionKey = PolicyTranslationExtractor::MESSAGE_ID_PREFIX . "{$module}.{$function}";
                $policyChoices[$moduleKey][$moduleFunctionKey] = "$module|$function";
            }
        }

        return $policyChoices;
    }
}
