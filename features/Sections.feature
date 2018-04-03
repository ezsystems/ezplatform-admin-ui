Feature: Sections management
  As an administrator
  In order to customize my eZ installation
  I want to manage my content Sections.

  Background:
    Given I am logged as "admin"
    And I go to "Sections" in "Admin" tab

  @javascript @common
  Scenario: Changes can be discarded while creating new Section
    When I start creating new "Section"
    And I set fields
      | label      | value                  |
      | Name       | Test Section           |
      | Identifier | TestSectionIdentifier  |
    And I click on the edit action bar button "Discard changes"
    Then I should be on "Sections" page
    And there's no "Test Section" on "Sections" list

  @javascript @common
  Scenario: New Section can be added
    When I start creating new "Section"
    And I set fields
      | label      | value                  |
      | Name       | Test Section           |
      | Identifier | TestSectionIdentifier  |
    And I click on the edit action bar button "Create"
    Then I should be on "Section" "Test Section" page
    And "Content items" list in "Section" "Test Section" is empty
    And "Section" "Test Section" has proper attributes
      | label      | value                 |
      | Name       | Test Section          |
      | Identifier | TestSectionIdentifier |

  @javascript @common
  Scenario: I can navigate to Admin / Sections through breadcrumb
    Given I go to "Test Section" "Section" page
    When I click on "Sections" on breadcrumb
    Then I should be on "Sections" page

  @javascript @common
  Scenario: Content item assignation can be discarded
    Given there's "Test Section" on "Sections" list
    When I start assigning to "Test Section" from "Sections" page
    And I select content "Media/Images" through UDW
    And I close the UDW window
    Then I should be on "Sections" page
    And there's empty "Test Section" on "Sections" list

  @javascript @common
  Scenario: Content item can be assigned to section from the Sections list
    Given there's "Test Section" on "Sections" list
    When I start assigning to "Test Section" from "Sections" page
    And I select content "Media/Images" through UDW
    And I confirm the selection in UDW
    Then I should be on "Section" "Test Section" page
    And content items list in section "Test Section" contains items
      | Name   | Content Type | Path  |
      | Images | Folder       | Media |

  @javascript @common
  Scenario: Changes can be discarded while editing Section
    Given there's "Test Section" on "Sections" list
    When I start editing "Section" "Test Section"
    And I set "Name" to "Test Section edited"
    And I click on the edit action bar button "Discard changes"
    Then I should be on "Sections" page
    And there's "Test Section" on "Sections" list
    And there's no "Test Section edited" on "Sections" list

  @javascript @common
  Scenario: Section can be edited
    Given there's "Test Section" on "Sections" list
    When I start editing "Section" "Test Section"
    And I set "Name" to "Test Section edited"
    And I click on the edit action bar button "Save"
    Then I should be on "Section" "Test Section edited" page
    And notification that "Section" "Test Section edited" is updated appears

  @javascript @common
  Scenario: Changes can be discarded while editing Section from section details
    Given I go to "Test Section edited" "Section" page
    When I start editing "Section" "Test Section edited" from details page
    And I set "Name" to "Test Section edited2"
    And I click on the edit action bar button "Discard changes"
    Then I should be on "Sections" page
    And there's "Test Section edited" on "Sections" list
    And there's no "Test Section edited2" on "Sections" list

  @javascript @common
  Scenario: Section can be edited from section details
    Given I go to "Test Section edited" "Section" page
    When I start editing "Section" "Test Section edited" from details page
    And I set "Name" to "Test Section edited2"
    And I click on the edit action bar button "Save"
    Then I should be on "Section" "Test Section edited2" page
    And notification that "Section" "Test Section edited2" is updated appears

  @javascript @common
  Scenario: Non-empty section cannot be deleted
    Given there's non-empty "Test Section edited2" on "Sections" list
    Then "Section" "Test Section edited2" cannot be selected

  @javascript @common
  Scenario: Content item can be reassigned to section from the Sections details
    Given I go to "Media" "Section" page
    When I start assigning to "Media" from "Section" page
    And I select content "Media/Images" through UDW
    And I confirm the selection in UDW
    Then I should be on "Section" "Media" page
    And content items list in section "Media" contains items
      | Name   | Content Type | Path  |
      | Images | Folder       | Media |
    And Going to sections list we see there's empty "Test Section edited2" on list

  @javascript @common
  Scenario: Empty section can be deleted
    Given there's empty "Test Section edited2" on "Sections" list
    When I delete "Section"
      | item                 |
      | Test Section edited2 |
    Then there's no "Test Section edited2" on "Sections" list
    And notification that "Section" "Test Section edited2" is removed appears

  @javascript @common
  Scenario: Section can be deleted from section details
    When I start creating new "Section"
    And I set fields
      | label      | value                  |
      | Name       | Test Section           |
      | Identifier | TestSectionIdentifier2 |
    And I click on the edit action bar button "Create"
    And I delete "Section" from details page
      | item   |
      | Test Section |
    Then there's no "Test Section" on "Sections" list
    And notification that "Section" "Test Section" is removed appears