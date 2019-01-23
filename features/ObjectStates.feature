Feature: Object States management
  As an administrator
  In order to customize my eZ installation
  I want to manage Object States.

  Background:
    Given I am logged as "admin"
      And I go to "Object States" in "Admin" tab

  @javascript @common @parallel-scenario
  Scenario: Changes can be discarded while creating Object State Group
    When I start creating new "Object State Group"
      And I set fields
        | label                | value                           |
        | Name                 | Test Object State Group 2       |
        | Identifier           | TestObjectStateGroupIdentifier2 |
      And I click on the edit action bar button "Discard changes"
    Then I should be on "Object State Groups" page
      And there's no "Test Object State Group 2" on "Object State Groups" list

  @javascript @common @parallel-scenario
  Scenario: New Object State Group can be added
    When I start creating new "Object State Group"
      And I set fields
        | label                | value                          |
        | Name                 | Test Object State Group        |
        | Identifier           | TestObjectStateGroupIdentifier |
      And I click on the edit action bar button "Create"
    Then I should be on "Object State Group" "Test Object State Group" page
      And "Object States" list in "Object State Group" "Test Object State Group" is empty
      And "Object State Group" "Test OSG" has proper attributes
        | label                   | value                          |
        | Object state group name | Test Object State Group        |
        | Identifier              | TestObjectStateGroupIdentifier |
      And notification that "Object state type group" "Test Object State Group" is created appears

  @javascript @common @parallel-wait @parallel-scenario
  Scenario: I can navigate to Admin / Object State Groups through breadcrumb
    Given I go to "Test Object State Group" "Object State Group" page
    When I click on "Object States" on breadcrumb
    Then I should be on "Object State Groups" page

  @javascript @common @parallel-scenario
  Scenario: Changes can be discarded while editing Object State Groups
    Given there's "Test Object State Group" on "Object State Groups" list
    When I start editing "Object State Group" "Test Object State Group"
      And I set fields
        | label | value                            |
        | Name  | Test Object State Group edited 2 |
      And I click on the edit action bar button "Discard changes"
    Then I should be on "Object State Groups" page
      And there's no "Test Object State Group edited 2" on "Object State Groups" list

  @javascript @common @parallel-scenario
  Scenario: Object State Group can be edited
    Given there's "Test Object State Group" on "Object State Groups" list
    When I start editing "Object State Group" "Test Object State Group"
      And I set fields
        | label | value                          |
        | Name  | Test Object State Group edited |
      And I click on the edit action bar button "Save"
    Then I should be on "Object State Group" "Test Object State Group edited" page
      And notification that "Object state group" "TestObjectStateGroupIdentifier" is updated appears

  @javascript @common @parallel-wait @parallel-scenario
  Scenario: Changes can be discarded while editing Object State Group from group details
    Given I go to "Test Object State Group edited" "Object State Group" page
    When I start editing "Object State Group" "Test Object State Group edited" from details page
      And I set fields
        | label | value                           |
        | Name  | Test Object State Group edited3 |
      And I click on the edit action bar button "Discard changes"
    Then I should be on "Object State Groups" page
      And there's "Test Object State Group edited" on "Object State Groups" list
      And there's no "Test Object State Group edited3" on "Object State Groups" list

  @javascript @common @parallel-scenario
  Scenario: Object State Group can be edited from group details
    Given I go to "Test Object State Group edited" "Object State Group" page
    When I start editing "Object State Group" "Test Object State Group edited" from details page
      And I set fields
        | label | value                           |
        | Name  | Test Object State Group edited2 |
      And I click on the edit action bar button "Save"
    Then I should be on "Object State Group" "Test Object State Group edited2" page
      And notification that "Object state group" "TestObjectStateGroupIdentifier" is updated appears

  @javascript @common @parallel-wait @parallel-scenario
  Scenario: Object State creation can be discarded
    Given I go to "Test Object State Group edited2" "Object State Group" page
    When I start creating new "Object State" in "Test Object State Group edited2"
      And I set fields
        | label      | value                      |
        | Name       | Test Object State 2        |
        | Identifier | TestObjectStateIdentifier2 |
      And I click on the edit action bar button "Discard changes"
    Then I should be on "Object State Group" "Test Object State Group edited2" page
      And there's no "Test Object State 2" on "Test Object State Group edited2" Object States list

  @javascript @common @parallel-scenario
  Scenario: New Object State can be added
    Given I go to "Test Object State Group edited2" "Object State Group" page
    When I start creating new "Object State" in "Test Object State Group edited2"
      And I set fields
        | label      | value                     |
        | Name       | Test Object State         |
        | Identifier | TestObjectStateIdentifier |
      And I click on the edit action bar button "Create"
    Then I should be on "Object State" "Test Object State" page
      And "Object State" "Test Object State" has proper attributes
        | label             | value                     |
        | Object state name | Test Object State         |
        | Identifier        | TestObjectStateIdentifier |
      And notification that "Object state" "Test Object State" is created appears

  @javascript @common @parallel-wait @parallel-scenario
  Scenario: I can navigate to Object State Group page through breadcrumb
    Given I go to "Test Object State Group edited2" "Object State Group" page
      And I go to "Test Object State" Object State page from "Test Object State Group edited2"
    When I click on "Object State Group: Test Object State Group edited2" on breadcrumb
    Then I should be on "Object State Group" "Test Object State Group edited2" page

  @javascript @common @parallel-scenario
  Scenario: Changes can be discarded while editing Object State
    Given I go to "Test Object State Group edited2" "Object State Group" page
    When I start editing "Object State" "Test Object State" from "Test Object State Group edited2"
      And I set fields
        | label | value                    |
        | Name  | Test Object State edited |
      And I click on the edit action bar button "Discard changes"
    Then I should be on "Object State Group" "Test Object State Group edited2" page
      And there's "Test Object State" on "Test Object State Group edited2" Object States list
      And there's no "Test Object State edited" on "Test Object State Group edited2" Object States list

  @javascript @common @parallel-wait @parallel-scenario
  Scenario: Object State can be edited
    Given I go to "Test Object State Group edited2" "Object State Group" page
    When I start editing "Object State" "Test Object State" from "Test Object State Group edited2"
      And I set fields
        | label | value                    |
        | Name  | Test Object State edited |
      And I click on the edit action bar button "Save"
    Then I should be on "Object State" "Test Object State edited" page
      And notification that "Object state" "TestObjectStateIdentifier" is updated appears
      And "Object State" "Test Object State edited" has proper attributes
        | label             | value                     |
        | Object state name | Test Object State edited  |
        | Identifier        | TestObjectStateIdentifier |

  @javascript @common @parallel-wait @parallel-scenario
  Scenario: Changes can be discarded while editing Object State from state details
    Given I go to "Test Object State Group edited2" "Object State Group" page
      And I go to "Test Object State edited" Object State page from "Test Object State Group edited2"
    When I start editing "Object State" "Test Object State edited" from details page
      And I set fields
        | label | value                     |
        | Name  | Test Object State edited3 |
      And I click on the edit action bar button "Discard changes"
    Then I should be on "Object State Group" "Test Object State Group edited2" page
      And there's no "Test Object State edited3" on "Test Object State Group edited2" Object States list

  @javascript @common @parallel-scenario
  Scenario: Object State can be edited from state details
    Given I go to "Test Object State Group edited2" "Object State Group" page
      And I go to "Test Object State edited" Object State page from "Test Object State Group edited2"
    When I start editing "Object State" "Test Object State edited" from details page
      And I set fields
        | label | value                     |
        | Name  | Test Object State edited2 |
      And I click on the edit action bar button "Save"
    Then I should be on "Object State" "Test Object State edited2" page
      And notification that "Object state" "TestObjectStateIdentifier" is updated appears
      And "Object State" "Test Object State edited" has proper attributes
        | label             | value                     |
        | Object state name | Test Object State edited2 |
        | Identifier        | TestObjectStateIdentifier |

  @javascript @common @parallel-scenario
  Scenario: Second object state can be created
    Given I go to "Test Object State Group edited2" "Object State Group" page
    When I start creating new "Object State" in "Test Object State Group edited2"
      And I set fields
        | label      | value                      |
        | Name       | Test Object State 2        |
        | Identifier | TestObjectStateIdentifier2 |
      And I click on the edit action bar button "Create"
    Then I should be on "Object State" "Test Object State 2" page
      And "Object State" "Test Object State 2" has proper attributes
        | label             | value                      |
        | Object state name | Test Object State 2        |
        | Identifier        | TestObjectStateIdentifier2 |
      And notification that "Object state" "Test Object State 2" is created appears

  @javascript @common
  Scenario: Object state can be deleted
    Given I go to "Test Object State Group edited2" "Object State Group" page
    When  I delete Object State from "Test Object State Group edited2"
      | item                |
      | Test Object State 2 |
    Then notification that "Object state" "TestObjectStateIdentifier2" is deleted appears
      And there's no "Test Object State 2" on "Test Object State Group edited2" Object States list

  @javascript @common
  Scenario: Object State Group can be deleted
    Given there's "Test Object State Group edited2" on "Object State Groups" list
    When I delete "Object State Group"
      | item                            |
      | Test Object State Group edited2 |
    Then notification that "Object state type group" "TestObjectStateGroupIdentifier" is deleted appears
      And there's no "Test Object State Group edited2" on "Object State Groups" list
