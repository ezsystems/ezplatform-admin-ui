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
      | ezstring             | Text line                    |                                                                       | value     | Lorem ipsum                                                               |            |                       |         |             | Lorem ipsum               |
      | ezselection          | Selection                    | is_multiple:false,options:A first-Bielefeld-TestValue-Turtles-Zombies | value     | TestValue                                                                 |            |                       |         |             | TestValue                 |
      # | ezgmaplocation       | Map location                 |                                                                       | latitude  | 34.1                                                                      | longitude  | -118.2                | address | Los Angeles | Los Angeles               |
      | ezauthor             | Authors                      |                                                                       | name      | Test Name                                                                 | email      | email@example.com     |         |             | Test Name                 |
      | ezboolean            | Checkbox                     |                                                                       | value     | true                                                                      |            |                       |         |             | 1                         |
      | ezobjectrelation     | Content relation (single)    |                                                                       | value     | Media/Images                                                              |            |                       |         |             | Images                    |
      | ezobjectrelationlist | Content relations (multiple) |                                                                       | firstItem | Media/Images                                                              | secondItem | Media/Files           |         |             | Images Files              |
      | ezcountry            | Country                      |                                                                       | value     | Angola                                                                    |            |                       |         |             | Angola                    |
      | ezdate               | Date                         |                                                                       | value     | 11/23/2019                                                                |            |                       |         |             | Saturday 23 November 2019 |
      | ezdatetime           | Date and time                |                                                                       | date      | 11/23/2019                                                                | time       | 14:45                 |         |             | Sat 2019-23-11 14:45:00   |
      | ezemail              | Email address                |                                                                       | value     | email@example.com                                                         |            |                       |         |             | email@example.com         |
      | ezfloat              | Float                        |                                                                       | value     | 11.11                                                                     |            |                       |         |             | 11.11                     |
      | ezisbn               | ISBN                         |                                                                       | value     | 978-3-16-148410-0                                                         |            |                       |         |             | 978-3-16-148410-0         |
      | ezinteger            | Integer                      |                                                                       | value     | 1111                                                                      |            |                       |         |             | 1111                      |
      | ezkeyword            | Keywords                     |                                                                       | value     | keyword1                                                                  |            |                       |         |             | keyword1                  |
      | ezrichtext           | Rich text                    |                                                                       | value     | Lorem ipsum dolor sit                                                     |            |                       |         |             | Lorem ipsum dolor sit     |
      | eztext               | Text block                   |                                                                       | value     | Lorem ipsum dolor                                                         |            |                       |         |             | Lorem ipsum dolor         |
      | eztime               | Time                         |                                                                       | value     | 14:45                                                                     |            |                       |         |             | 2:45:00 pm                |
      | ezurl                | URL                          |                                                                       | text      | Test URL                                                                  | url        | http://www.google.com |         |             | Test URL                  |
      | ezmedia              | Media                        |                                                                       | value     | video1.mp4.zip                                                            |            |                       |         |             | video1.mp4                |
      | ezimage              | Image                        |                                                                       | value     | image1.png.zip                                                            |            |                       |         |             | image1.png                |
      | ezbinaryfile         | File                         |                                                                       | value     | binary1.txt.zip                                                           |            |                       |         |             | binary1.txt               |
      | ezmatrix             | Matrix                       | Min_rows:2,Columns:col1-col2-col3                                     | value     | col1:col2:col3,Ala:miała:kota,Szpak:dziobał:bociana,Bociana:dziobał:szpak |            |                       |         |             | Matrix                    |
      | ezimageasset         | Image Asset                  |                                                                       | value     |  imageasset1.png.zip                                                      |            |                       |         |             | imageasset1.png           |

  @javascript @APIUser:admin
  Scenario: Create an ImageAsset Content item and edit specified field
    Given I create "image" Content items in "/Media/Images/" in "eng-GB"
      | name             | image                                                        |
      | ImageAssetImage  | vendor/ezsystems/behatbundle/src/lib/Data/Images/small2.jpg  |
      And I create a 'Image Asset CT2' Content Type in "Content" with 'ImageAssetCT2' identifier
      | Field Type  | Name         | Identifier        | Required | Searchable | Translatable | Settings        |
      | Image Asset | ImageAField  | imageafield       | yes      | no	       | yes          |                 |
      And I am logged as admin
      And I'm on Content view Page for root
    When I start creating a new content 'Image Asset CT2'
      And I select "Media/Images/ImageAssetImage" from Image Asset Repository for "ImageAField" field
      And I click on the edit action bar button "Publish"
    Then success notification that "Content published." appears
      And I should be on Content view Page for "ImageAssetImage"
      And content attributes equal
      | label          | value      |
      | ImageAField    | small2.jpg |

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
      # | latitude  | -37.8                        | longitude  | 145.0                    | address | Melbourne | Los Angeles               | Melbourne                    |
      | name      | Test Name Edited             | email      | edited.email@example.com |         |           | Test Name                 | Test Name Edited             |
      | value     | false                        |            |                          |         |           | 1                         | 0                            |
      | value     | Media/Files                  |            |                          |         |           | Images                    | Files                        |
      | firstItem | Users/Editors                | secondItem | Media/Multimedia         |         |           | Images Files              | Editors Multimedia           |
      | value     | Albania                      |            |                          |         |           | Angola                    | Albania                      |
      | value     | 12/30/2019                   |            |                          |         |           | Saturday 23 November 2019 | Monday 30 December 2019      |
      | date      | 12/30/2019                   | time       | 15:15                    |         |           | Sat 2019-23-11 14:45:00   | Mon 2019-30-12 15:15:00      |
      | value     | edited.email@example.com     |            |                          |         |           | email@example.com         | edited.email@example.com     |
      | value     | 12.34                        |            |                          |         |           | 11.11                     | 12.34                        |
      | value     | 0-13-048257-9                |            |                          |         |           | 978-3-16-148410-0         | 0-13-048257-9                |
      | value     | 1234                         |            |                          |         |           | 1111                      | 1234                         |
      | value     | keyword2                     |            |                          |         |           | keyword1                  | keyword2                     |
      | value     | Edited Lorem ipsum dolor sit |            |                          |         |           | Lorem ipsum dolor sit     | Edited Lorem ipsum dolor sit |
      | value     | Edited Lorem ipsum dolor     |            |                          |         |           | Lorem ipsum dolor         | Edited Lorem ipsum dolor     |
      | value     | Edited Lorem ipsum           |            |                          |         |           | Lorem ipsum               | Edited Lorem ipsum           |
      | value     | 15:15                        |            |                          |         |           | 2:45:00 pm                | 3:15:00 pm                   |
      | text      | Edited Test URL              | url        | http://www.ez.no         |         |           | Test URL                  | Edited Test URL              |
      | value     | video2.mp4.zip               |            |                          |         |           | video1.mp4                | video2.mp4                   |
      | value     | image2.png.zip               |            |                          |         |           | image1.png                | image2.png                   |
      | value     | binary2.txt.zip              |            |                          |         |           | binary1.txt               | binary2.txt                  |
      | value     | col1:col2:col3,11:12:13,21:22:23,31:32:33 |                         ||         |           | Matrix                    | Matrix                       |
      | value     | imageasset2.png.zip          |            |                          |         |           | imageasset1.png           | imageasset2.png              |

  @javascript @APIUser:admin @contentQuery
  Scenario Outline: Create content item with Content Query field
    Given I create a "<fieldName> CT" Content Type in "Content" with "<fieldInternalName>" identifier
      | Field Type  | Name        | Identifier          | Required | Searchable | Translatable | Settings        |
      | <fieldName> | Field       | <fieldInternalName> | no       | no	        | yes          | <fieldSettings> |
      | Text line   | Name        | name	            | no       | yes	    | yes          |                 |
    Given I am logged as admin
    And I'm on Content view Page for root
    When I start creating a new content "<fieldName> CT"
    And the "Ezcontentquery" field is noneditable
    And I set content fields
      | label    | <label1>    |
      | Name     | <fieldName> |
    And I click on the edit action bar button "Publish"
    Then success notification that "Content published." appears
    And I should be on Content view Page for "<fieldName>"
    And content attributes equal
      | label    | <label1> | fieldTypeIdentifier   |
      | Field    | <value1> | <fieldInternalName> |
    Examples:
      | fieldInternalName | fieldName     | fieldSettings                                                                                                  | label1 | value1                  |
      | ezcontentquery    | Content query | QueryType-Folders under media,ContentType-folder,ItemsPerPage-100,Parameters-contentTypeId:folder;locationId:43| value  | Images,Files,Multimedia |

  @javascript @APIUser:admin @contentQuery
  Scenario: Edit content item with Content Query
    Given I am logged as admin
    And I'm on Content view Page for "Content query"
    When I click on the edit action bar button "Edit"
    And I set content fields
      | label    | <label1>          |
      | Name     | New Content query |
    And I click on the edit action bar button "Publish"
    Then success notification that "Content published." appears
    And I should be on Content view Page for "New Content query"
    And content attributes equal
      | label    | value                   | fieldTypeIdentifier |
      | Field    | Images,Files,Multimedia | ezcontentquery      |
