Feature: Searching for a Content item
  As an administrator
  I want to search for Content items.

  @javascript @admin @common
  Scenario: Content can be searched for
    Given I create "folder" Content items in root in "eng-GB"
      | name              | short_name          |
      | Searched folder   | Searched folder     |
    And I am logged as "admin"
    And I go to "Content structure" in "Content" tab
    And I click on the left menu bar button "Search"
    When I search for a Content named "Searched folder"
    Then I should see in search results an item named "Searched folder"
    