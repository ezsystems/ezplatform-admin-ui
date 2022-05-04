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
      And I am logged as admin
      And I'm on Content view Page for root
    When I start creating a new content "<fieldName> CT"
      And I set content fields
        | label    | <label1>    | <label2> | <label3> |
        | Field    | <value1>    | <value2> | <value3> |
        | Name     | <fieldName> |          |          |
      And I click on the edit action bar button "Publish"
    Then success notification that "Content published." appears
      And I should be on Content view Page for "<contentItemName>"
      And content attributes equal
          | label    | <label1> | <label2> | <label3> |
          | Field    | <value1> | <value2> | <value3> |

    Examples:
      | fieldInternalName    | fieldName                    | fieldSettings                                                         |  label1   | value1                                                                    | label2     | value2                | label3  | value3      | contentItemName           |
      | ezselection          | Selection                    | is_multiple:false,options:A first-Bielefeld-TestValue-Turtles-Zombies | value     | TestValue                                                                 |            |                       |         |             | TestValue                 |
      | ezboolean            | Checkbox                     |                                                                       | value     | true                                                                      |            |                       |         |             | 1                         |
      | ezemail              | Email address                |                                                                       | value     | email@example.com                                                         |            |                       |         |             | email@example.com         |
      | ezfloat              | Float                        |                                                                       | value     | 11.11                                                                     |            |                       |         |             | 11.11                     |
      | ezisbn               | ISBN                         |                                                                       | value     | 978-3-16-148410-0                                                         |            |                       |         |             | 978-3-16-148410-0         |
      | ezinteger            | Integer                      |                                                                       | value     | 1111                                                                      |            |                       |         |             | 1111                      |
      | ezkeyword            | Keywords                     |                                                                       | value     | keyword1                                                                  |            |                       |         |             | keyword1                  |
      | ezmatrix             | Matrix                       | Min_rows:2,Columns:col1-col2-col3                                     | value     | col1:col2:col3,Ala:miała:kota,Szpak:dziobał:bociana,Bociana:dziobał:szpak |            |                       |         |             | Matrix                    |

  @javascript @APIUser:admin
  Scenario Outline: Edit content item with given field
    Given I am logged as admin
      And I'm on Content view Page for "<oldContentItemName>"
    When I click on the edit action bar button "Edit"
      And I set content fields
        | label    | <label1> | <label2> | <label3> |
        | Field    | <value1> | <value2> | <value3> |
      And I click on the edit action bar button "Publish"
    Then success notification that "Content published." appears
      And I should be on Content view Page for "<newContentItemName>"
      And content attributes equal
        | label    | <label1> | <label2> | <label3> |
        | Field    | <value1> | <value2> | <value3> |

    Examples:
      | label1    | value1                       | label2     | value2                   | label3  | value3    | oldContentItemName        | newContentItemName           |
      | value     | Bielefeld                    |            |                          |         |           | TestValue                 | Bielefeld                    |
      | value     | false                        |            |                          |         |           | 1                         | 0                            |
      | value     | edited.email@example.com     |            |                          |         |           | email@example.com         | edited.email@example.com     |
      | value     | 12.34                        |            |                          |         |           | 11.11                     | 12.34                        |
      | value     | 0-13-048257-9                |            |                          |         |           | 978-3-16-148410-0         | 0-13-048257-9                |
      | value     | 1234                         |            |                          |         |           | 1111                      | 1234                         |
      | value     | keyword2                     |            |                          |         |           | keyword1                  | keyword2                     |
      | value     | col1:col2:col3,11:12:13,21:22:23,31:32:33 |                         ||         |           | Matrix                    | Matrix                       |
