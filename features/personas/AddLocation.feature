@javascript @addLocation
Feature: Verify that an Editor with Content Type limitation on conten/create policy can add location

  Scenario: I can add location
    Given I open Login page
    And I log in as "Add" with password "Passw0rd-42"
    And I go to "Content structure" in "Content" tab
    And I start creating a new content "Article"
    And I set content fields
      | label | value |
      | Title | NewArticle |
      | Intro | NewArticle |
    And I click on the edit action bar button "Publish"
    And success notification that "Content published." appears
    And I should be on content item page "NewArticle" of type "Article" in root path
    
