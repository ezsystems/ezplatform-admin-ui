@IbexaOSS @IbexaContent @IbexaExperience @IbexaCommerce
Feature: Languages management
  As an administrator
  In order to customize my project
  I want to manage Languages for my website.

  Background:
        Given I am logged as admin
        And I open "Languages" page in admin SiteAccess

  @javascript
  Scenario: Changes can be discarded while creating new Language
    When I create a new Language
      And I set fields
        | label         | value   |
        | Name          | Deutsch |
        | Language code | de-DE   |
      And I click on the edit action bar button "Discard changes"
    Then I should be on "Languages" page
      And there's no "Deutsch" Language on Languages list

  @javascript
  Scenario: New Language can be added
    When I create a new Language
      And I set fields
        | label         | value   |
        | Name          | Deutsch |
        | Language code | de-DE   |
      And I click on the edit action bar button "Create"
    Then I should be on "Deutsch" Language page
      And Language has proper attributes
        | Name         | Language code   | Enabled |
        | Deutsch      | de-DE           | true    |

  @javascript
  Scenario: New Language with existing language code cannot be added
    When I create a new Language
      And I set fields
        | label         | value          |
        | Name          | Deutsch Second |
        | Language code | de-DE          |
      And I click on the edit action bar button "Create"
    Then error notification that "language with the specified language code already exists" appears

  @javascript
  Scenario: Changes can be discarded while editing Language
    Given there's a "Deutsch" Language on Languages list
    When I edit "Deutsch" from Languages list
      And I set fields
        | label | value          |
        | Name  | Edited Deutsch |
      And I click on the edit action bar button "Discard changes"
    Then I should be on "Languages" page
      And there's a "Deutsch" Language on Languages list
      And there's no "Edited Deutsch" Language on Languages list

  @javascript
  Scenario: Language can be disabled
    Given there's a "Deutsch" Language on Languages list
    When I edit "Deutsch" from Languages list
      And I set fields
        | label         | value          |
        | Name          | Edited Deutsch |
        | Enabled       | false          |
      And I click on the edit action bar button "Save"
    Then I should be on "Edited Deutsch" Language page
      And notification that "Language" "Deutsch" is updated appears
      And Language has proper attributes
        | Name           | Language code   | Enabled |
        | Edited Deutsch | de-DE           | false   |

  @javascript
  Scenario: Language can be enabled
    Given I open "Edited Deutsch" Language page in admin SiteAccess
    And Language has proper attributes
      | Name            | Language code   | Enabled |
      | Edited Deutsch  | de-DE           | false   |
    When I start editing the Language
      And I set fields
        | label   | value |
        | Enabled | true  |
      And I click on the edit action bar button "Save"
    Then I should be on "Edited Deutsch" Language page
      And notification that "Language" "Edited Deutsch" is updated appears
      And Language has proper attributes
        | Name            | Language code   | Enabled |
        | Edited Deutsch  | de-DE           | true    |

  @javascript
  Scenario: Language can be deleted
    Given there's a "Edited Deutsch" Language on Languages list
    When I delete Language "Edited Deutsch"
    Then notification that "Language" "Edited Deutsch" is removed appears
      And there's no "Edited Deutsch" Language on Languages list

