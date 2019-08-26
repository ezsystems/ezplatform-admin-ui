@javascript @subtreeEditor
Feature: Verify that an Editor with Subtree limitations can perform all his tasks

  Background:
    Given I open Login page
    And I login as "SubtreeEditor" with password "Passw0rd-42"
    And I go to "Content structure" in "Content" tab

  Scenario Outline: I can create and publish Content in locations I'm allowed
    Given I navigate to content "<parentContentItemName>" of type "DedicatedFolder" in "<contentPath>"
    And I start creating a new content "DedicatedFolder"
    And I set content fields
      | label      | value         |
      | Name       | <contentName> |
      | Short name | <contentName> |
    When I click on the edit action bar button "Publish"
    Then success notification that "Content published." appears
    And I should be on content container page "<contentName>" of type "DedicatedFolder" in "<newContentPath>"

    Examples:
      | parentContentItemName | contentPath                         | contentName | newContentPath                                   |
      | FolderParent          | root/FolderGrandParent              | NewContent1 | root/FolderGrandParent/FolderParent              |
      | FolderChild1          | root/FolderGrandParent/FolderParent | NewContent2 | root/FolderGrandParent/FolderParent/FolderChild1 |

  Scenario Outline: I can edit Content in locations I'm allowed
    Given I open UDW and go to "<contentPath>"
    When I click on the edit action bar button "Edit"
    And I set content fields
      | label      | value         |
      | Name       | <newFieldValue> |
      | Short name | <newFieldValue> |
    And I click on the edit action bar button "Publish"
    Then success notification that "Content published." appears
    And I should be on content container page "<newFieldValue>" of type "DedicatedFolder" in "<parentContentPath>"
    And content attributes equal
      | label    | value           |
      | Name     | <newFieldValue> |

    Examples:
      | contentPath                                                  | newFieldValue       | parentContentPath                                |
      | root/FolderGrandParent/FolderParent/FolderChild1/NewContent2 | NewContent2Edited   | root/FolderGrandParent/FolderParent/FolderChild1 |
      | root/FolderGrandParent/FolderParent/NewContent1              | NewContent1Edited   | root/FolderGrandParent/FolderParent              |

  Scenario: I can move Content to Trash in locations I'm allowed
    Given I navigate to content "NewContent1Edited" of type "DedicatedFolder" in "root/FolderGrandParent/FolderParent"
    When I send content to trash
    Then I should be on content container page "FolderParent" of type "DedicatedFolder" in "root/FolderGrandParent"
    And there's no "NewContent1Edited" "DedicatedFolder" on "FolderParent" Sub-items list

  Scenario: I can move Content in locations I'm allowed
    Given I navigate to content "ContentToMove" of type "DedicatedFolder" in "root/FolderGrandParent/FolderParent/FolderChild1"
    When I click on the edit action bar button "Move"
    And I select content "root/FolderGrandParent/FolderParent" through UDW
    And I confirm the selection in UDW
    Then success notification that "'ContentToMove' moved to 'FolderParent'" appears
    And I should be on content container page "ContentToMove" of type "DedicatedFolder" in "root/FolderGrandParent/FolderParent"

  Scenario Outline: I cannot edit, create or send to trash Content outside my permissions
    When I open UDW and go to "<contentPath>"
    Then the buttons are disabled
      | buttonName |
      | Create     |
      | Edit       |
    And the "Send to Trash" button is not visible

    Examples:
      | contentPath            |
      | root                   |
      | root/FolderGrandParent |