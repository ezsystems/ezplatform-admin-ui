@IbexaOSS @IbexaContent @IbexaExperience @IbexaCommerce
Feature: Searching for a Content item
  As an administrator
  I want to search for Content items.

  @javascript @APIUser:admin
  Scenario: Content can be searched for
    Given I create "folder" Content items in root in "eng-GB"
      | name              | short_name          |
      | Searched folder   | Searched folder     |
    Given I open Login page in admin SiteAccess
    And I am logged as admin
    And I open "Search" page in admin SiteAccess
    When I search for a Content named "Searched folder"
    Then I should see in search results an item named "Searched folder"
