@IbexaOSS @IbexaContent @IbexaExperience @IbexaCommerce
Feature: Object States management
  As an administrator
  In order to customize my project
  I want to manage Object States.

  Background:
    Given I am logged as admin

  @javascript
  Scenario: Changes can be discarded while creating Object state group
    Given I open "Object State groups" page in admin SiteAccess
    When I create a new Object State group
      And I set fields
        | label                | value                          |
        | Name                 | Test Object State Group        |
        | Identifier           | TestObjectStateGroupIdentifier |
      And I click on the edit action bar button "Discard changes"
    Then I should be on "Object state groups" page
      And there's no "Test Object State Group" Object State group on Object State groups list

  @javascript
  Scenario: New Object state group can be added
    Given I open "Object State groups" page in admin SiteAccess
    When I create a new Object State group
      And I set fields
        | label                | value                          |
        | Name                 | Test Object State Group        |
        | Identifier           | TestObjectStateGroupIdentifier |
      And I click on the edit action bar button "Create"
    Then I should be on "Test Object State Group" Object State group page
      And "Test Object State Group" Object State group has no Object States
      And Object State group has proper attributes
        | label                   | value                          |
        | Object state group name | Test Object State Group        |
        | Identifier              | TestObjectStateGroupIdentifier |
      And notification that "Object state group" "Test Object State Group" is created appears

  @javascript
  Scenario: Changes can be discarded while editing Object state groups
    Given I open "Object State groups" page in admin SiteAccess
    And there's a "Test Object State Group" Object State group on Object State groups list
    When I edit "Test Object State Group" from Object State groups list
      And I set fields
        | label | value                          |
        | Name  | Test Object State Group edited |
      And I click on the edit action bar button "Discard changes"
    Then I should be on "Object state groups" page
      And there's a "Test Object State Group" Object State group on Object State groups list
      And there's no "Test Object State Group edited" Object State group on Object State groups list

  @javascript
  Scenario: Object state group can be edited
    Given I open "Object State groups" page in admin SiteAccess
    And there's a "Test Object State Group" Object State group on Object State groups list
    When I edit "Test Object State Group" from Object State groups list
      And I set fields
        | label | value                          |
        | Name  | Test Object State Group edited |
      And I click on the edit action bar button "Save"
    Then notification that "Object state group" "Test Object State Group edited" is updated appears
      And I should be on "Test Object State Group edited" Object State group page

  @javascript
  Scenario: Changes can be discarded while editing Object state group from group details
    Given I open "Test Object State Group edited" Object State group page in admin SiteAccess
    When I edit the Object State
      And I set fields
        | label | value                           |
        | Name  | Test Object State Group edited2 |
      And I click on the edit action bar button "Discard changes"
    Then I should be on "Object state groups" page
      And there's a "Test Object State Group edited" Object State group on Object State groups list
      And there's no "Test Object State Group edited2" Object State group on Object State groups list

  @javascript
  Scenario: Object state group can be edited from group details
    Given I open "Test Object State Group edited" Object State group page in admin SiteAccess
    When I edit the Object State
      And I set fields
        | label | value                           |
        | Name  | Test Object State Group edited2 |
      And I click on the edit action bar button "Save"
    Then notification that "Object state group" "Test Object State Group edited2" is updated appears
      And I should be on "Test Object State Group edited2" Object State group page

  @javascript
  Scenario: Object state creation can be discarded
    Given I open "Test Object State Group edited2" Object State group page in admin SiteAccess
    When I create a new Object State
      And I set fields
        | label      | value                     |
        | Name       | Test Object State         |
        | Identifier | TestObjectStateIdentifier |
      And I click on the edit action bar button "Discard changes"
    Then I should be on "Test Object State Group edited2" Object State group page
      And there's no "Test Object State" Object State on Object States list for "Test Object State Group edited2"

  @javascript
  Scenario: New Object state can be added
    Given I open "Test Object State Group edited2" Object State group page in admin SiteAccess
    When I create a new Object State
      And I set fields
        | label      | value                     |
        | Name       | Test Object State         |
        | Identifier | TestObjectStateIdentifier |
      And I click on the edit action bar button "Create"
    Then I should be on "Test Object State" Object State page
      And Object State has proper attributes
        | label             | value                     |
        | Object state name | Test Object State         |
        | Identifier        | TestObjectStateIdentifier |
      And notification that "Object state" "Test Object State" is created appears

  @javascript
  Scenario: Changes can be discarded while editing Object state
    Given I open "Test Object State Group edited2" Object State group page in admin SiteAccess
      And I start editing Object State "Test Object State" from Object State Group
      And I set fields
        | label | value                    |
        | Name  | Test Object State edited |
      And I click on the edit action bar button "Discard changes"
    Then I should be on "Test Object State Group edited2" Object State group page
    And there's a "Test Object State" Object State on Object States list for "Test Object State Group edited2"
    And there's no "Test Object State edited" Object State on Object States list for "Test Object State Group edited2"

  @javascript
  Scenario: Object state can be edited
    Given I open "Test Object State Group edited2" Object State group page in admin SiteAccess
    When I start editing Object State "Test Object State" from Object State Group
      And I set fields
        | label | value                    |
        | Name  | Test Object State edited |
      And I click on the edit action bar button "Save"
    Then notification that "Object state" "Test Object State edited" is updated appears
      And I should be on "Test Object State edited" Object State page
      And Object State has proper attributes
        | label             | value                     |
        | Object state name | Test Object State edited  |
        | Identifier        | TestObjectStateIdentifier |

  @javascript
  Scenario: Changes can be discarded while editing Object state from state details
    Given I open "Test Object State edited" Object State page in admin SiteAccess
    When I edit the Object State
    And I set fields
      | label | value                     |
      | Name  | Test Object State edited2 |
      And I click on the edit action bar button "Discard changes"
    Then I should be on "Test Object State Group edited2" Object State group page
      And there's a "Test Object State edited" Object State on Object States list for "Test Object State Group edited2"
      And there's no "Test Object State edited2" Object State on Object States list for "Test Object State Group edited2"

  @javascript
  Scenario: Object State can be edited from state details
    Given I open "Test Object State edited" Object State page in admin SiteAccess
    When I edit the Object State
      And I set fields
        | label | value                     |
        | Name  | Test Object State edited2 |
      And I click on the edit action bar button "Save"
    Then notification that "Object state" "Test Object State edited2" is updated appears
      And I should be on "Test Object State edited2" Object State page
      And Object state has proper attributes
        | label             | value                     |
        | Object state name | Test Object State edited2 |
        | Identifier        | TestObjectStateIdentifier |

  @javascript
  Scenario: Second object state can be created
    Given I open "Test Object State Group edited2" Object State group page in admin SiteAccess
    When I create a new Object State
      And I set fields
        | label      | value                      |
        | Name       | Test Object State 2        |
        | Identifier | TestObjectStateIdentifier2 |
      And I click on the edit action bar button "Create"
    Then I should be on "Test Object State 2" Object State page
      And Object state has proper attributes
        | label             | value                      |
        | Object state name | Test Object State 2        |
        | Identifier        | TestObjectStateIdentifier2 |
      And notification that "Object state" "Test Object State 2" is created appears

  @javascript
  Scenario: Object state can be deleted
    Given I open "Test Object State Group edited2" Object State group page in admin SiteAccess
    When I delete Object State "Test Object State 2"
    Then notification that "Object state" "Test Object State 2" is deleted appears
      And there's no "Test Object State 2" Object State on Object States list for "Test Object State Group edited2"

  @javascript
  Scenario: Object State group can be deleted
    Given I open "Object state groups" page in admin SiteAccess
    And there's a "Test Object State Group edited2" Object State group on Object State groups list
    When I delete Object State group "Test Object State Group edited2"
    Then notification that "Object state group" "Test Object State Group edited2" is deleted appears
      And there's no "Test Object State Group edited2" Object State group on Object State groups list
