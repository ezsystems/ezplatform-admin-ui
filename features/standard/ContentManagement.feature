@IbexaOSS @IbexaContent @IbexaExperience @IbexaCommerce
Feature: Content items creation
  As an administrator
  In order to manage content to my site
  I want to create, edit, copy and move content items.
  
Background:
      Given I am logged as admin

@javascript @APIUser:admin
Scenario: Content moving can be cancelled
  Given I create "folder" Content items
    | name               | short_name          | parentPath        | language |
    | ContentManagement  | ContentManagement   | root              | eng-GB   |
    | FolderToCancelMove | FolderToCancelMove  | ContentManagement | eng-GB   |
  And I'm on Content view Page for "ContentManagement/FolderToCancelMove"
  When I click on the edit action bar button "Move"
    And I select content "Media" through UDW
    And I close the UDW window
  Then I should be on Content view Page for "ContentManagement/FolderToCancelMove"

@javascript @APIUser:admin
Scenario: Content can be moved
  Given I create "folder" Content items
    | name               | short_name        | parentPath        | language |
    | ContentManagement  | ContentManagement | root              | eng-GB   |
    | FolderToMove       | FolderToMove      | ContentManagement | eng-GB   |
  And I'm on Content view Page for "ContentManagement/FolderToMove"
  When I click on the edit action bar button "Move"
    And I select content "Media/Files" through UDW
    And I confirm the selection in UDW
  Then success notification that "'FolderToMove' moved to 'Files'" appears
    And I should be on Content view Page for "Media/Files/FolderToMove"
    And I'm on Content view Page for "ContentManagement"
    And there's no "FolderToMove" "Folder" on Subitems list

@javascript @APIUser:admin
Scenario: Content copying can be cancelled
  Given I create "folder" Content items
    | name               | short_name         | parentPath        | language |
    | ContentManagement  | ContentManagement  | root              | eng-GB   |
    | FolderToCopyCancel | FolderToCopyCancel | ContentManagement | eng-GB   |
  And I'm on Content view Page for "ContentManagement/FolderToCopyCancel"
  When I click on the edit action bar button "Copy"
    And I select content "Media" through UDW
    And I close the UDW window
  Then I should be on Content view Page for "ContentManagement/FolderToCopyCancel"

@javascript @APIUser:admin
Scenario: Content can be copied
  Given I create "folder" Content items
    | name               | short_name         | parentPath        | language |
    | ContentManagement  | ContentManagement  | root              | eng-GB   |
    | FolderToCopy       | FolderToCopy       | ContentManagement | eng-GB   |
  And I'm on Content view Page for "ContentManagement/FolderToCopy"
  When I click on the edit action bar button "Copy"
  And I select content "Media/Files" through UDW
    And I confirm the selection in UDW
  Then success notification that "'FolderToCopy' copied to 'Files'" appears
    And I should be on Content view Page for "Media/Files/FolderToCopy"
    And I'm on Content view Page for "ContentManagement"
    And there's a "FolderToCopy" "Folder" on Subitems list

  @javascript @APIUser:admin
  Scenario: Subtree copying can be cancelled
    Given I create "folder" Content items
      | name                      | short_name                | parentPath        | language |
      | ContentManagement         | ContentManagement         | root              | eng-GB   |
      | FolderToSubtreeCopyCancel | FolderToSubtreeCopyCancel | ContentManagement | eng-GB   |
    And I'm on Content view Page for "ContentManagement/FolderToSubtreeCopyCancel"
    When I click on the edit action bar button "Copy Subtree"
    And I select content "Media" through UDW
    And I close the UDW window
    Then I should be on Content view Page for "ContentManagement/FolderToSubtreeCopyCancel"

  @javascript @APIUser:admin
  Scenario: Subtree can be copied
    Given I create "folder" Content items
      | name                      | short_name                | parentPath        | language |
      | ContentManagement         | ContentManagement         | root              | eng-GB   |
      | FolderToSubtreeCopy | FolderToSubtreeCopy | ContentManagement | eng-GB   |
    And I'm on Content view Page for "ContentManagement/FolderToSubtreeCopy"
    When I click on the edit action bar button "Copy Subtree"
    And I select content "Media" through UDW
    And I confirm the selection in UDW
    Then success notification that "Subtree 'FolderToSubtreeCopy' copied to Location 'Media'" appears
    And I should be on Content view Page for "Media/FolderToSubtreeCopy"
    And I'm on Content view Page for "ContentManagement"
    And there's a "FolderToSubtreeCopy" "Folder" on Subitems list
