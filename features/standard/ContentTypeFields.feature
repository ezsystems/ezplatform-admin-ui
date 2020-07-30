Feature: Content fields setting and editing
  As an administrator
  In order to manage content on my site
  I want to set, edit, copy and move content items.

  @javascript @common @admin @test4
  Scenario Outline: Create content item with given field
    Given I create a "<fieldName> CT" Content Type in "Content" with "<fieldInternalName>" identifier
      | Field Type  | Name        | Identifier          | Required | Searchable | Translatable | Settings       |
      | <fieldName> | Field       | <fieldInternalName> | no      | no	      | yes          | <fieldSettings>  |
      | Text line   | Name        | name	            | no      | yes	      | yes          |                  |
    And I am logged as "admin"
      And I go to "Content structure" in "Content" tab
    When I start creating a new content "<fieldName> CT"
      And I set content fields
        | label    | <label1>    | <label2> | <label3> |
        | Field    | <value1>    | <value2> | <value3> |
        | Name     | <fieldName> |          |          |
      And I click on the edit action bar button "Publish"
    Then success notification that "Content published." appears
      And I should be on content item page "<contentItemName>" of type "<fieldName> CT" in root path
      And content attributes equal
        | label    | <label1> | <label2> | <label3> |
        | Field    | <value1> | <value2> | <value3> |

    Examples:
      | fieldInternalName    | fieldName                    | fieldSettings                                                         |  label1   | value1                                                                    | label2     | value2                | label3  | value3      | contentItemName           |
      | ezselection          | Selection                    | is_multiple:false,options:A first-Bielefeld-TestValue-Turtles-Zombies | value     | TestValue                                                                 |            |                       |         |             | TestValue                 |
      | ezgmaplocation       | Map location                 |                                                                       | latitude  | 34.1                                                                      | longitude  | -118.2                | address | Los Angeles | Los Angeles            |
      | ezauthor             | Authors                      |                                                                       | name      | Test Name                                                                 | email      | email@example.com     |         |             | Test Name                 |
      | ezboolean            | Checkbox                     |                                                                       | value     | true                                                                      |            |                       |         |             | 1                         |
      | ezobjectrelation     | Content relation (single)    |                                                                       | value     | Media/Images                                                              |            |                       |         |             | Images                    |
      | ezobjectrelationlist | Content relations (multiple) |                                                                       | firstItem | Media/Images                                                              | secondItem | Media/Files           |         |             | Images Files              |
      | ezcountry            | Country                      |                                                                       | value     | Poland                                                                    |            |                       |         |             | Poland                    |
      | ezdate               | Date                         |                                                                       | value     | 11/23/2019                                                                |            |                       |         |             | Saturday 23 November 2019 |
      | ezdatetime           | Date and time                |                                                                       | date      | 11/23/2019                                                                | time       | 14:45                 |         |             | Sat 2019-23-11 14:45:00   |
      | ezemail              | Email address                |                                                                       | value     | email@example.com                                                         |            |                       |         |             | email@example.com         |
      | ezfloat              | Float                        |                                                                       | value     | 11.11                                                                     |            |                       |         |             | 11.11                     |
      | ezisbn               | ISBN                         |                                                                       | value     | 978-3-16-148410-0                                                         |            |                       |         |             | 978-3-16-148410-0         |
      | ezinteger            | Integer                      |                                                                       | value     | 1111                                                                      |            |                       |         |             | 1111                      |
      | ezkeyword            | Keywords                     |                                                                       | value     | first keyword, second                                                     |            |                       |         |             | first keyword, second     |
      | ezrichtext           | Rich text                    |                                                                       | value     | Lorem ipsum dolor sit                                                     |            |                       |         |             | Lorem ipsum dolor sit     |
      | eztext               | Text block                   |                                                                       | value     | Lorem ipsum dolor                                                         |            |                       |         |             | Lorem ipsum dolor         |
      | ezstring             | Text line                    |                                                                       | value     | Lorem ipsum                                                               |            |                       |         |             | Lorem ipsum               |
      | eztime               | Time                         |                                                                       | value     | 14:45                                                                     |            |                       |         |             | 2:45:00 pm                |
      | ezurl                | URL                          |                                                                       | text      | Test URL                                                                  | url        | http://www.google.com |         |             | Test URL                  |
      | ezmedia              | Media                        |                                                                       | value     | video1.mp4.zip                                                            |            |                       |         |             | video1.mp4                |
      | ezimage              | Image                        |                                                                       | value     | image1.png.zip                                                            |            |                       |         |             | image1.png                |
      | ezbinaryfile         | File                         |                                                                       | value     | binary1.txt.zip                                                           |            |                       |         |             | binary1.txt               |
      | ezmatrix             | Matrix                       | Min_rows:2,Columns:col1-col2-col3                                     | value     | col1:col2:col3,Ala:miała:kota,Szpak:dziobał:bociana,Bociana:dziobał:szpak |            |                       |         |             | Matrix                    |
      | ezimageasset         | Image Asset                  |                                                                       | value     |  imageasset1.png.zip                                                      |            |                       |         |             | imageasset1.png           |

  @javascript @common @admin
  Scenario: Create an ImageAsset Content item and edit specified field
    Given I create "Image" Content items in "/Media/Images/" in "eng-GB"
      | name             | image                                                        |
      | ImageAssetImage  | vendor/ezsystems/behatbundle/src/lib/Data/Images/small2.jpg  |
      And I create a 'Image Asset CT2' Content Type in "Content" with 'ImageAssetCT2' identifier
      | Field Type  | Name         | Identifier        | Required | Searchable | Translatable | Settings        |
      | Image Asset | ImageAField  | imageafield       | yes      | no	       | yes          |                 |
      And I am logged as "admin"
      And I go to "Content structure" in "Content" tab
    When I start creating a new content 'Image Asset CT2'
      And I select "Media/Images/ImageAssetImage" from Image Asset Repository for "ImageAField" field
      And I click on the edit action bar button "Publish"
    Then success notification that "Content published." appears
      And I should be on content item page "ImageAssetImage" of type "Image Asset CT2" in root path
      And content attributes equal
      | label          | value      |
      | ImageAField    | small2.jpg |

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
      | fieldName                    | label1    | value1                       | label2     | value2                   | label3  | value3    | oldContentItemName        | newContentItemName           |
      | Selection                    | value     | Bielefeld                    |            |                          |         |           | TestValue                 | Bielefeld                    |
      | Map location                 | latitude  | -37.8                        | longitude  | 145.0                    | address | Melbourne | Los Angeles               | Melbourne                    |
      | Authors                      | name      | Test Name Edited             | email      | edited.email@example.com |         |           | Test Name                 | Test Name Edited             |
      | Checkbox                     | value     | false                        |            |                          |         |           | 1                         | 0                            |
      | Content relation (single)    | value     | Media/Files                  |            |                          |         |           | Images                    | Files                        |
      | Content relations (multiple) | firstItem | Users/Editors                | secondItem | Media/Multimedia         |         |           | Images Files              | Editors Multimedia           |
      | Country                      | value     | Sweden                       |            |                          |         |           | Poland                    | Sweden                       |
      | Date                         | value     | 12/30/2019                   |            |                          |         |           | Saturday 23 November 2019 | Monday 30 December 2019      |
      | Date and time                | date      | 12/30/2019                   | time       | 15:15                    |         |           | Sat 2019-23-11 14:45:00   | Mon 2019-30-12 15:15:00      |
      | Email address                | value     | edited.email@example.com     |            |                          |         |           | email@example.com         | edited.email@example.com     |
      | Float                        | value     | 12.34                        |            |                          |         |           | 11.11                     | 12.34                        |
      | ISBN                         | value     | 0-13-048257-9                |            |                          |         |           | 978-3-16-148410-0         | 0-13-048257-9                |
      | Integer                      | value     | 1234                         |            |                          |         |           | 1111                      | 1234                         |
      | Keywords                     | value     | first keyword, second, edit  |            |                          |         |           | first keyword, second     | first keyword, second, edit  |
      | Rich text                    | value     | Edited Lorem ipsum dolor sit |            |                          |         |           | Lorem ipsum dolor sit     | Edited Lorem ipsum dolor sit |
      | Text block                   | value     | Edited Lorem ipsum dolor     |            |                          |         |           | Lorem ipsum dolor         | Edited Lorem ipsum dolor     |
      | Text line                    | value     | Edited Lorem ipsum           |            |                          |         |           | Lorem ipsum               | Edited Lorem ipsum           |
      | Time                         | value     | 15:15                        |            |                          |         |           | 2:45:00 pm                | 3:15:00 pm                   |
      | URL                          | text      | Edited Test URL              | url        | http://www.ez.no         |         |           | Test URL                  | Edited Test URL              |
      | Media                        | value     | video2.mp4.zip               |            |                          |         |           | video1.mp4                | video2.mp4                   |
      | Image                        | value     | image2.png.zip               |            |                          |         |           | image1.png                | image2.png                   |
      | File                         | value     | binary2.txt.zip              |            |                          |         |           | binary1.txt               | binary2.txt                  |
      | Matrix                       | value     | col1:col2:col3,11:12:13,21:22:23,31:32:33 |                         ||         |           | Matrix                    | Matrix                       |
      | Image Asset                  | value     | imageasset2.png.zip          |            |                          |         |           | imageasset1.png           | imageasset2.png              |

  @javascript @common @admin @queryFieldType @commerceExcluded
  Scenario Outline: Create content item with Content Query field
    Given I create a "<fieldName> CT" Content Type in "Content" with "<fieldInternalName>" identifier
      | Field Type  | Name        | Identifier          | Required | Searchable | Translatable | Settings        |
      | <fieldName> | Field       | <fieldInternalName> | no       | no	        | yes          | <fieldSettings> |
      | Text line   | Name        | name	            | no       | yes	    | yes          |                 |
    And I am logged as "admin"
    And I go to "Content structure" in "Content" tab
    When I start creating a new content "<fieldName> CT"
    And the "Ezcontentquery" field is noneditable
    And I set content fields
      | label    | <label1>    |
      | Name     | <fieldName> |
    And I click on the edit action bar button "Publish"
    Then success notification that "Content published." appears
    And I should be on content item page "<fieldName>" of type "<fieldName> CT" in root path
    And content attributes equal
      | label    | <label1> | fieldType   |
      | Field    | <value1> | <fieldName> |
    Examples:
      | fieldInternalName | fieldName     | fieldSettings                                               | label1 | value1                 |
      | ezcontentquery    | Content query | QueryType-EzPlatformAdminUi:MediaSubtree,ContentType-folder | value  | Media,Files,Multimedia |

  @javascript @common @queryFieldType @commerceExcluded
  Scenario: Edit content item with Content Query
    Given I am logged as "admin"
    And I navigate to content "Content query" of type "Content query CT" in root path
    When I click on the edit action bar button "Edit"
    And I set content fields
      | label    | <label1>          |
      | Name     | New Content query |
    And I click on the edit action bar button "Publish"
    Then success notification that "Content published." appears
    And I should be on content item page "New Content query" of type "Content query CT" in root path
    And content attributes equal
      | label    | value                  | fieldType     |
      | Field    | Media,Files,Multimedia | Content query |