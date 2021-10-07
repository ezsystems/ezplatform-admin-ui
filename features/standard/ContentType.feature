@IbexaOSS @IbexaContent @IbexaExperience @IbexaCommerce
Feature: Content types management
  As an administrator
  In order to customize my eZ installation
  I want to manage my Content types.

  Background:
    Given I am logged as admin

  @javascript
  Scenario: Changes can be discarded while creating Content Type
    Given I'm on Content Type Page for "Content" group
    When I create a new Content Type
      And I set fields
      | label      | value                     |
      | Name       | Test Content Type         |
      | Identifier | TestContentTypeIdentifier |
      And I click on the edit action bar button "Discard changes"
    Then I should be on Content Type group page for "Content" group
      And there's no "Test Content Type" on Content Types list

  @javascript
  Scenario: New Content Type can be added to Content Type group
    Given I'm on Content Type Page for "Content" group
    When I create a new Content Type
      And I set fields
        | label                | value                     |
        | Name                 | Test Content Type         |
        | Identifier           | TestContentTypeIdentifier |
        | Content name pattern | <name>                    |
      And I select "Content" category to Content Type definition
      And I add field "Country" to Content Type definition
      And I set "Name" to "Country field" for "Country" field
      And I click on the edit action bar button "Create"
    Then notification that "Content Type" "New Content Type" is updated appears
    Then I should be on Content Type page for "Test Content Type"
      And Content Type has proper Global properties
        | label                | value                     |
        | Name                 | Test Content Type         |
        | Identifier           | TestContentTypeIdentifier |
        | Content name pattern | <name>                    |
      And Content Type "Test Content Type" has proper fields
        | fieldName       | fieldType |
        | Country field   | ezcountry |

  @javascript @APIUser:admin
  Scenario: Changes can be discarded while editing Content type
    Given I create a "TestDiscard CT" Content Type in "Content" with "testdiscard" identifier
      | Field Type  | Name        | Identifier          | Required | Searchable | Translatable | Settings       |
      | Text line   | Name        | name	            | no      | yes	      | yes          |                  |
    And I'm on Content Type Page for "Content" group
    And there's a "TestDiscard CT" on Content Types list
    When I start editing Content Type "TestDiscard CT"
      And I set fields
        | label | value                    |
        | Name  | Test Content Type edited |
      And I click on the edit action bar button "Discard changes"
    Then I should be on Content Type group page for "Content" group
      And there's a "TestDiscard CT" on Content Types list
      And there's no "Test Content Type edited" on Content Types list

  @javascript @APIUser:admin
  Scenario: New Field can be added while editing Content Type
    Given I create a "TestEdit CT" Content Type in "Content" with "testedit" identifier
      | Field Type  | Name        | Identifier          | Required | Searchable | Translatable | Settings       |
      | Text line   | Name        | name	            | no      | yes	      | yes          |                  |
    And I'm on Content Type Page for "Content" group
    When I start editing Content Type "TestEdit CT"
      And I set fields
        | label | value                    |
        | Name  | Test Content Type edited |
      And I add field "Date" to Content Type definition
    And I set "Name" to "DateField" for "Date" field
      And I click on the edit action bar button "Save"
    Then success notification that "Content Type 'TestEdit CT' updated." appears
    Then I should be on Content Type page for "Test Content Type edited"
      And Content Type has proper Global properties
        | label                | value                     |
        | Name                 | Test Content Type edited  |
        | Identifier           | testedit                  |
        | Content name pattern | <name>                    |
      And Content Type "Test Content Type" has proper fields
        | fieldName      | fieldType |
        | Name           | ezstring  |
        | DateField      | ezdate    |

  @javascript @APIUser:admin
  Scenario: Content type can be deleted from Content Type group
    Given I create a "TestDelete CT" Content Type in "Content" with "testdelete" identifier
      | Field Type  | Name        | Identifier          | Required | Searchable | Translatable | Settings       |
      | Text line   | Name        | name	            | no      | yes	      | yes          |                  |
    And I'm on Content Type Page for "Content" group
    And there's a "TestDiscard CT" on Content Types list
    When I delete "TestDelete CT" Content Type
    Then success notification that "Content Type 'TestDelete CT' deleted." appears
    And there's no "TestDelete CT" on Content Types list
