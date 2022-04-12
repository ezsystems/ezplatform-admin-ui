@IbexaOSS @IbexaContent @IbexaExperience @IbexaCommerce @javascript
Feature: Bookmarks management

  Background:
    Given I am logged as admin

  @APIUser:admin
  Scenario: Content Item can be added to bookmarks
    Given I create "folder" Content items
      | name            | short_name       | parentPath        | language |
      | BookmarkFolder  | BookmarkFolder   | root              | eng-GB   |
    And I'm on Content view Page for "root/BookmarkFolder"
    When I bookmark the Content Item "root/BookmarkFolder"
    Then it is marked as bookmarked

  @APIUser:admin
  Scenario: Content Item can be bookmarked from UDW
    Given I create "folder" Content items
      | name         | short_name    | parentPath        | language |
      | BookmarkUDW  | BookmarkUDW   | root              | eng-GB   |
    And I'm on Content view Page for "root"
    And I click on the left menu bar button "Browse"
    When I select content "root/BookmarkUDW" through UDW
    And I bookmark the Content Item "root/BookmarkUDW" in Universal Discovery Widget
    Then it is marked as bookmarked in Universal Discovery Widget

  Scenario: Bookmarks can be displayed
    Given I open "Bookmarks" page in admin SiteAccess
    Then there's a "BookmarkFolder" Content Item on Bookmarks list
    And there's a "BookmarkUDW" Content Item on Bookmarks list

  Scenario: Content Item can be previewed from Bookmarks page
    Given I open "Bookmarks" page in admin SiteAccess
    And there's a "BookmarkFolder" Content Item on Bookmarks list
    When I go to "BookmarkFolder" Content Item from Bookmarks
    Then I should be on Content view Page for "root/BookmarkFolder"

  Scenario: Content Item can be edited
    Given I open "Bookmarks" page in admin SiteAccess
    And there's a "BookmarkFolder" Content Item on Bookmarks list
    When I start editing "BookmarkFolder" Content Item from Bookmarks
    Then I should be on Content update page for "BookmarkFolder"

  Scenario: Bookmark can be deleted
    Given I open "Bookmarks" page in admin SiteAccess
    And there's a "BookmarkFolder" Content Item on Bookmarks list
    When I delete the bookmark for "BookmarkFolder" Content Item
    Then there's no "BookmarkFolder" Content Item on Bookmarks list

  Scenario: Bookmarked Content Item can be previewed from UDW
    Given I'm on Content view Page for "root"
    And I click on the left menu bar button "Browse"
    And I change the UDW tab to "Bookmarks"
    When I select bookmarked content "BookmarkUDW" through UDW
    And I preview selected content
    Then I'm on Content view Page for "root/BookmarkUDW"
