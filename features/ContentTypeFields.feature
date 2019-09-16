Feature: Content fields setting and editing
  As an administrator
  In order to manage content on my site
  I want to set, edit, copy and move content items.

  @common
  Scenario Outline: Create Content Types for other Scenarios to use
    Given a Content Type "<fieldName> CT" with an "<fieldInternalName>" field definition

    Examples:
      | fieldInternalName    | fieldName                    |
      | ezselection          | Selection                    |
      | ezgmaplocation       | Map location                 |
      | ezauthor             | Authors                      |
      | ezboolean            | Checkbox                     |
      | ezobjectrelation     | Content relation (single)    |
      | ezobjectrelationlist | Content relations (multiple) |
      | ezcountry            | Country                      |
      | ezdate               | Date                         |
      | ezdatetime           | Date and time                |
      | ezemail              | Email address                |
      | ezfloat              | Float                        |
      | ezisbn               | ISBN                         |
      | ezinteger            | Integer                      |
      | ezkeyword            | Keywords                     |
      | ezrichtext           | Rich text                    |
      | eztext               | Text block                   |
      | ezstring             | Text line                    |
      | eztime               | Time                         |
      | ezurl                | URL                          |
      | ezmedia              | Media                        |
      | ezimage              | Image                        |
      | ezbinaryfile         | File                         |

  @common
  Scenario: Regenrate GraphQL schema
    Given I regenerate GraphQL schema

  @javascript @common
  Scenario Outline: Create content item with given field
      Given I am logged as "admin"
      And I go to "Content structure" in "Content" tab
    When I start creating a new content "<fieldName> CT"
      And I set content fields
        | label    | <label1> | <label2> | <label3> |
        | Field    | <value1> | <value2> | <value3> |
      And I click on the edit action bar button "Publish"
    Then success notification that "Content published." appears
      And I should be on content item page "<contentItemName>" of type "<fieldName> CT" in root path
      And content attributes equal
        | label    | <label1> | <label2> | <label3> |
        | Field    | <value1> | <value2> | <value3> |

    Examples:
      | fieldName                    | label1    | value1                    | label2     | value2                | label3  | value3   | contentItemName           |
      | Selection                    | value     | Test-value                |            |                       |         |          | Test-value                |
      | Map location                 | latitude  | 32                        | longitude  | 132                   | address | Acapulco | Acapulco                  |
      | Authors                      | name      | Test Name                 | email      | email@example.com     |         |          | Test Name                 |
      | Checkbox                     | value     | true                      |            |                       |         |          | 1                         |
      | Content relation (single)    | value     | Media/Images              |            |                       |         |          | Images                    |
      | Content relations (multiple) | firstItem | Media/Images              | secondItem | Media/Files           |         |          | Images Files              |
      | Country                      | value     | Poland                    |            |                       |         |          | Poland                    |
      | Date                         | value     | 11/23/2019                |            |                       |         |          | Saturday 23 November 2019 |
      | Date and time                | date      | 11/23/2019                | time       | 14:45                 |         |          | Sat 2019-23-11 14:45:00   |
      | Email address                | value     | email@example.com         |            |                       |         |          | email@example.com         |
      | Float                        | value     | 11.11                     |            |                       |         |          | 11.11                     |
      | ISBN                         | value     | 978-3-16-148410-0         |            |                       |         |          | 978-3-16-148410-0         |
      | Integer                      | value     | 1111                      |            |                       |         |          | 1111                      |
      | Keywords                     | value     | first keyword, second     |            |                       |         |          | first keyword, second     |
      | Rich text                    | value     | Lorem ipsum dolor sit     |            |                       |         |          | Lorem ipsum dolor sit     |
      | Text block                   | value     | Lorem ipsum dolor         |            |                       |         |          | Lorem ipsum dolor         |
      | Text line                    | value     | Lorem ipsum               |            |                       |         |          | Lorem ipsum               |
      | Time                         | value     | 14:45                     |            |                       |         |          | 2:45:00 pm                |
      | URL                          | text      | Test URL                  | url        | http://www.google.com |         |          | Test URL                  |
      | Media                        | value     | video1.mp4.zip            |            |                       |         |          | video1.mp4                |
      | Image                        | value     | image1.png.zip            |            |                       |         |          | image1.png                |
      | File                         | value     | binary1.txt.zip           |            |                       |         |          | binary1.txt               |

  @javascript @common
  Scenario Outline: Edit content item with given field
    Given I am logged as "admin"
      And I navigate to content "<oldContentItemName>" of type "<fieldName> CT" in root path
    When I click on the edit action bar button "Edit"
      And I set content fields
        | label    | <label1> | <label2> | <label3> |
        | Field    | <value1> | <value2> | <value3> |
      And I click on the edit action bar button "Publish"
    Then success notification that "Content published." appears
      And I should be on content item page "<newContentItemName>" of type "<fieldName> CT" in root path
      And content attributes equal
        | label    | <label1> | <label2> | <label3> |
        | Field    | <value1> | <value2> | <value3> |

    Examples:
      | fieldName                    | label1    | value1                       | label2     | value2                   | label3  | value3   | oldContentItemName        | newContentItemName           |
      | Selection                    | value     | Bielefeld                    |            |                          |         |          | Test-value                | Bielefeld                    |
      | Map location                 | latitude  | 56                           | longitude  | 101                      | address | Ohio     | Acapulco                  | Ohio                         |
      | Authors                      | name      | Test Name Edited             | email      | edited.email@example.com |         |          | Test Name                 | Test Name Edited             |
      | Checkbox                     | value     | false                        |            |                          |         |          | 1                         | 0                            |
      | Content relation (single)    | value     | Media/Files                  |            |                          |         |          | Images                    | Files                        |
      | Content relations (multiple) | firstItem | Users/Editors                | secondItem | Media/Multimedia         |         |          | Images Files              | Editors Multimedia           |
      | Country                      | value     | Sweden                       |            |                          |         |          | Poland                    | Sweden                       |
      | Date                         | value     | 12/30/2019                   |            |                          |         |          | Saturday 23 November 2019 | Monday 30 December 2019      |
      | Date and time                | date      | 12/30/2019                   | time       | 15:15                    |         |          | Sat 2019-23-11 14:45:00   | Mon 2019-30-12 15:15:00      |
      | Email address                | value     | edited.email@example.com     |            |                          |         |          | email@example.com         | edited.email@example.com     |
      | Float                        | value     | 12.34                        |            |                          |         |          | 11.11                     | 12.34                        |
      | ISBN                         | value     | 0-13-048257-9                |            |                          |         |          | 978-3-16-148410-0         | 0-13-048257-9                |
      | Integer                      | value     | 1234                         |            |                          |         |          | 1111                      | 1234                         |
      | Keywords                     | value     | first keyword, second, edit  |            |                          |         |          | first keyword, second     | first keyword, second, edit  |
      | Rich text                    | value     | Edited Lorem ipsum dolor sit |            |                          |         |          | Lorem ipsum dolor sit     | Edited Lorem ipsum dolor sit |
      | Text block                   | value     | Edited Lorem ipsum dolor     |            |                          |         |          | Lorem ipsum dolor         | Edited Lorem ipsum dolor     |
      | Text line                    | value     | Edited Lorem ipsum           |            |                          |         |          | Lorem ipsum               | Edited Lorem ipsum           |
      | Time                         | value     | 15:15                        |            |                          |         |          | 2:45:00 pm                | 3:15:00 pm                   |
      | URL                          | text      | Edited Test URL              | url        | http://www.ez.no         |         |          | Test URL                  | Edited Test URL              |
      | Media                        | value     | video2.mp4.zip               |            |                          |         |          | video1.mp4                | video2.mp4                   |
      | Image                        | value     | image2.png.zip               |            |                          |         |          | image1.png                | image2.png                   |
      | File                         | value     | binary2.txt.zip              |            |                          |         |          | binary1.txt               | binary2.txt                  |
