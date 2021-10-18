@javascript @subtreeEditor @IbexaOSS @IbexaContent @IbexaExperience @IbexaCommerce
Feature: Verify that an Editor with Subtree limitations can perform all his tasks

  Background:
    Given I open Login page in admin SiteAccess
    And I log in as "SubtreeEditor" with password "Passw0rd-42"
    And I should be on "Dashboard" page
    And I go to "Content structure" in "Content" tab

  Scenario Outline: I can create and publish Content in locations I'm allowed
    Given I navigate to content "<parentContentItemName>" of type "DedicatedFolder" in "<contentPath>"
    And I start creating a new Content "DedicatedFolder"
    And I set content fields
      | label      | value         |
      | Name       | <contentName> |
      | Short name | <contentName> |
    When I click on the edit action bar button "Publish"
    Then success notification that "Content published." appears
    And I should be on Content view Page for "<newContentPath>/<contentName>"

    Examples:
      | parentContentItemName | contentPath                         | contentName | newContentPath                                   |
      | FolderParent          | root/FolderGrandParent              | NewContent1 | root/FolderGrandParent/FolderParent              |
      | FolderChild1          | root/FolderGrandParent/FolderParent | NewContent2 | root/FolderGrandParent/FolderParent/FolderChild1 |

  Scenario Outline: I can edit Content in locations I'm allowed
    Given I navigate to content "<contentName>" of type "DedicatedFolder" in "<contentPath>"
    When I click on the edit action bar button "Edit"
    And I set content fields
      | label      | value         |
      | Name       | <newFieldValue> |
      | Short name | <newFieldValue> |
    And I click on the edit action bar button "Publish"
    Then success notification that "Content published." appears
    And I should be on Content view Page for "<parentContentPath>/<newFieldValue>"
    And content attributes equal
      | label    | value           |
      | Name     | <newFieldValue> |

    Examples:
      | contentPath                                      | contentName | newFieldValue       | parentContentPath                                |
      | root/FolderGrandParent/FolderParent/FolderChild1 | NewContent2 | NewContent2Edited   | root/FolderGrandParent/FolderParent/FolderChild1 |
      | root/FolderGrandParent/FolderParent              | NewContent1 | NewContent1Edited   | root/FolderGrandParent/FolderParent              |

  Scenario: I can move Content to Trash in locations I'm allowed
    Given I navigate to content "NewContent1Edited" of type "DedicatedFolder" in "root/FolderGrandParent/FolderParent"
    When I send content to trash
    And I should be on Content view Page for "root/FolderGrandParent/FolderParent"
    And there's no "NewContent1Edited" "DedicatedFolder" on Subitems list

  Scenario: I can move Content in locations I'm allowed
    Given I navigate to content "ContentToMove" of type "DedicatedFolder" in "root/FolderGrandParent/FolderParent/FolderChild1"
    When I click on the edit action bar button "Move"
    And I select content "root/FolderGrandParent/FolderParent" through UDW
    And I confirm the selection in UDW
    Then success notification that "'ContentToMove' moved to 'FolderParent'" appears
    And I should be on Content view Page for "root/FolderGrandParent/FolderParent/ContentToMove"

  Scenario: I cannot edit, create or send to trash Content in root location
    Then the buttons are disabled
      | buttonName     |
      | Create content |
      | Edit           |
    And the "Send to Trash" button is not visible

  Scenario: I cannot edit, create or send to trash Content outside my permissions
    Given I navigate to content "FolderGrandParent" of type "DedicatedFolder" in "root"
    Then the buttons are disabled
      | buttonName     |
      | Create content |
      | Edit           |
    And the "Send to Trash" button is not visible
