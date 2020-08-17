Feature: Trash management
  As an administrator
  In order to manage content to my site
  I want to empty trash, delete, restore and restore element under new parent location in trash.

Background:
  Given I am logged as "admin"
    And I go to "Content structure" in "Content" tab

@javascript @common
Scenario Outline: Content can be moved to trash
  Given I start creating a new content "Folder"
    And I set content fields
      | label | value         |
      | Name  | <contentName> |
    And I click on the edit action bar button "Publish"
    And success notification that "Content published." appears
    And I should be on content container page "<contentName>" of type "Folder" in root path
  When I send content to trash
  Then I should be redirected to root in default view
    And going to trash there is "Folder" "<contentName>" on list

  Examples:
    | contentName |
    | Folder1     |
    | Folder2     |
    | Folder3     |
    | Folder4     |

@javascript @common
Scenario: Element in trash can be deleted
  Given I click on the left menu bar button "Trash"
    And there is "Folder" "Folder1" on trash list
  When I delete item from trash list
    | item       |
    | Folder1    |
  Then success notification that "Deleted selected item(s) from Trash." appears
    And there is no "Folder" "Folder1" on trash list

@javascript @common
Scenario: Element in trash can be restored
  Given I click on the left menu bar button "Trash"
    And there is "Folder" "Folder2" on trash list
  When I restore item from trash
    | item       |
    | Folder2    |
  Then success notification that "Restored content to its original Location." appears
    And there is no "Folder" "Folder2" on trash list
    And going to root path there is "Folder2" "Folder" on Sub-items list

@javascript @common
Scenario: Element in trash can be restored under new location
  Given I click on the left menu bar button "Trash"
    And there is "Folder" "Folder3" on trash list
  When I restore item from trash under new location "Media/Files"
    | item       |
    | Folder3    |
  Then success notification that "Restored content under Location 'Files'." appears
    And there is no "Folder" "Folder3" on trash list
    And going to "Media/Files" there is a "Folder3" "Folder" on Sub-items list

@javascript @common @admin
Scenario: Content can be moved to trash from non-root location
  Given I create "folder" Content items in "/Media/Files/" in "eng-GB"
      | name               | short_name         |
      | TestFolderToRemove | TestFolderToRemove |
    And I navigate to content "TestFolderToRemove" of type "Folder" in "Media/Files"
  When I send content to trash
  Then there's no "Folder" "TestFolderToRemove" on "Files" Sub-items list
    And going to trash there is "Folder" "TestFolderToRemove" on list
