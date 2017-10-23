<?php
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

class RedirectableForm extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('on_success_redirection_url', HiddenType::class)
            ->add('on_failure_redirection_url', HiddenType::class);
    }
}