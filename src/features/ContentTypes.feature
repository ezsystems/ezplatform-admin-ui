Feature: Content types management
  As a administrator
  In order to customize my eZ installation
  I want to manage my Content types structure.
      
  @javascript @common
  Scenario: Content Type Groups context is available
    Given that im in context of Content Type Groups
    Then i can see Content Type Groups list
      
  @javascript @common
  Scenario: Changes can be discarded while creating new Content Type Group
    Given that im in context of Content Type Groups
    When i click plus button
      And fill Name field
      And click Discard Changes
    Then i shoud be redirected to Content Types context
      And i should see Content Type Groups list
      And there's nothing new on the list
      
  @javascript @common
  Scenario: New Content Type Group can be added
    Given that im in context of Content Type Groups
    When i click plus button
      And i fill Name field
      And i click Create button
    Then i should be in new group context
      
  @javascript @common
  Scenario: Changes can be discarded while creating Content Type
    Given that im in context of Content Type Group
    When i click plus button
      And i fill Name field
      And i fill Identifier field
      And i click Discard changes
    Then i shoud be redirected to Content Type Group context
      And i should see Content Types list
      And there's nothing new on the list

  @javascript @common
  Scenario: New Field can be added to new Content Type
    Given that im in context of Content Type Group
    When i click plus button
      And i fill Name field
      And i fill Identifier field
      And i fill Content name pattern field
      And i select Text line content field definition
      And Click Add field definition
    Then message "Content type 'content_type_name' updated." appears
      And form for new fieldtype definition appears.

  @javascript @common
  Scenario: New Content Type can be added to Content Type Group
    Given that i'm in context of content type update form
      And there are all necessary field filled
      And there is at least one field added
    When i click Save
    Then i should be redirected to new Content Type context
      And Content Type has proper values
      And Content Type has proper fields
      And message "Content type 'content_type_name' updated." appears

  @javascript @common
  Scenario: I can navigate to Content Type Group through breadcrumb
    Given that i'm in context of Content Type
    When i click on Content Type group name on breadcrumb
    Then i shoud be redirected to Content Type Group context
      And i should see Content Types list

  @javascript @common
  Scenario: Changes can be discarded while editing Content type
    Given that i'm in context of Content Type Group
      And there's Content Type on the list
    When i click Edit button left to 'content_type_name'
      And fill Name field.
      And click Discard changes
    Then i shoud be redirected to Content Type Group context
      And i should see Content Types list
      And changes shoudn't be applied to content type name

  @javascript @common
  Scenario: New Field can be added while editing Content Type
    Given that im in context of Content Type Group
      And there's Content Type on the list
    When i click Edit button left to 'content_type_name'
      And i select Date content field definition
      And Click Add field definition
    Then message "Content type 'content_type_name' updated." appears
      And form for new fieldtype definition appears.

  @javascript @common
  Scenario: Content type can be edited
    Given that i'm in context of Content Type update form
      And there's Content Type on the list
      And i fill Name field.
      And click Save
    Then i should be redirected to new Content Type context
      And Content Type has proper values
      And Content Type has proper fields
      And message "Content type 'content_type_name' updated." appears

  @javascript @common
  Scenario: Changes of Content Type are visible in Content Type Group context
    Given that i'm in context of just edited Content Type
    When i click on Content Type Group name on breadcrumb
    Then i shoud be redirected to Content Type Group context
      And i should see Content Types list
      And Content Type should have name 'content_type_name_edited'

  @javascript @common
  Scenario: Content type can be deleted from Content Type Group
    Given that i'm in context of Content Type Group
    When i click on checkbox left to Content Type
      And i click on trash icon
      And i click delete on dialog
    Then content type should disappear from content type list
      And message "Content type 'content_type_name' deleted." appears.

  @javascript @common
  Scenario: I can navigate to Admin / Content Types through breadcrumb
    Given that i'm in context of Content Type Group
    When i click on Content Types on breadcrumb
    Then i shoud be redirected to Content Type Groups context
      And i should see Content Type Groups list

  @javascript @common
  Scenario: Changes can be discarded while editing Content Type Group
    Given that i'm in context of Content Type Groups
      And there's Content Type Groups on the list
    When i click Edit button left to 'content_type_group_name'
      And fill Name field.
      And click Discard changes
    Then i shoud be redirected to Content Type Groups context
      And i should see Content Type Groups list
      And changes shoudn't be applied to group name

  @javascript @common
  Scenario: Content Type Group can be edited
    Given that i'm in context of Content Type Groups
      And there's Content Type Groups on the list
    When i click Edit button left to 'content_type_group_name'
      And fill Name field.
      And click Save
    Then i should be redirected to Content Group context
      And message "Content type group 'content_type_group' updated." appears

  @javascript @common
  Scenario: Content type group can be deleted
    Given that i'm in context of Content Type Groups
      And there's empty Content Type Group on Content Type Groups List
    When i click on checkbox left to empty Content Type Group
      And i click on trash icon
      And i click delete on dialog
    Then content type should disappear from content type list
      And message "Content type group 'content_type_grup_name' deleted." appears.

  @javascript @common
  Scenario: Non-empty Content type group cannot be deleted
    Given that i'm in context of Content Type Groups
    When Content Type Group is non-empty
    Then i can't click on checkbox left to this Content Type Group
