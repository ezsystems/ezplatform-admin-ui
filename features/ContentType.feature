Feature: Content types management
  As an administrator
  In order to customize my eZ installation
  I want to manage my Content types.

  Background:
    Given I am logged as "admin"
      And I go to "Content Types" in "Admin" tab
      And I go to "Content" "Content Type Group" page

  @javascript @common
  Scenario: Changes can be discarded while creating Content Type
    When I start creating new "Content Type" in "Content"
      And I set fields
      | label      | value    |
      | Name       | Test CT  |
      | Identifier | iTestCT  |
      And I click on the edit action bar button "Discard changes"
    Then I should be on "Content Type Group" "Content" page
      And there's no "Test CT" on "Content" "Content Type Group" list

  @javascript @common
  Scenario: New Content Type can be added to Content Type Group
    When I start creating new "Content Type" in "Content"
      And I set fields
      | label                | value    |
      | Name                 | Test CT  |
      | Identifier           | iTestCT  |
      | Content name pattern | <name>   |
      And I add field "ezcountry" to Content Type definition
      And I set "Name" in "ezcountry" to "CountryField"
      And I click on the edit action bar button "Save"
    Then I should be on "Content Type" "Test CT" page
      And Content Type has proper Global properties
      | label                | value    |
      | Name                 | Test CT  |
      | Identifier           | iTestCT  |
      | Content name schema  | <name>   |
      And Content Type "Test CT" has field "CountryField" of type "ezcountry"
      And notification that "Content type" "Test CT" is updated appears

  @javascript @common
  Scenario: I can navigate to Content Type Group through breadcrumb
    Given I go to "Test CT" "Content Type" page from "Content"
    When I click on "Content" on breadcrumb
    Then I should be on "Content Type Group" "Content" page

  @javascript @common
  Scenario: Changes can be discarded while editing Content type
    Given there's "Test CT" on "Content" "Content Type Group" list
    When I start editing "Content Type" "Test CT" from "Content"
      And I set "Name" to "Test CT edited"
      And I click on the edit action bar button "Discard changes"
    Then I should be on "Content Type Group" "Content" page
      And there's "Test CT" on "Content" "Content Type Group" list
      And there's no "Test CT edited" on "Content" "Content Type Group" list

  @javascript @common
  Scenario: New Field can be added while editing Content Type
    Given there's "Test CT" on "Content" "Content Type Group" list
    When I start editing "Content Type" "Test CT" from "Content"
      And I set "Name" to "Test CT edited"
      And I add field "ezdate" to Content Type definition
      And I set "Name" in "ezdate" to "DateField"
      And I click on the edit action bar button "Save"
    Then I should be on "Content Type" "Test CT edited" page
      And Content Type has proper Global properties
      | label                | value           |
      | Name                 | Test CT edited  |
      | Identifier           | iTestCT         |
      | Content name schema  | <name>          |
      And Content Type "Test CT" has proper fields
      | fieldName      | fieldType |
      | CountryField   | ezcountry |
      | DateField      | ezdate    |
      And notification that "Content type" "Test CT edited" is updated appears

  @javascript @common
  Scenario: Content type can be deleted from Content Type Group
    Given there's "Test CT edited" on "Content" "Content Type Group" list
    When I delete "Content Type" from "Content"
      | item           |
      | Test CT edited |
    Then there's no "Test CT edited" on "Content" "Content Type Group" list
      And notification that "Content type" "Test CT edited" is deleted appears