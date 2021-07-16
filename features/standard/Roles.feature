@IbexaOSS @IbexaContent @IbexaExperience @IbexaCommerce
Feature: Roles management
  As an administrator
  In order to customize my eZ installation
  I want to manage users Roles.

  @javascript
  Scenario: Changes can be discarded while creating Role
    Given I am logged as admin
    And I open "Roles" page in admin SiteAccess
    When I create a new Role
    And I set fields
      | label | value     |
      | Name  | Test Role |
    And I click on the edit action bar button "Discard changes"
    Then I should be on "Roles" page
    And there's no "Test Role" Role on Roles list

  @javascript
  Scenario: New Role can be created
    Given I am logged as admin
    And I open "Roles" page in admin SiteAccess
    When I create a new Role
    And I set fields
      | label | value     |
      | Name  | Test Role |
    And I click on the edit action bar button "Create"
    Then I should be on "Test Role" Role page
    And Policies list is empty
    And Assignments list is empty

  @javascript
  Scenario: Changes can be discarded while editing Role
    Given I am logged as admin
    And I open "Roles" page in admin SiteAccess
    And there's a "Test Role" Role on Roles list
    When I edit "Test Role" from Roles list
    And I set fields
      | label | value            |
      | Name  | Test Role edited |
    And I click on the edit action bar button "Discard changes"
    Then I should be on "Roles" page
    And there's a "Test Role" Role on Roles list
    And there's no "Test Role edited" Role on Roles list

  @javascript
  Scenario: Role can be edited
    Given I am logged as admin
    And I open "Roles" page in admin SiteAccess
    And there's a "Test Role" Role on Roles list
    When I edit "Test Role" from Roles list
    And I set fields
      | label | value            |
      | Name  | Test Role edited |
    And I click on the edit action bar button "Save"
    Then I should be on "Test Role edited" Role page
    And Policies list is empty
    And Assignments list is empty

  @javascript
  Scenario: User assignation can be discarded
    Given I am logged as admin
    And I open "Roles" page in admin SiteAccess
    And there's a "Test Role edited" Role on Roles list
    When I start assigning to "Test Role edited" from Roles page
    And I assign users to role
      | path                                         |
      | Users/Administrator users/Administrator User |
    And I assign groups to role
      | path          |
      | Users/Editors |
      | Users         |
    And I select "Media" from Sections as role assignment limitation
    And I click on the edit action bar button "Discard changes"
    Then I should be on "Test Role edited" Role page
    And Policies list is empty
    And Assignments list is empty

  @javascript
  Scenario: User can be assigned to role from the Roles list
    Given I am logged as admin
    And I open "Roles" page in admin SiteAccess
    And there's a "Test Role edited" Role on Roles list
    When I start assigning to "Test Role edited" from Roles page
    And I assign users to role
      | path                                         |
      | Users/Anonymous Users/Anonymous User         |
      | Users/Administrator users/Administrator User |
    And I assign groups to role
      | path          |
      | Users/Editors |
    And I select limitation "Media/Images" for assignment through UDW
    And I click on the edit action bar button "Save"
    Then I should be on "Test Role edited" Role page
    And Policies list is empty
    And there are assignments on the "Test Role edited" assignments list
      | User/Group          | Limitation                         |
      | Administrator User  | Subtree: /Media/Images |
      | Anonymous User      | Subtree: /Media/Images |
      | Editors             | Subtree: /Media/Images |

  @javascript
  Scenario: User can be assigned to role from the Role details view
    Given I am logged as admin
    And I open "Test Role edited" Role page in admin SiteAccess
    When I start assigning users and groups from Role page
    And I assign groups to role
      | path  |
      | Users |
    And I click on the edit action bar button "Save"
    Then I should be on "Test Role edited" Role page
    And Policies list is empty
    And there are assignments on the "Test Role edited" assignments list
      | User/Group          | Limitation                         |
      | Administrator User  | Subtree: /Media/Images |
      | Editors             | Subtree: /Media/Images |
      | Anonymous User      | Subtree: /Media/Images |
      | Users	            | None                               |

  @javascript
  Scenario: Assignment can be deleted from role
    Given I am logged as admin
    And I open "Test Role edited" Role page in admin SiteAccess
    When I delete assignment from "Test Role edited" role
      | item                |
      | Administrator User  |
      | Editors             |
      | Users	            |
    Then I should be on "Test Role edited" Role page
    And Policies list is empty
    And there are assignments on the "Test Role edited" assignments list
      | User/Group          | Limitation                         |
      | Anonymous User      | Subtree: /Media/Images |

  @javascript
  Scenario: Adding policy can be discarded
    Given I am logged as admin
    And I open "Test Role edited" Role page in admin SiteAccess
    When I start creating a new Policy
    And I select policy "Content Type / All functions"
    And I click on the edit action bar button "Discard changes"
    Then I should be on "Test Role edited" Role page
    And Policies list is empty
    And there are assignments on the "Test Role edited" assignments list
      | User/Group          | Limitation             |
      | Anonymous User      | Subtree: /Media/Images |

  @javascript
  Scenario: Policies can be added to role
    Given I am logged as admin
    And I open "Test Role edited" Role page in admin SiteAccess
    When I start creating a new Policy
    And I select policy "Content / Read"
    And I click on the edit action bar button "Create"
    And success notification that "Now you can set Limitations for the Policy." appears
    And I select limitation for "Content Type"
      | option  |
      | File    |
    And I click on the edit action bar button "Update"
    Then I should be on "Test Role edited" Role page
    And there is a policy "Content/Read" with "Content Type: File" limitation on the "Test Role edited" policies list
    And there are assignments on the "Test Role edited" assignments list
      | User/Group          | Limitation             |
      | Anonymous User      | Subtree: /Media/Images |

  @javascript
  Scenario: Policies without limitations can be added to role
    Given I am logged as admin
    And I open "Test Role edited" Role page in admin SiteAccess
    When I start creating a new Policy
    And I select policy "User / Password"
    And I click on the edit action bar button "Create"
    Then I should be on "Test Role edited" Role page
    And there is a policy "User/Password" with "None" limitation on the "Test Role edited" policies list
    And there are assignments on the "Test Role edited" assignments list
      | User/Group          | Limitation             |
      | Anonymous User      | Subtree: /Media/Images |

  @javascript
  Scenario: Policies can be edited
    Given I am logged as admin
    And I open "Test Role edited" Role page in admin SiteAccess
    When I start editing the policy "Content" "Read"
    And I select limitation for "Content Type"
      | option  |
      | Article |
      | Folder  |
    And I select subtree limitation "Users/Anonymous Users" for policy through UDW
    And I select limitation for "State"
      | option      |
      | Lock:Locked |
    And I click on the edit action bar button "Update"
    Then I should be on "Test Role edited" Role page
    And there are policies on the "Test Role edited" policies list
      | policy       | limitation                                  |
      | Content/Read | Content Type: Article, Folder               |
      | Content/Read | Subtree: /Users/Anonymous Users |
      | Content/Read | State: Lock:Locked                          |
    And there are assignments on the "Test Role edited" assignments list
      | User/Group          | Limitation             |
      | Anonymous User      | Subtree: /Media/Images |

  @javascript
  Scenario: Policy can be deleted
    Given I am logged as admin
    And I open "Test Role edited" Role page in admin SiteAccess
    When I delete policy from "Test Role edited" role
      | item     |
      | Content  |
    Then success notification that "Removed Policies from Role 'Test Role edited'." appears
    And there is no policy "Content/Read" with "Content Type: File" limitation on the "Test Role edited" policies list
    And there are assignments on the "Test Role edited" assignments list
      | User/Group          | Limitation             |
      | Anonymous User      | Subtree: /Media/Images |

  @javascript
  Scenario: Role can be deleted
    Given I am logged as admin
    And I open "Roles" page in admin SiteAccess
    And there's a "Test Role edited" Role on Roles list
    When I delete Role "Test Role edited"
    Then notification that "Role" "Test Role edited" is removed appears
    And there's no "Test Role edited" Role on Roles list

  @javascript
  Scenario: I can access role that has limitation to deleted location
    Given I am using the API as "admin"
    And I create "folder" Content items in root in "eng-GB"
      | name            | short_name      |
      | DeletedLocation | DeletedLocation |
    And I create a role "DeletedLocationRole"
    And I add policy "content" "read" to "DeletedLocationRole" with limitations
      | limitationType | limitationValue  |
      | Location       | /DeletedLocation |
    And I send "/DeletedLocation" to the Trash
    When I am logged as admin
    And I open "DeletedLocationRole" Role page in admin SiteAccess
    Then I should be on "DeletedLocationRole" Role page
