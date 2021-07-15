@IbexaOSS @IbexaContent @IbexaExperience @IbexaCommerce @richtext
Feature: Content items creation
  As an administrator
  In order to manage content to my site
  I want to create and edit Content Items

  Background:
    Given I am logged as admin

  @javascript
  Scenario: Content draft can be saved
    Given I'm on Content view Page for root
    When I start creating a new content "Article"
      And I set content fields
        | label       | value              |
        | Title       | Test Article draft |
        | Short title | Test Article draft |
      And I click on the edit action bar button "Save"
    Then success notification that "Content draft saved." appears
      And I should be on Content update page for "Test Article draft"
      And I open the "Dashboard" page in admin SiteAccess
      And there's draft "Test Article draft" on Dashboard list

  @javascript @APIUser:admin
  Scenario: Content draft can be deleted
    Given I create "article" Content drafts
      | title     | short_title | parentPath | language |
      | TestDraft | TestDraft   | root        | eng-GB   |
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
      And I start creating a new content "Article"
      And I set content fields
        | label       | value                  |
        | Title       | TestArticleSavePublish |
        | Short title | TestArticleSavePublish |
        | Intro       | TestArticleIntro       |
      When I click on the edit action bar button "Save"
      And I should be on Content update page for "TestArticleSavePublish"
      And I click on the edit action bar button "Publish"
    Then success notification that "Content published." appears
    And I should be on Content view Page for "TestArticleSavePublish"

  @javascript @APIUser:admin
  Scenario: Content draft edition can be closed
    Given I create "article" Content drafts
      | title                  | short_title            | parentPath | language |
      | TestDraftDashboardEdit | TestDraftDashboardEdit | root       | eng-GB   |
    And I open the "Dashboard" page in admin SiteAccess
    And there's draft "TestDraftDashboardEdit" on Dashboard list
    And I start editing content draft "TestDraftDashboardEdit"
    And I should be on Content update page for "TestDraftDashboardEdit"
    When I click on the close button
    And I should be on Content view Page for root

  @javascript @APIUser:admin
  Scenario: Content draft can be created and published through draft list modal
    Given I create "article" Content items
      | title                | short_title          | parentPath | language |
      | ContentDraftConflict | ContentDraftConflict | root       | eng-GB   |
    And I create a new Draft for "ContentDraftConflict" Content item in "eng-GB"
      | title                         | short_title                 |
      | ContentDraftConflictVersion1 | ContentDraftConflictVersion2 |
    And I'm on Content view Page for "ContentDraftConflict"
    When I click on the edit action bar button "Edit"
      And I start creating new draft from draft conflict modal
      And I set content fields
        | label       | value                        |
        | Title       | ContentDraftConflictVersion2 |
        | Short title | ContentDraftConflictVersion2 |
      And I click on the edit action bar button "Publish"
    Then success notification that "Content published." appears
      And I should be on Content view Page for ContentDraftConflictVersion2
      And content attributes equal
        | label      | value                        |
        | Title       | ContentDraftConflictVersion2 |
        | Short title | ContentDraftConflictVersion2 |

  @javascript @APIUser:admin
  Scenario: Content draft from draft list modal can be published
    Given I create "article" Content items
      | title                           | short_title                     | parentPath        | language |
      | ContentDraftConflictFromTheList | ContentDraftConflictFromTheList | root              | eng-GB   |
    And I create a new Draft for "ContentDraftConflictFromTheList" Content item in "eng-GB"
      | title                                   | short_title                             |
      | ContentDraftConflictFromTheListVersion2 | ContentDraftConflictFromTheListVersion2 |
    And I'm on Content view Page for "ContentDraftConflictFromTheList"
    When I click on the edit action bar button "Edit"
      And I start editing draft with version number "2" from draft conflict modal
      And I set content fields
        | label  | value                                         |
        | Intro  | ContentDraftConflictFromTheListVersion2Edited |
      And I click on the edit action bar button "Publish"
    Then success notification that "Content published." appears
      And I should be on Content view Page for ContentDraftConflictFromTheListVersion2
      And content attributes equal
        | label       | value                                         |
        | Title       | ContentDraftConflictFromTheListVersion2       |
        | Short title | ContentDraftConflictFromTheListVersion2       |
        | Intro       | ContentDraftConflictFromTheListVersion2Edited |
