@javascript @addLocation
Feature: Verify that an Editor with Content Type limitation on content/create policy can add location

  Scenario: I can add location
    Given I open Login page
    And I log in as "Add" with password "Passw0rd-42"
    And I go to "Content structure" in "Content" tab
    And I navigate to content "NewArticle" of type "Article" in root
    When I go to "Locations" tab in Content structure of item "NewArticle"
    And I add a new Location to item "NewArticle" under "root/Destination"
    Then I navigate to content "NewArticle" of type "Article" in "root/Destination"
