Feature: Content fields setting and editing
  As an administrator
  In order to manage content on my site
  I want to set, edit, copy and move content items.

  @javascript @common @EZP-29291-excluded @fieldTypes @testRun
  Scenario Outline: Create content item with given field
    Given a Content Type "<fieldName> CT" with an "<fieldInternalName>" field definition
      And I am logged as "admin"
      And I go to "Content structure" in "Content" tab
    When I start creating a new content "<fieldName> CT"
      And I set content fields
        | label    | <label1> | <label2> | <label3> |
        | Field    | <value1> | <value2> | <value3> |
      And I click on the edit action bar button "Publish"
    Then I should be on content item page "<contentItemName>" of type "<fieldName> CT" in root path
      And success notification that "Content published." appears
      And content attributes equal
        | label    | <label1> | <label2> | <label3> |
        | Field    | <value1> | <value2> | <value3> |

    Examples:
      | fieldInternalName    | fieldName                    | label1    | value1                    | label2     | value2                | label3  | value3   | contentItemName           |
      | ezselection          | Selection                    | value     | Test-value                |            |                       |         |          | Test-value                |
      | ezgmaplocation       | Map location                 | latitude  | 32                        | longitude  | 132                   | address | Acapulco | Acapulco                  |
      | ezauthor             | Authors                      | name      | Test Name                 | email      | email@example.com     |         |          | Test Name                 |
      | ezboolean            | Checkbox                     | value     | true                      |            |                       |         |          | 1                         |
      | ezobjectrelation     | Content relation (single)    | value     | Media/Images              |            |                       |         |          | Images                    |
      | ezobjectrelationlist | Content relations (multiple) | firstItem | Media/Images              | secondItem | Media/Files           |         |          | Images Files              |
      | ezcountry            | Country                      | value     | Poland                    |            |                       |         |          | Poland                    |
      | ezdate               | Date                         | value     | 11/23/2019                |            |                       |         |          | Saturday 23 November 2019 |
      | ezdatetime           | Date and time                | date      | 11/23/2019                | time       | 14:45                 |         |          | Sat 2019-23-11 14:45:00   |
      | ezemail              | Email address                | value     | email@example.com         |            |                       |         |          | email@example.com         |
      | ezfloat              | Float                        | value     | 11.11                     |            |                       |         |          | 11.11                     |
      | ezisbn               | ISBN                         | value     | 978-3-16-148410-0         |            |                       |         |          | 978-3-16-148410-0         |
      | ezinteger            | Integer                      | value     | 1111                      |            |                       |         |          | 1111                      |
      | ezkeyword            | Keywords                     | value     | first keyword, second     |            |                       |         |          | first keyword, second     |
      | ezrichtext           | Rich text                    | value     | Lorem ipsum dolor sit     |            |                       |         |          | Lorem ipsum dolor sit     |
      | eztext               | Text block                   | value     | Lorem ipsum dolor         |            |                       |         |          | Lorem ipsum dolor         |
      | ezstring             | Text line                    | value     | Lorem ipsum               |            |                       |         |          | Lorem ipsum               |
      | eztime               | Time                         | value     | 14:45                     |            |                       |         |          | 2:45:00 pm                |
      | ezurl                | URL                          | text      | Test URL                  | url        | http://www.google.com |         |          | Test URL                  |
      | ezmedia              | Media                        | value     | video1.mp4.zip            |            |                       |         |          | video1.mp4                |
      | ezimage              | Image                        | value     | image1.png.zip            |            |                       |         |          | image1.png                |
      | ezbinaryfile         | File                         | value     | binary1.txt.zip           |            |                       |         |          | binary1.txt               |
      | ezdatetime           | Date and time                | date      | 11/23/2019                | time       | 10:45                 |         |          | Sat 2019-23-11 10:45:00   |
      | ezdatetime           | Date and time                | date      | 11/23/2019                | time       | 11:45                 |         |          | Sat 2019-23-11 11:45:00   |
      | ezdatetime           | Date and time                | date      | 11/23/2019                | time       | 12:45                 |         |          | Sat 2019-23-11 12:45:00   |
      | ezdatetime           | Date and time                | date      | 11/23/2019                | time       | 13:45                 |         |          | Sat 2019-23-11 13:45:00   |
      | ezdatetime           | Date and time                | date      | 11/23/2019                | time       | 14:45                 |         |          | Sat 2019-23-11 14:45:00   |
      | ezdatetime           | Date and time                | date      | 11/23/2019                | time       | 15:45                 |         |          | Sat 2019-23-11 15:45:00   |
      | ezdatetime           | Date and time                | date      | 11/23/2019                | time       | 16:45                 |         |          | Sat 2019-23-11 16:45:00   |
      | ezdatetime           | Date and time                | date      | 11/23/2019                | time       | 17:45                 |         |          | Sat 2019-23-11 17:45:00   |
      | ezdatetime           | Date and time                | date      | 11/23/2019                | time       | 18:45                 |         |          | Sat 2019-23-11 18:45:00   |
      | ezdatetime           | Date and time                | date      | 11/23/2019                | time       | 19:45                 |         |          | Sat 2019-23-11 19:45:00   |
      | ezdatetime           | Date and time                | date      | 11/23/2019                | time       | 20:45                 |         |          | Sat 2019-23-11 20:45:00   |
      | ezdatetime           | Date and time                | date      | 11/23/2019                | time       | 21:45                 |         |          | Sat 2019-23-11 21:45:00   |
      | ezdatetime           | Date and time                | date      | 11/23/2019                | time       | 22:45                 |         |          | Sat 2019-23-11 22:45:00   |
      | ezdatetime           | Date and time                | date      | 11/23/2019                | time       | 23:45                 |         |          | Sat 2019-23-11 23:45:00   |
      | ezdatetime           | Date and time                | date      | 11/23/2019                | time       | 10:45                 |         |          | Sat 2019-23-11 10:45:00   |
      | ezdatetime           | Date and time                | date      | 11/23/2019                | time       | 11:45                 |         |          | Sat 2019-23-11 11:45:00   |
      | ezdatetime           | Date and time                | date      | 11/23/2019                | time       | 12:45                 |         |          | Sat 2019-23-11 12:45:00   |
      | ezdatetime           | Date and time                | date      | 11/23/2019                | time       | 13:45                 |         |          | Sat 2019-23-11 13:45:00   |
      | ezdatetime           | Date and time                | date      | 11/23/2019                | time       | 14:45                 |         |          | Sat 2019-23-11 14:45:00   |
      | ezdatetime           | Date and time                | date      | 11/23/2019                | time       | 15:45                 |         |          | Sat 2019-23-11 15:45:00   |
      | ezdatetime           | Date and time                | date      | 11/23/2019                | time       | 16:45                 |         |          | Sat 2019-23-11 16:45:00   |
      | ezdatetime           | Date and time                | date      | 11/23/2019                | time       | 17:45                 |         |          | Sat 2019-23-11 17:45:00   |
      | ezdatetime           | Date and time                | date      | 11/23/2019                | time       | 18:45                 |         |          | Sat 2019-23-11 18:45:00   |
      | ezdatetime           | Date and time                | date      | 11/23/2019                | time       | 19:45                 |         |          | Sat 2019-23-11 19:45:00   |
      | ezdatetime           | Date and time                | date      | 11/23/2019                | time       | 20:45                 |         |          | Sat 2019-23-11 20:45:00   |
      | ezdatetime           | Date and time                | date      | 11/23/2019                | time       | 21:45                 |         |          | Sat 2019-23-11 21:45:00   |
      | ezdatetime           | Date and time                | date      | 11/23/2019                | time       | 22:45                 |         |          | Sat 2019-23-11 22:45:00   |
      | ezdatetime           | Date and time                | date      | 11/23/2019                | time       | 23:45                 |         |          | Sat 2019-23-11 23:45:00   |
      | eztime               | Time                         | value     | 10:45                     |            |                       |         |          | 10:45:00 am               |
      | eztime               | Time                         | value     | 11:45                     |            |                       |         |          | 11:45:00 am               |
      | eztime               | Time                         | value     | 12:45                     |            |                       |         |          | 12:45:00 pm               |
      | eztime               | Time                         | value     | 13:45                     |            |                       |         |          | 1:45:00 pm                |
      | eztime               | Time                         | value     | 14:45                     |            |                       |         |          | 2:45:00 pm                |
      | eztime               | Time                         | value     | 15:45                     |            |                       |         |          | 3:45:00 pm                |
      | eztime               | Time                         | value     | 16:45                     |            |                       |         |          | 4:45:00 pm                |
      | eztime               | Time                         | value     | 17:45                     |            |                       |         |          | 5:45:00 pm                |
      | eztime               | Time                         | value     | 18:45                     |            |                       |         |          | 6:45:00 pm                |
      | eztime               | Time                         | value     | 19:45                     |            |                       |         |          | 7:45:00 pm                |
      | eztime               | Time                         | value     | 20:45                     |            |                       |         |          | 8:45:00 pm                |
      | eztime               | Time                         | value     | 21:45                     |            |                       |         |          | 9:45:00 pm                |
      | eztime               | Time                         | value     | 22:45                     |            |                       |         |          | 10:45:00 pm               |
      | eztime               | Time                         | value     | 23:45                     |            |                       |         |          | 11:45:00 pm               |
      | eztime               | Time                         | value     | 10:45                     |            |                       |         |          | 10:45:00 am               |
      | eztime               | Time                         | value     | 11:45                     |            |                       |         |          | 11:45:00 am               |
      | eztime               | Time                         | value     | 12:45                     |            |                       |         |          | 12:45:00 pm               |
      | eztime               | Time                         | value     | 13:45                     |            |                       |         |          | 1:45:00 pm                |
      | eztime               | Time                         | value     | 14:45                     |            |                       |         |          | 2:45:00 pm                |
      | eztime               | Time                         | value     | 15:45                     |            |                       |         |          | 3:45:00 pm                |
      | eztime               | Time                         | value     | 16:45                     |            |                       |         |          | 4:45:00 pm                |
      | eztime               | Time                         | value     | 17:45                     |            |                       |         |          | 5:45:00 pm                |
      | eztime               | Time                         | value     | 18:45                     |            |                       |         |          | 6:45:00 pm                |
      | eztime               | Time                         | value     | 19:45                     |            |                       |         |          | 7:45:00 pm                |
      | eztime               | Time                         | value     | 20:45                     |            |                       |         |          | 8:45:00 pm                |
      | eztime               | Time                         | value     | 21:45                     |            |                       |         |          | 9:45:00 pm                |
      | eztime               | Time                         | value     | 22:45                     |            |                       |         |          | 10:45:00 pm               |
      | eztime               | Time                         | value     | 23:45                     |            |                       |         |          | 11:45:00 pm               |

  @javascript @common @EZP-29291-excluded @fieldTypes
  Scenario Outline: Edit content item with given field
    Given I am logged as "admin"
      And I navigate to content "<oldContentItemName>" of type "<fieldName> CT" in root path
    When I click on the edit action bar button "Edit"
      And I set content fields
        | label    | <label1> | <label2> | <label3> |
        | Field    | <value1> | <value2> | <value3> |
      And I click on the edit action bar button "Publish"
    Then I should be on content item page "<newContentItemName>" of type "<fieldName> CT" in root path
      And success notification that "Content published." appears
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
      | Email address               | value     | edited.email@example.com     |            |                          |         |          | email@example.com         | edited.email@example.com     |
      | Float                        | value     | 12.34                        |            |                          |         |          | 11.11                     | 12.34                        |
      | ISBN                         | value     | 0-13-048257-9                |            |                          |         |          | 978-3-16-148410-0         | 0-13-048257-9                |
      | Integer                      | value     | 1234                         |            |                          |         |          | 1111                      | 1234                         |
      | Keywords                     | value     | first keyword, second, edit  |            |                          |         |          | first keyword, second     | first keyword, second, edit  |
      | Rich text                    | value     | Edited Lorem ipsum dolor sit |            |                          |         |          | Lorem ipsum dolor sit     | Edited Lorem ipsum dolor sit |
      | Text block                   | value     | Edited Lorem ipsum dolor     |            |                          |         |          | Lorem ipsum dolor         | Edited Lorem ipsum dolor     |
      | Text line                    | value     | Edited Lorem ipsum           |            |                          |         |          | Lorem ipsum               | Edited Lorem ipsum           |
      | Time                         | value     | 15:15                        |            |                          |         |          | 2:45:00 pm                | 3:15:00 pm                   |
      | URL                          | text      | Edited Test URL              | url        | http://www.ez.no         |         |          | Test URL                  | Edited Test URL              |
      | Media                        | value     | video2.mp4.zip                   |            |                          |         |          | video1.mp4                | video2.mp4                   |
      | Image                        | value     | image2.png.zip                   |            |                          |         |          | image1.png                | image2.png                   |
      | File                         | value     | binary2.txt.zip                  |            |                          |         |          | binary1.txt               | binary2.txt                  |
