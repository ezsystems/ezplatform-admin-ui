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
use Symfony\Component\Translation\TranslatorInterface;

class PolicyChoiceType extends AbstractType
{
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
        $this->policyChoices = $this->buildPolicyChoicesFromMap($translator, $policyMap);
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
            'choices_as_values' => true,
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
     * Key is the humanized "module" name.
     * Value is a hash with "<module>|<function"> as key and humanized "function" name as value.
     *
     * @param TranslatorInterface $translator
     * @param array $policyMap
     *
     * @return array
     */
    private function buildPolicyChoicesFromMap(TranslatorInterface $translator, array $policyMap): array
    {
        $policyChoices = [
            'role.policy.all_modules' => [
                'role.policy.all_modules_all_functions' => '*|*',
            ],
        ];

        foreach ($policyMap as $module => $functionList) {
            $humanizedModule = $this->humanize($module);
            // For each module, add possibility to grant access to all functions.
            $policyChoices[$humanizedModule] = [
                "$humanizedModule / " . $translator->trans('role.policy.all_functions', [], 'ezrepoforms_role') => "$module|*",
            ];

            foreach ($functionList as $function => $limitationList) {
                $policyChoices[$humanizedModule][$humanizedModule . ' / ' . $this->humanize($function)] = "$module|$function";
            }
        }

        return $policyChoices;
    }

    /**
     * Makes a technical name human readable.
     *
     * Sequences of underscores are replaced by single spaces. The first letter
     * of the resulting string is capitalized, while all other letters are
     * turned to lowercase.
     *
     * @see \Symfony\Component\Form\FormRenderer::humanize()
     *
     * @param string $text the text to humanize
     *
     * @return string the humanized text
     */
    private function humanize(string $text): string
    {
        return ucfirst(trim(strtolower(preg_replace(['/([A-Z])/', '/[_\s]+/'], ['_$1', ' '], $text))));
    }
}
