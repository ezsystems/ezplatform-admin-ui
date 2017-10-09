<?php
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Tab\LocationView;


use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\Helper\FieldsGroups\FieldsGroupsList;
use EzSystems\EzPlatformAdminUi\Tab\AbstractTab;
use EzSystems\EzPlatformAdminUi\Tab\OrderedTabInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment;

class ContentTab extends AbstractTab implements OrderedTabInterface
{
    /** @var FieldsGroupsList */
    private $fieldsGroupsListHelper;

    /**
     * @param Environment $twig
     * @param TranslatorInterface $translator
     * @param FieldsGroupsList $fieldsGroupsListHelper
     */
    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        FieldsGroupsList $fieldsGroupsListHelper
    ) {
        parent::__construct($twig, $translator);

        $this->fieldsGroupsListHelper = $fieldsGroupsListHelper;
    }

    public function getIdentifier(): string
    {
        return 'content';
    }

    public function getName(): string
    {
        /** @Desc("Content") */
        return $this->translator->trans('tab.name.content', [], 'locationview');
    }

    public function getOrder(): int
    {
        return 100;
    }

    public function renderView(array $parameters): string
    {
        /** @var Content $content */
        $content = $parameters['content'];
        /** @var ContentType $contentType */
        $contentType = $parameters['contentType'];
        $fieldDefinitions = $contentType->getFieldDefinitions();
        $fieldDefinitionsByGroup = $this->groupFieldDefinitions($fieldDefinitions);

        return $this->twig->render(
            'EzPlatformAdminUiBundle:content/tab:content.html.twig',
            [
                'content' => $content,
                'fieldDefinitionsByGroup' => $fieldDefinitionsByGroup,
            ]
        );
    }

    /**
     * @param $fieldDefinitions
     *
     * @return mixed
     */
    private function groupFieldDefinitions($fieldDefinitions)
    {
        $fieldDefinitionsByGroup = [];
        foreach ($this->fieldsGroupsListHelper->getGroups() as $groupId => $groupName) {
            $fieldDefinitionsByGroup[$groupId] = [
                'name' => $groupName,
                'fieldDefinitions' => []
            ];
        }

        foreach ($fieldDefinitions as $fieldDefinition) {
            $groupId = $fieldDefinition->fieldGroup;
            if (!$groupId) {
                $groupId = $this->fieldsGroupsListHelper->getDefaultGroup();
            }

            $fieldDefinitionsByGroup[$groupId]['fieldDefinitions'][] = $fieldDefinition;
        }

        return $fieldDefinitionsByGroup;
    }
}
