@IbexaOSS @IbexaContent @IbexaExperience @IbexaCommerce
Feature: Content type groups management
  As an administrator
  In order to customize my project
  I want to manage my Content Type groups

  Background:
    Given I am logged as admin

  @javascript
  Scenario: Changes can be discarded while creating new Content Type group
    Given I open "Content Type groups" page in admin SiteAccess
    When I create a new Content Type group
      And I set fields
        | label | value    |
        | Name  | Test Content Type Group |
      And I click on the edit action bar button "Discard changes"
    Then I should be on "Content Type groups" page
      And there's no "Test Content Type Group" Content Type group on Content Type groups list

  @javascript
  Scenario: New Content Type group can be added
    Given I open "Content Type groups" page in admin SiteAccess
    When I create a new Content Type group
      And I set fields
        | label | value    |
        | Name  | Test Content Type Group |
      And I click on the edit action bar button "Create"
    Then I should be on Content Type group page for "Test Content Type Group" group
    And there're no Content Types for that group

  @javascript
  Scenario: Changes can be discarded while editing Content Type group
    Given I open "Content Type groups" page in admin SiteAccess
    And there's a "Test Content Type Group" Content Type group on Content Type groups list
    When I start editing Content Type group "Test Content Type Group"
      And I set fields
        | label | value           |
        | Name  | Test Content Type Group edited |
      And I click on the edit action bar button "Discard changes"
    Then I should be on "Content Type groups" page
      And there's a "Test Content Type Group" Content Type group on Content Type groups list
      And there's no "Test Content Type Group edited" Content Type group on Content Type groups list

  @javascript
  Scenario: Content Type group can be edited
    Given I open "Content Type groups" page in admin SiteAccess
    And there's a "Test Content Type Group" Content Type group on Content Type groups list
    When I start editing Content Type group "Test Content Type Group"
      And I set fields
        | label | value                          |
        | Name  | Test Content Type Group edited |
      And I click on the edit action bar button "Save"
    Then I should be on Content Type group page for "Test Content Type Group edited" group
      And success notification that "Updated Content Type group 'Test Content Type Group'." appears

  @javascript
  Scenario: Content type group can be deleted
    Given I open "Content Type groups" page in admin SiteAccess
    And there's an empty "Test Content Type Group edited" Content Type group on Content Type groups list
    When I delete "Test Content Type Group edited" from Content Type groups
    Then success notification that "Deleted Content Type group 'Test Content Type Group edited'." appears
      And there's no "Test Content Type Group edited" Content Type group on Content Type groups list

  @javascript
  Scenario: Non-empty Content type group cannot be deleted
    Given I open "Content Type groups" page in admin SiteAccess
    When there's non-empty "Content" Content Type group on Content Type groups list
    Then Content Type group "Content" cannot be selected
