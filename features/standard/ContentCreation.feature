Feature: Content items creation
  As an administrator
  In order to menage content to my site
  I want to create, edit, copy and move content items.

  Background:
    Given I am logged as "admin"
    And I go to "Content structure" in "Content" tab

  @javascript @common
  Scenario: Content creation can be cancelled
    When I start creating a new content "Article"
    And I set content fields
      | label | value        |
      | Title | Test Article |
    And I click on the edit action bar button "Cancel"
    Then I should be on root container page in Content View
    And there's no "Test Article" "Article" on Sub-items list of root

  @javascript @common
  Scenario: Content can be previewed during creation
    When I start creating a new content "Article"
    And I set content fields
      | label | value              |
      | Title | Test Article       |
    And I set article main content field to "Test article intro"
    And I click on the edit action bar button "Preview"
    And I go to "tablet" view in "Test Article" preview
    And I go to "mobile" view in "Test Article" preview
    And I go to "desktop" view in "Test Article" preview
    And I go back from content "Test Article" preview
    Then I should be on "Content Update" "Test Article" page
    And content fields are set
      | label | value        |
      | Title | Test Article |
    And article main content field is set to "Test article intro"

  @javascript @common
  Scenario: Content can be published
    When I start creating a new content "Article"
    And I set content fields
      | label | value        |
      | Title | Test Article |
    And I set article main content field to "Test article intro"
    And I click on the edit action bar button "Publish"
    Then success notification that "Content published." appears
    And I should be on content container page "Test Article" of type "Article" in root path
    And content attributes equal
      | label | value        |
      | Title | Test Article |
    And article main content field equals "Test article intro"

  @javascript @common
  Scenario: Content can be edited
    Given I navigate to content "Test Article" of type "Article" in root path
    And I click on the edit action bar button "Edit"
    And I set content fields
      | label | value               |
      | Title | Test Article edited |
    And I set article main content field to "Test Article intro edited"
    And I click on the edit action bar button "Publish"
    Then success notification that "Content published." appears
    And I should be on content container page "Test Article edited" of type "Article" in root path
    And content attributes equal
      | label | value               |
      | Title | Test Article edited |
    And article main content field equals "Test Article intro edited"

  @javascript @common
  Scenario: Content can be previewed during edition
    Given I open UDW and go to "root/Test Article edited"
    When I click on the edit action bar button "Edit"
    And I should be on "Content Update" "Test Article edited" page
    And I click on the edit action bar button "Preview"
    And I go to "tablet" view in "Test Article edited" preview
    And I go to "mobile" view in "Test Article edited" preview
    And I go to "desktop" view in "Test Article edited" preview
    And I go back from content "Test Article edited" preview
    Then I should be on "Content Update" "Test Article edited" page
    And content fields are set
      | label | value               |
      | Title | Test Article edited |
    And article main content field is set to "Test Article intro edited"
