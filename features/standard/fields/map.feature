@IbexaOSS @IbexaContent @IbexaExperience @IbexaCommerce @contentTypeFields
Feature: Content fields setting and editing
  As an administrator
  In order to manage content on my site
  I want to set, edit, copy and move content items.

  @javascript @APIUser:admin
  Scenario Outline: Create content item with given field
    Given I create a "<fieldName> CT" Content Type in "Content" with "<fieldInternalName>" identifier
      | Field Type  | Name        | Identifier          | Required | Searchable | Translatable | Settings       |
      | <fieldName> | Field       | <fieldInternalName> | no      | no	      | yes          | <fieldSettings>  |
      | Text line   | Name        | name	            | no      | yes	      | yes          |                  |
      And a "folder" Content item named "MapFieldsContainer" exists in root
      | name               | short_name         |
      | MapFieldsContainer | MapFieldsContainer |
      And I am logged as admin
      And I'm on Content view Page for MapFieldsContainer
    When I start creating a new content "<fieldName> CT"
      And I set content fields
        | label    | <label1>    | <label2> | <label3> |
        | Field    | <value1>    | <value2> | <value3> |
        | Name     | <fieldName> |          |          |
      And I click on the edit action bar button "Publish"
    Then success notification that "Content published." appears
      And I should be on Content view Page for "MapFieldsContainer/<contentItemName>"
      And content attributes equal
          | label    | <label1> | <label2> | <label3> |
          | Field    | <value1> | <value2> | <value3> |

    Examples:
      | fieldInternalName    | fieldName                    | fieldSettings                                                         |  label1   | value1                                                                    | label2     | value2                | label3  | value3      | contentItemName           |
      | ezgmaplocation       | Map location                 |                                                                       | latitude  | 34.1                                                                      | longitude  | -118.2                | address | Los Angeles | Los Angeles               |
      | ezcountry            | Country                      |                                                                       | value     | Angola                                                                    |            |                       |         |             | Angola                    |

  @javascript @APIUser:admin
  Scenario Outline: Edit content item with given field
    Given I am logged as admin
      And I'm on Content view Page for "MapFieldsContainer/<oldContentItemName>"
    When I click on the edit action bar button "Edit"
      And I set content fields
        | label    | <label1> | <label2> | <label3> |
        | Field    | <value1> | <value2> | <value3> |
      And I click on the edit action bar button "Publish"
    Then success notification that "Content published." appears
      And I should be on Content view Page for "MapFieldsContainer/<newContentItemName>"
      And content attributes equal
        | label    | <label1> | <label2> | <label3> |
        | Field    | <value1> | <value2> | <value3> |

    Examples:
      | label1    | value1                       | label2     | value2                   | label3  | value3    | oldContentItemName        | newContentItemName           |
      | latitude  | -37.8                        | longitude  | 145.0                    | address | Melbourne | Los Angeles               | Melbourne                    |
      | value     | Argentina                    |            |                          |         |           | Angola                    | Argentina                    |
