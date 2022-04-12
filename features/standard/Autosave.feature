@IbexaOSS @IbexaContent @IbexaExperience @IbexaCommerce
Feature: Content Items creation
  As an administrator
  In order to manage content to my site
  I want to check autosave feature during creation of Content Items

  @javascript @APIUser:admin
  Scenario: Content item is visible in draft dashboard after being autosaved
    Given I create a user group "AutosaveEnabledTestGroup"
    And I create a role "autosaveEnabledTestRole" with policies
      | module | function |
      | *      | *        |
    And I create a user "AutosaveEnabledTestUser" with last name "AutosaveEnabledTestLastName" in group "AutosaveEnabledTestGroup"
    And I assign user "AutosaveEnabledTestUser" to role "autosaveEnabledTestRole"
    And I set autosave interval value to "5" for user "AutosaveEnabledTestUser"
    And I open Login page in admin SiteAccess
    And I log in as "AutosaveEnabledTestUser" with password "Passw0rd-42"
    And I'm on Content view Page for root
    When I start creating a new content "Article"
    And I set content fields
      | label       | value                       |
      | Title       | Test Article Autosave draft |
      | Short title | Test Article Autosave draft |
    And I wait for 5 seconds for Content Item to be autosaved
    And I click on the close button
    And I open the "Dashboard" page in admin SiteAccess
    Then there's draft "Test Article Autosave draft" on Dashboard list

  @javascript @APIUser:admin
  Scenario: Content item is not autosaved and draft is not visible in dashboard when autosave is disabled
    Given I create a user group "AutosaveDisabledTestGroup"
    And I create a role "autosaveDisabledTestRole" with policies
      | module | function |
      | *      | *        |
    And I create a user "AutosaveDisabledTestUser" with last name "AutosaveDisabledTestLastName" in group "AutosaveDisabledTestGroup"
    And I assign user "AutosaveDisabledTestUser" to role "autosaveDisabledTestRole"
    And I open Login page in admin SiteAccess
    And I log in as "AutosaveDisabledTestUser" with password "Passw0rd-42"
    And I'm on Content view Page for root
    And I go to user settings
    And I disable autosave
    And I click on the edit action bar button "Save"
    And I'm on Content view Page for root
    When I start creating a new content "Article"
    And I set content fields
      | label       | value                       |
      | Title       | Test Article Autosave Off draft |
      | Short title | Test Article Autosave Off draft |
    And I click on the close button
    And I open the "Dashboard" page in admin SiteAccess
    Then there's no draft "Test Article Autosave Off draft" on Dashboard list
