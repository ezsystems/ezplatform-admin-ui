Feature: Content type groups management
  As an administrator
  In order to customize my eZ installation
  I want to manage my Content types structure.

  Background:
    Given I am logged as "admin"
      And I go to "Content Types" in "Admin" tab

  @javascript @common
  Scenario: Changes can be discarded while creating new Content Type Group
    When I start creating new "Content Type Group"
      And I set "Name" to "Test Content Type Group"
      And I click on the edit action bar button "Discard changes"
    Then I should be on "Content Type Groups" page
      And there's no "Test Content Type Group" on "Content Type Groups" list

  @javascript @common
  Scenario: New Content Type Group can be added
    When I start creating new "Content Type Group"
      And I set "Name" to "Test Content Type Group"
      And I click on the edit action bar button "Create"
    Then I should be on "Content Type Group" "Test Content Type Group" page
      And "Content Types" list in "Content Type Group" "Test Content Type Group" is empty

  @javascript @common
  Scenario: I can navigate to Admin / Content Types through breadcrumb
    Given I go to "Test Content Type Group" "Content Type Group" page
    When I click on "Content Types" on breadcrumb
    Then I should be on "Content Type Groups" page

  @javascript @common
  Scenario: Changes can be discarded while editing Content Type Group
    Given there's "Test Content Type Group" on "Content Type Groups" list
    When I start editing "Content Type Group" "Test Content Type Group"
      And I set "Name" to "Test Content Type Group edited"
      And I click on the edit action bar button "Discard changes"
    Then I should be on "Content Type Groups" page
      And there's "Test Content Type Group" on "Content Type Groups" list
      And there's no "Test Content Type Group edited" on "Content Type Groups" list

  @javascript @common
  Scenario: Content Type Group can be edited
    Given there's "Test Content Type Group" on "Content Type Groups" list
    When I start editing "Content Type Group" "Test Content Type Group"
      And I set "Name" to "Test Content Type Group edited"
      And I click on the edit action bar button "Save"
    Then I should be on "Content Type Group" "Test Content Type Group edited" page
      And notification that "Content type group" "Test Content Type Group" is updated appears

  @javascript @common
  Scenario: Content type group can be deleted
    Given there's empty "Test Content Type Group edited" on "Content Type Groups" list
    When I delete "Content Type Group"
      | item                           |
      | Test Content Type Group edited |
    Then there's no "Test Content Type Group edited" on "Content Type Groups" list
      And notification that "Content type group" "Test Content Type Group edited" is deleted appears

  @javascript @common
  Scenario: Non-empty Content type group cannot be deleted
    Given there's non-empty "Content" on "Content Type Groups" list
    Then "Content Type Group" "Content" cannot be selected
