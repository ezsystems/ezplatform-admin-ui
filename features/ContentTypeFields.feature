Feature: Content fields setting and editing
  As an administrator
  In order to manage content on my site
  I want to set, edit, copy and move content items.

  @javascript @common @EZP-29291-excluded
  Scenario Outline: Create content item with given field
    Given a Content Type "<fieldName> CT" with an "<fieldInternalName>" field definition
      And I am logged as "admin"
      And I go to "Content structure" in "Content" tab
    When I start creating a new content "<fieldName> CT"
      And I set content fields
        | label    | <label1> | <label2> | <label3> |
        | Field    | <value1> | <value2> | <value3> |
      And I click on the edit action bar button "Publish"
    Then I should be on content item page "<ctName>" of type "<fieldName> CT" in "Home"
      And success notification that "Content published." appears
      And content attributes equal
        | label    | <label1> | <label2> | <label3> |
        | Field    | <value1> | <value2> | <value3> |

    Examples:
      | fieldInternalName    | fieldName                    | label1    | value1                | label2     | value2                | label3  | value3   | ctName                  |
      | ezselection          | Selection                    | value     | Test-value            |            |                       |         |          | Test-value              |
      | ezgmaplocation       | Map location                 | latitude  | 32                    | longitude  | 132                   | address | Acapulco | Acapulco                |
      | ezauthor             | Authors                      | name      | Test Name             | email      | email@example.com     |         |          | Test Name               |
      | ezboolean            | Checkbox                     | value     | true                  |            |                       |         |          | 1                       |
      | ezobjectrelation     | Content relation (single)    | value     | Media/Images          |            |                       |         |          | Images                  |
      | ezobjectrelationlist | Content relations (multiple) | firstItem | Media/Images          | secondItem | Media/Files           |         |          | Images Files            |
      | ezcountry            | Country                      | value     | Poland                |            |                       |         |          | Poland                  |
      | ezdate               | Date                         | value     | 12/30/2019            |            |                       |         |          | Monday 30 December 2019 |
      | ezdatetime           | Date and time                | date      | 12/30/2019            | time       | 13:15                 |         |          | Mon 2019-30-12 13:15:00 |
      | ezemail              | E-mail address               | value     | email@example.com     |            |                       |         |          | email@example.com       |
      | ezfloat              | Float                        | value     | 11.11                 |            |                       |         |          | 11.11                   |
      | ezisbn               | ISBN                         | value     | 978-3-16-148410-0     |            |                       |         |          | 978-3-16-148410-0       |
      | ezinteger            | Integer                      | value     | 1111                  |            |                       |         |          | 1111                    |
      | ezkeyword            | Keywords                     | value     | first keyword, second |            |                       |         |          | first keyword, second   |
      | ezrichtext           | Rich text                    | value     | Lorem ipsum dolor sit |            |                       |         |          | Lorem ipsum dolor sit   |
      | eztext               | Text block                   | value     | Lorem ipsum dolor sit |            |                       |         |          | Lorem ipsum dolor sit   |
      | ezstring             | Text line                    | value     | Lorem ipsum dolor sit |            |                       |         |          | Lorem ipsum dolor sit   |
      | eztime               | Time                         | value     | 13:15                 |            |                       |         |          | 1:15:00 pm              |
      | ezurl                | URL                          | text      | Test URL              | url        | http://www.google.com |         |          | Test URL                |
#      | ezmedia              | Media                        | value   | value         |        |                   |        |                   |
#      | ezimage              | Image                        | value   | value         |        |                   |        |                   |
#      | ezbinaryfile         | File                         | value   | value         |        |                   |        |                   |