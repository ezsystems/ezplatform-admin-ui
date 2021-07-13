@IbexaOSS @IbexaContent @IbexaExperience @IbexaCommerce
Feature: Content items creation
  As an administrator
  In order to manage content to my site
  I want to create and edit Content Items

  Background:
    Given I am logged as admin

  @javascript
  Scenario: Content draft can be saved
    Given I'm on Content view Page for root
    When I start creating a new content "Folder"
      And I set content fields
        | label | value             |
        | Name  | Test Folder Draft |
      And I click on the edit action bar button "Save"
    Then success notification that "Content draft saved." appears
      And I should be on Content update page for "Test Folder Draft"
      And I open the "Dashboard" page in admin SiteAccess
      And there's draft "Test Folder Draft" on Dashboard list

  @javascript @APIUser:admin
  Scenario: Content draft can be deleted
    Given I create "folder" Content drafts
      | name      | short_name | parentPath | language |
      | TestDraft | TestDraft  | root       | eng-GB   |
      And I open the "Dashboard" page in admin SiteAccess
      And there's draft "TestDraft" on Dashboard list
    When I start editing content draft "TestDraft"
      And I click on the edit action bar button "Delete draft"
    Then I should be on Content view Page for root
      And I open the "Dashboard" page in admin SiteAccess
      And there's no draft "TestDraft" on Dashboard list

  @javascript
  Scenario: Content draft can be saved and then published
    Given I'm on Content view Page for root
      And I start creating a new content "Folder"
      And I set content fields
        | label | value                 |
        | Name  | TestFolderSavePublish |
      When I click on the edit action bar button "Save"
      And I should be on Content update page for "TestFolderSavePublish"
      And I click on the edit action bar button "Publish"
    Then success notification that "Content published." appears
    And I should be on Content view Page for "TestFolderSavePublish"

  @javascript @APIUser:admin
  Scenario: Content draft edition can be closed
    Given I create "folder" Content drafts
      | name                   | short_name             | parentPath | language |
      | TestDraftDashboardEdit | TestDraftDashboardEdit | root       | eng-GB   |
    And I open the "Dashboard" page in admin SiteAccess
    And there's draft "TestDraftDashboardEdit" on Dashboard list
    And I start editing content draft "TestDraftDashboardEdit"
    And I should be on Content update page for "TestDraftDashboardEdit"
    When I click on the close button
    And I should be on Content view Page for root

  @javascript @APIUser:admin
  Scenario: Content draft can be created and published through draft list modal
    Given I create "folder" Content items
      | name                 | short_name           | parentPath        | language |
      | ContentDraftConflict | ContentDraftConflict | root              | eng-GB   |
    And I create a new Draft for "ContentDraftConflict" Content item in "eng-GB"
      | name                         | short_name                   |
      | ContentDraftConflictVersion1 | ContentDraftConflictVersion2 |
    And I'm on Content view Page for "ContentDraftConflict"
    When I click on the edit action bar button "Edit"
      And I start creating new draft from draft conflict modal
      And I set content fields
        | label       | value                        |
        | Short name  | ContentDraftConflictVersion2 |
      And I click on the edit action bar button "Publish"
    Then success notification that "Content published." appears
      And I should be on Content view Page for ContentDraftConflictVersion2
      And content attributes equal
        | label      | value                        |
        | Name       | ContentDraftConflict         |
        | Short name | ContentDraftConflictVersion2 |

  @javascript @APIUser:admin
  Scenario: Content draft from draft list modal can be published
    Given I create "folder" Content items
      | name                            | short_name                      | parentPath        | language |
      | ContentDraftConflictFromTheList | ContentDraftConflictFromTheList | root              | eng-GB   |
    And I create a new Draft for "ContentDraftConflictFromTheList" Content item in "eng-GB"
      | name                                    | short_name                              |
      | ContentDraftConflictFromTheListVersion2 | ContentDraftConflictFromTheListVersion2 |
    And I'm on Content view Page for "ContentDraftConflictFromTheList"
    When I click on the edit action bar button "Edit"
      And I start editing draft with version number "2" from draft conflict modal
      And I set content fields
        | label      | value                                         |
        | Short name | ContentDraftConflictFromTheListVersion2Edited |
      And I click on the edit action bar button "Publish"
    Then success notification that "Content published." appears
      And I should be on Content view Page for ContentDraftConflictFromTheListVersion2Edited
      And content attributes equal
        | label      | value                                         |
        | Name       | ContentDraftConflictFromTheListVersion2       |
        | Short name | ContentDraftConflictFromTheListVersion2Edited |
