Feature: Object States management
  As an administrator
  In order to customize my eZ installation
  I want to manage Object States.

  Background:
    Given I am logged as "admin"
      And I go to "Object States" in "Admin" tab

  @javascript @common
  Scenario: Changes can be discarded while creating Object state group
    When I start creating new "Object state group"
      And I set fields
        | label                | value                          |
        | Name                 | Test Object State Group        |
        | Identifier           | TestObjectStateGroupIdentifier |
      And I click on the edit action bar button "Discard changes"
    Then I should be on "Object state groups" page
      And there's no "Test Object State Group" on "Object state groups" list

  @javascript @common
  Scenario: New Object state group can be added
    When I start creating new "Object state group"
      And I set fields
        | label                | value                          |
        | Name                 | Test Object State Group        |
        | Identifier           | TestObjectStateGroupIdentifier |
      And I click on the edit action bar button "Create"
    Then I should be on "Object state group" "Test Object State Group" page
      And "Object states" list in "Object state group" "Test Object State Group" is empty
      And "Object state group" "Test OSG" has proper attributes
        | label                   | value                          |
        | Object state group name | Test Object State Group        |
        | Identifier              | TestObjectStateGroupIdentifier |
      And notification that "Object state group" "Test Object State Group" is created appears

  @javascript @common
  Scenario: I can navigate to Admin / Object state Groups through breadcrumb
    Given I go to "Test Object State Group" "Object state group" page
    When I click on "Object states" on breadcrumb
    Then I should be on "Object state groups" page

  @javascript @common
  Scenario: Changes can be discarded while editing Object state groups
    Given there's "Test Object State Group" on "Object state groups" list
    When I start editing "Object state group" "Test Object State Group"
      And I set fields
        | label | value                          |
        | Name  | Test Object State Group edited |
      And I click on the edit action bar button "Discard changes"
    Then I should be on "Object state groups" page
      And there's "Test Object State Group" on "Object state groups" list
      And there's no "Test Object State Group edited" on "Object state groups" list

  @javascript @common
  Scenario: Object state group can be edited
    Given there's "Test Object State Group" on "Object state groups" list
    When I start editing "Object state group" "Test Object State Group"
      And I set fields
        | label | value                          |
        | Name  | Test Object State Group edited |
      And I click on the edit action bar button "Save"
    Then I should be on "Object state group" "Test Object State Group edited" page
      And notification that "Object state group" "Test Object State Group edited" is updated appears

  @javascript @common
  Scenario: Changes can be discarded while editing Object state group from group details
    Given I go to "Test Object State Group edited" "Object state group" page
    When I start editing "Object state group" "Test Object State Group edited" from details page
      And I set fields
        | label | value                           |
        | Name  | Test Object State Group edited2 |
      And I click on the edit action bar button "Discard changes"
    Then I should be on "Object state groups" page
      And there's "Test Object State Group edited" on "Object state groups" list
      And there's no "Test Object State Group edited2" on "Object state groups" list

  @javascript @common
  Scenario: Object state group can be edited from group details
    Given I go to "Test Object State Group edited" "Object state group" page
    When I start editing "Object state group" "Test Object State Group edited" from details page
      And I set fields
        | label | value                           |
        | Name  | Test Object State Group edited2 |
      And I click on the edit action bar button "Save"
    Then I should be on "Object state group" "Test Object State Group edited2" page
      And notification that "Object state group" "Test Object State Group edited2" is updated appears

  @javascript @common
  Scenario: Object state creation can be discarded
    Given I go to "Test Object State Group edited2" "Object state group" page
    When I start creating new "Object state" in "Test Object State Group edited2"
      And I set fields
        | label      | value                     |
        | Name       | Test Object State         |
        | Identifier | TestObjectStateIdentifier |
      And I click on the edit action bar button "Discard changes"
    Then I should be on "Object state group" "Test Object State Group edited2" page
      And there's no "Test Object State" on "Test Object State Group edited2" Object states list

  @javascript @common
  Scenario: New Object state can be added
    Given I go to "Test Object State Group edited2" "Object state group" page
    When I start creating new "Object state" in "Test Object State Group edited2"
      And I set fields
        | label      | value                     |
        | Name       | Test Object State         |
        | Identifier | TestObjectStateIdentifier |
      And I click on the edit action bar button "Create"
    Then I should be on "Object state" "Test Object State" page
      And "Object state" "Test Object State" has proper attributes
        | label             | value                     |
        | Object state name | Test Object State         |
        | Identifier        | TestObjectStateIdentifier |
      And notification that "Object state" "Test Object State" is created appears

  @javascript @common
  Scenario: I can navigate to Object state group page through breadcrumb
    Given I go to "Test Object State Group edited2" "Object state group" page
      And I go to "Test Object State" Object state page from "Test Object State Group edited2"
    When I click on "Object state group: Test Object State Group edited2" on breadcrumb
    Then I should be on "Object state group" "Test Object State Group edited2" page

  @javascript @common
  Scenario: Changes can be discarded while editing Object state
    Given I go to "Test Object State Group edited2" "Object state group" page
    When I start editing "Object state" "Test Object State" from "Test Object State Group edited2"
      And I set fields
        | label | value                    |
        | Name  | Test Object State edited |
      And I click on the edit action bar button "Discard changes"
    Then I should be on "Object state group" "Test Object State Group edited2" page
      And there's "Test Object State" on "Test Object State Group edited2" Object states list
      And there's no "Test Object State edited" on "Test Object State Group edited2" Object states list

  @javascript @common
  Scenario: Object state can be edited
    Given I go to "Test Object State Group edited2" "Object state group" page
    When I start editing "Object state" "Test Object State" from "Test Object State Group edited2"
      And I set fields
        | label | value                    |
        | Name  | Test Object State edited |
      And I click on the edit action bar button "Save"
    Then I should be on "Object state" "Test Object State edited" page
      And notification that "Object state" "Test Object State edited" is updated appears
      And "Object state" "Test Object State edited" has proper attributes
        | label             | value                     |
        | Object state name | Test Object State edited  |
        | Identifier        | TestObjectStateIdentifier |

  @javascript @common
  Scenario: Changes can be discarded while editing Object state from state details
    Given I go to "Test Object State Group edited2" "Object state group" page
      And I go to "Test Object State edited" Object state page from "Test Object State Group edited2"
    When I start editing "Object state" "Test Object State edited" from details page
      And I set fields
        | label | value                     |
        | Name  | Test Object State edited2 |
      And I click on the edit action bar button "Discard changes"
    Then I should be on "Object state group" "Test Object State Group edited2" page
      And there's "Test Object State edited" on "Test Object State Group edited2" Object states list
      And there's no "Test Object State edited2" on "Test Object State Group edited2" Object states list

  @javascript @common
  Scenario: Object State can be edited from state details
    Given I go to "Test Object State Group edited2" "Object state group" page
      And I go to "Test Object State edited" Object state page from "Test Object State Group edited2"
    When I start editing "Object state" "Test Object State edited" from details page
      And I set fields
        | label | value                     |
        | Name  | Test Object State edited2 |
      And I click on the edit action bar button "Save"
    Then I should be on "Object state" "Test Object State edited2" page
      And notification that "Object state" "Test Object State edited2" is updated appears
      And "Object state" "Test Object State edited" has proper attributes
        | label             | value                     |
        | Object state name | Test Object State edited2 |
        | Identifier        | TestObjectStateIdentifier |

  @javascript @common
  Scenario: Second object state can be created
    Given I go to "Test Object State Group edited2" "Object state group" page
    When I start creating new "Object state" in "Test Object State Group edited2"
      And I set fields
        | label      | value                      |
        | Name       | Test Object State 2        |
        | Identifier | TestObjectStateIdentifier2 |
      And I click on the edit action bar button "Create"
    Then I should be on "Object state" "Test Object State 2" page
      And "Object state" "Test Object State 2" has proper attributes
        | label             | value                      |
        | Object state name | Test Object State 2        |
        | Identifier        | TestObjectStateIdentifier2 |
      And notification that "Object state" "Test Object State 2" is created appears

  @javascript @common
  Scenario: Object state can be deleted
    Given I go to "Test Object State Group edited2" "Object state group" page
    When  I delete Object state from "Test Object State Group edited2"
        | item                |
        | Test Object State 2 |
    Then notification that "Object state" "Test Object State 2" is deleted appears
      And there's no "Test Object State 2" on "Test Object State Group edited2" Object states list

  @javascript @common
  Scenario: Object state group can be deleted
    Given there's "Test Object State Group edited2" on "Object state groups" list
    When I delete "Object state group"
      | item                            |
      | Test Object State Group edited2 |
    Then notification that "Object state group" "Test Object State Group edited2" is deleted appears
      And there's no "Test Object State Group edited2" on "Object state groups" list
