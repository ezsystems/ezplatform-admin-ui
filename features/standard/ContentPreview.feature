@IbexaOSS @IbexaContent @IbexaExperience @IbexaCommerce
Feature: Content Item preview
  As an administrator
  To make sure my Content looks good on mobile, desktop and tablet
  I want to preview it during creation

  Background:
      Given I am logged as admin
      And I'm on Content view Page for "root"

  @javascript
  Scenario: Content can be previewed during creation
    When I start creating a new content "Folder"
    And I set content fields
      | label | value     |
      | Name  | Test Name |
    And I click on the edit action bar button "Preview"
    And I go to "tablet" preview
    And I go to "mobile" preview
    And I go to "desktop" preview
    And I go back from content preview
    Then I should be on Content update page for "Test Name"
    And content fields are set
      | label | value     |
      | Name  | Test Name |
