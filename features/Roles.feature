Feature: Roles management
  As an administrator
  In order to customize my eZ installation
  I want to manage users Roles.

  Background:
    Given I am logged as "admin"
      And I go to "Roles" in "Admin" tab

  @javascript @common
  Scenario: Changes can be discarded while creating Role
    When I start creating new "Role"
      And I set "Name" to "Test RL"
      And I click on the edit action bar button "Discard changes"
    Then I should be on "Roles" page
      And there's no "Test RL" on "Roles" list

  @javascript @common
  Scenario: New Role can be created
    When I start creating new "Role"
      And I set "Name" to "Test RL"
      And I click on the edit action bar button "Create"
    Then I should be on "Role" "Test RL" page
      And "Policies" list in "Role" "Test RL" is empty
      And "Assignments" list in "Role" "Test RL" is empty

  @javascript @common
  Scenario: I can navigate to Roles through breadcrumb
    Given I go to "Test RL" "Role" page
    When I click on "Roles" on breadcrumb
    Then I should be on "Roles" page

  @javascript @common
  Scenario: Changes can be discarded while editing Role
    Given there's "Test RL" on "Roles" list
    When I start editing "Role" "Test RL"
      And I set "Name" to "Test RL edited"
      And I click on the edit action bar button "Discard changes"
    Then I should be on "Roles" page
      And there's "Test RL" on "Roles" list
      And there's no "Test RL edited" on "Roles" list

  @javascript @common
  Scenario: Role can be edited
    Given there's "Test RL" on "Roles" list
    When I start editing "Role" "Test RL"
      And I set "Name" to "Test RL edited"
      And I click on the edit action bar button "Save"
    Then I should be on "Role" "Test RL edited" page
      And "Policies" list in "Role" "Test RL" is empty
      And "Assignments" list in "Role" "Test RL" is empty

  @javascript @common
  Scenario: User assignation can be discarded
    Given there's "Test RL edited" on "Roles" list
    When I start assigning to "Test RL edited" from "Roles" page
      And I select "Administrator User" from "User"
      And I select options from "Group"
      | option  |
      | Editors |
      | Users   |
      And I select "Media" from Sections as role assignment limitation
      And I click on the edit action bar button "Discard changes"
    Then I should be on "Role" "Test RL edited" page
      And "Policies" list in "Role" "Test RL edited" is empty
      And "Assignments" list in "Role" "Test RL edited" is empty

  @javascript @common
  Scenario: User can be assigned to role from the Roles list
    Given there's "Test RL edited" on "Roles" list
    When I start assigning to "Test RL edited" from "Roles" page
      And I select options from "User"
      | option             |
      | Administrator User |
      | Anonymous User     |
      And I select "Editors" from "Group"
      And I select limitation "Media/Images" for assignment through UDW
      And I click on the edit action bar button "Save"
    Then I should be on "Role" "Test RL edited" page
      And "Policies" list in "Role" "Test RL edited" is empty
      And There are assignments on the "Test RL edited" assignments list
      | user/group          | limitation                         |
      | Administrator User  | Subtree of Location: /Media/Images |
      | Anonymous User      | Subtree of Location: /Media/Images |
      | Editors             | Subtree of Location: /Media/Images |

  @javascript @common
  Scenario: User can be assigned to role from the Role details view
    Given there's "Test RL edited" on "Roles" list
      And I go to "Test RL edited" "Role" page
    When I start assigning users and groups to "Test RL edited" from role page
      And I select "Users" from "Group"
      And I click on the edit action bar button "Save"
    Then I should be on "Role" "Test RL edited" page
      And "Policies" list in "Role" "Test RL edited" is empty
      And There are assignments on the "Test RL edited" assignments list
      | user/group          | limitation                         |
      | Administrator User  | Subtree of Location: /Media/Images |
      | Editors             | Subtree of Location: /Media/Images |
      | Anonymous User      | Subtree of Location: /Media/Images |
      | Users	            | None                               |

  @javascript @common
  Scenario: Assignment can be deleted from role
    Given there's "Test RL edited" on "Roles" list
      And I go to "Test RL edited" "Role" page
    When I delete assignment from "Test RL edited" role
      | item                |
      | Administrator User  |
      | Editors             |
      | Users	            |
    Then I should be on "Role" "Test RL edited" page
      And "Policies" list in "Role" "Test RL edited" is empty
      And There's an assignment "Subtree of Location: /Media/Images" for "Anonymous User" on the "Test RL edited" assignments list

  @javascript @common
  Scenario: Adding policy can be discarded
    Given there's "Test RL edited" on "Roles" list
      And I go to "Test RL edited" "Role" page
    When I start creating new "Policy" in "Test RL edited"
      And I select policy "Class / All functions"
      And I click on the edit action bar button "Discard changes"
    Then I should be on "Role" "Test RL edited" page
      And "Policies" list in "Role" "Test RL edited" is empty
      And There's an assignment "Subtree of Location: /Media/Images" for "Anonymous User" on the "Test RL edited" assignments list

  @javascript @common
  Scenario: Policies can be added to role
    Given there's "Test RL edited" on "Roles" list
      And I go to "Test RL edited" "Role" page
    When I start creating new "Policy" in "Test RL edited"
      And I select policy "Content / Read"
      And I click on the edit action bar button "Create"
    Then I should be on "Role" "Test RL edited" page
      And There's a policy "Content/Read" with "None" limitation on the "Test RL edited" policies list
      And There's an assignment "Subtree of Location: /Media/Images" for "Anonymous User" on the "Test RL edited" assignments list

  @javascript @common
  Scenario: Policies can be edited
    Given there's "Test RL edited" on "Roles" list
      And I go to "Test RL edited" "Role" page
    When I start editing "Policy" "Content" from "Test RL edited"
      And I select options from "Class"
      | option  |
      | Article |
      | Folder  |
      And I select subtree limitation "Users/Anonymous Users" for policy through UDW
      And I select "Lock:Locked" from "State"
      And I click on the edit action bar button "Update"
    Then I should be on "Role" "Test RL edited" page
      And There are policies on the "Test RL edited" policies list
      | policy       | limitation                                  |
      | Content/Read | Content Type: Article, Folder               |
      | Content/Read | Subtree of Location: /Users/Anonymous Users |
      | Content/Read | State: Lock:Locked                          |
      And There's an assignment "Subtree of Location: /Media/Images" for "Anonymous User" on the "Test RL edited" assignments list

  @javascript @common
  Scenario: Policy can be deleted
    Given there's "Test RL edited" on "Roles" list
      And I go to "Test RL edited" "Role" page
    When I delete policy from "Test RL edited" role
      | item     |
      | Content  |
    Then notification that "Policies in role" "Test RL edited" is removed appears
      And "Policies" list in "Role" "Test RL edited" is empty
      And There's an assignment "Subtree of Location: /Media/Images" for "Anonymous User" on the "Test RL edited" assignments list

  @javascript @common
  Scenario: Role can be deleted
    Given there's "Test RL edited" on "Roles" list
    When I delete "Role"
      | item           |
      | Test RL edited |
    Then there's no "Test RL edited" on "Roles" list
      And notification that "Role" "Test RL edited" is removed appears