Feature: Content type groups management
  As an administrator
  In order to customize my eZ installation
  I want to manage my Content types structure.

  Background:
    Given I am logged as "admin"
      And I go to "Content Types" in "Admin" tab

  @javascript @common
  Scenario: Changes can be discarded while creating new Content Type group
    When I start creating new "Content Type group"
      And I set fields
        | label | value    |
        | Name  | Test Content Type Group |
      And I click on the edit action bar button "Discard changes"
    Then I should be on "Content Type groups" page
      And there's no "Test Content Type Group" on "Content Type groups" list

  @javascript @common
  Scenario: New Content Type group can be added
    When I start creating new "Content Type group"
      And I set fields
        | label | value    |
        | Name  | Test Content Type Group |
      And I click on the edit action bar button "Create"
    Then I should be on "Content Type group" "Test Content Type Group" page
      And "Content Types" list in "Content Type group" "Test Content Type Group" is empty

  @javascript @common
  Scenario: I can navigate to Admin / Content Types through breadcrumb
    Given I go to "Test Content Type Group" "Content Type group" page
    When I click on "Content Types" on breadcrumb
    Then I should be on "Content Type groups" page

  @javascript @common
  Scenario: Changes can be discarded while editing Content Type group
    Given there's "Test Content Type Group" on "Content Type groups" list
    When I start editing "Content Type group" "Test Content Type Group"
      And I set fields
        | label | value           |
        | Name  | Test Content Type Group edited |
      And I click on the edit action bar button "Discard changes"
    Then I should be on "Content Type groups" page
      And there's "Test Content Type Group" on "Content Type groups" list
      And there's no "Test Content Type Group edited" on "Content Type groups" list

  @javascript @common
  Scenario: Content Type group can be edited
    Given there's "Test Content Type Group" on "Content Type groups" list
    When I start editing "Content Type group" "Test Content Type Group"
      And I set fields
        | label | value                          |
        | Name  | Test Content Type Group edited |
      And I click on the edit action bar button "Save"
    Then I should be on "Content Type group" "Test Content Type Group edited" page
      And success notification that "Updated Content Type group 'Test Content Type Group'." appears

  @javascript @common
  Scenario: Content type group can be deleted
    Given there's empty "Test Content Type Group edited" on "Content Type groups" list
    When I delete "Content Type group"
      | item            |
      | Test Content Type Group edited |
    Then success notification that "Deleted Content Type group 'Test Content Type Group edited'." appears
      And there's no "Test Content Type Group edited" on "Content Type groups" list

  @javascript @common
  Scenario: Non-empty Content type group cannot be deleted
    Given there's non-empty "Content" on "Content Type groups" list
    Then "Content Type group" "Content" cannot be selected
