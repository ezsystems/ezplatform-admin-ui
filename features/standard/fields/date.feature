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
      And a "folder" Content item named "DateFieldsContainer" exists in root
      | name                | short_name          |
      | DateFieldsContainer | DateFieldsContainer |
      And I am logged as admin
      And I'm on Content view Page for DateFieldsContainer
    When I start creating a new content "<fieldName> CT"
      And I set content fields
        | label    | <label1>    | <label2> | <label3> |
        | Field    | <value1>    | <value2> | <value3> |
        | Name     | <fieldName> |          |          |
      And I click on the edit action bar button "Publish"
    Then success notification that "Content published." appears
      And I should be on Content view Page for "DateFieldsContainer/<contentItemName>"
      And content attributes equal
          | label    | <label1> | <label2> | <label3> |
          | Field    | <value1> | <value2> | <value3> |

    Examples:
      | fieldInternalName    | fieldName                    | fieldSettings                                                         |  label1   | value1                                                                    | label2     | value2                | label3  | value3      | contentItemName           |
      | ezdate               | Date                         |                                                                       | value     | 11/23/2019                                                                |            |                       |         |             | Saturday 23 November 2019 |
      | ezdatetime           | Date and time                |                                                                       | date      | 11/23/2019                                                                | time       | 14:45                 |         |             | Sat 2019-23-11 14:45:00   |
      | eztime               | Time                         |                                                                       | value     | 14:45                                                                     |            |                       |         |             | 2:45:00 pm                |

  @javascript @APIUser:admin
  Scenario Outline: Edit content item with given field
    Given I am logged as admin
      And I'm on Content view Page for "DateFieldsContainer/<oldContentItemName>"
    When I click on the edit action bar button "Edit"
      And I set content fields
        | label    | <label1> | <label2> | <label3> |
        | Field    | <value1> | <value2> | <value3> |
      And I click on the edit action bar button "Publish"
    Then success notification that "Content published." appears
      And I should be on Content view Page for "DateFieldsContainer/<newContentItemName>"
      And content attributes equal
        | label    | <label1> | <label2> | <label3> |
        | Field    | <value1> | <value2> | <value3> |

    Examples:
      | label1    | value1                       | label2     | value2                   | label3  | value3    | oldContentItemName        | newContentItemName           |
      | value     | 12/30/2019                   |            |                          |         |           | Saturday 23 November 2019 | Monday 30 December 2019      |
      | date      | 12/30/2019                   | time       | 15:15                    |         |           | Sat 2019-23-11 14:45:00   | Mon 2019-30-12 15:15:00      |
      | value     | 15:15                        |            |                          |         |           | 2:45:00 pm                | 3:15:00 pm                   |
