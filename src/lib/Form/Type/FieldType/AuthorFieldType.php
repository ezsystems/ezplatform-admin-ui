<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Form\Type\FieldType;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\Core\FieldType\Author\Type as AuthorType;
use eZ\Publish\Core\FieldType\Author\Author;
use eZ\Publish\Core\FieldType\Author\Value;
use EzSystems\EzPlatformAdminUi\Form\Type\FieldType\Author\AuthorCollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * Form Type representing ezauthor field type.
 */
class AuthorFieldType extends AbstractType
{
    /** @var \eZ\Publish\API\Repository\Repository */
    private $repository;

    /** @var int */
    private $defaultAuthor;

    /**
     * @param \eZ\Publish\API\Repository\Repository $repository
     */
    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'ezplatform_fieldtype_ezauthor';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->defaultAuthor = $options['default_author'];

        $builder
            ->add('authors', AuthorCollectionType::class, [])
            ->addViewTransformer($this->getViewTransformer())
            ->addEventListener(FormEvents::POST_SUBMIT, [$this, 'filterOutEmptyAuthors']);
    }

    /**
     * @param \Symfony\Component\Form\FormView $view
     * @param \Symfony\Component\Form\FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['attr']['default-author'] = $options['default_author'];
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Value::class,
            'default_author' => AuthorType::DEFAULT_VALUE_EMPTY,
        ])->setAllowedTypes('default_author', 'integer');
    }

    /**
     * Returns a view transformer which handles empty row needed to display add/remove buttons.
     *
     * @return \Symfony\Component\Form\DataTransformerInterface
     */
    public function getViewTransformer(): DataTransformerInterface
    {
        return new CallbackTransformer(function (Value $value) {
            if (0 === $value->authors->count()) {
                if ($this->defaultAuthor === AuthorType::DEFAULT_CURRENT_USER) {
                    $value->authors->append($this->fetchLoggedAuthor());
                } else {
                    $value->authors->append(new Author());
                }
            }

            return $value;
        }, function (Value $value) {
            return $value;
        });
    }

    /**
     * @param \Symfony\Component\Form\FormEvent $event
     */
    public function filterOutEmptyAuthors(FormEvent $event)
    {
        $value = $event->getData();

        $value->authors->exchangeArray(
            array_filter(
                $value->authors->getArrayCopy(),
                function (Author $author) {
                    return !empty($author->email) || !empty($author->name);
                }
            )
        );
    }

    /**
     * Returns currently logged user data, or empty Author object if none was found.
     *
     * @return \eZ\Publish\Core\FieldType\Author\Author
     */
    private function fetchLoggedAuthor(): Author
    {
        $author = new Author();

        try {
            $permissionResolver = $this->repository->getPermissionResolver();
            $userService = $this->repository->getUserService();
            $loggedUserId = $permissionResolver->getCurrentUserReference()->getUserId();
            $loggedUserData = $userService->loadUser($loggedUserId);

            $author->name = $loggedUserData->getName();
            $author->email = $loggedUserData->email;
        } catch (NotFoundException $e) {
            //Do nothing
        }

        return $author;
    }
}
