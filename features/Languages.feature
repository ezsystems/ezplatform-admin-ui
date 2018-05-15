Feature: Languages management
  As an administrator
  In order to customize my eZ installation
  I want to manage Languages for my website.

  Background:
    Given I am logged as "admin"
      And I go to "Languages" in "Admin" tab

  @javascript @common
  Scenario: Changes can be discarded while creating new Language
    When I start creating new "Language"
      And I set fields
        | label         | value   |
        | Name          | Deutsch |
        | Language code | de-DE   |
      And I click on the edit action bar button "Discard changes"
    Then I should be on "Languages" page
      And there's no "Deutsch" on "Languages" list

  @javascript @common
  Scenario: New Language can be added
    When I start creating new "Language"
      And I set fields
        | label         | value   |
        | Name          | Deutsch |
        | Language code | de-DE   |
      And I click on the edit action bar button "Create"
    Then I should be on "Language" "Deutsch" page
      And "Language" "Deutsch" has proper attributes
        | label         | value    |
        | Name          | Deutsch  |
        | Language code | de-DE    |
        | Enabled       | true     |

  @javascript @common
  Scenario: New Language with existing language code cannot be added
    When I start creating new "Language"
      And I set fields
        | label         | value          |
        | Name          | Deutsch Second |
        | Language code | de-DE          |
      And I click on the edit action bar button "Create"
    Then error notification that "language with specified language code already exists" appears

  @javascript @common
  Scenario: I can navigate to Admin / Languages through breadcrumb
    Given I go to "Deutsch" "Language" page
    When I click on "Languages" on breadcrumb
    Then I should be on "Languages" page

  @javascript @common
  Scenario: Changes can be discarded while editing Language
    Given there's "Deutsch" on "Languages" list
    When I start editing "Language" "Deutsch"
      And I set fields
        | label | value          |
        | Name  | Edited Deutsch |
      And I click on the edit action bar button "Discard changes"
    Then I should be on "Languages" page
      And there's "Deutsch" on "Languages" list
      And there's no "Edited Deutsch" on "Languages" list

  @javascript @common
  Scenario: Language can be disabled
    Given there's "Deutsch" on "Languages" list
    When I start editing "Language" "Deutsch"
      And I set fields
        | label         | value          |
        | Name          | Edited Deutsch |
        | Enabled       | false          |
      And I click on the edit action bar button "Save"
    Then I should be on "Language" "Edited Deutsch" page
      And notification that "Language" "Deutsch" is updated appears
      And "Language" "Deutsch" has proper attributes
        | label         | value    |
        | Name          | Edited Deutsch  |
        | Language code | de-DE    |
        | Enabled       | false     |

  @javascript @common
  Scenario: Language can be enabled
    Given there's "Edited Deutsch" on "Languages" list
      And "Edited Deutsch" on "Languages" list has attribute "Enabled" set to "false"
    When I start editing "Language" "Edited Deutsch"
      And I set fields
        | label   | value |
        | Enabled | true  |
      And I click on the edit action bar button "Save"
    Then I should be on "Language" "Edited Deutsch" page
      And notification that "Language" "Edited Deutsch" is updated appears
      And "Language" "Edited Deutsch" has proper attributes
        | label         | value          |
        | Name          | Edited Deutsch |
        | Language code | de-DE          |
        | Enabled       | true           |

  @javascript @common
  Scenario: Language can be deleted
    Given there's "Edited Deutsch" on "Languages" list
    When I delete "Language"
      | item           |
      | Edited Deutsch |
    Then there's no "Edited Deutsch" on "Languages" list
      And notification that "Language" "Edited Deutsch" is removed appears

