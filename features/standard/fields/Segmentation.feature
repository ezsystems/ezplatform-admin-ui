@IbexaOSS @IbexaContent @IbexaExperience @IbexaCommerce @testseg
Feature: Roles management
  As an administrator
  In order to customize my eZ installation
  I want to manage Segments.

  # scenariusze bez API
  @javascript @testseg1
  Scenario: Create a segment group without segments under segments group
    Given I am logged as admin
    And I open "Segmentation" page in admin SiteAccess
    When I click on segment group creation popup button
    And I fill segment group configuration fields
      | label      | value     |
      | Name       | testname |
      | Identifier | testid     |
    And I confirm creation of new segment group
    Then There's segment group with "testname" name and "testid" identifier in Segment Group Information section

  @javascript @testseg2
  Scenario: Create a segment group with segments under segments group
    Given I am logged as admin
    And I open "Segmentation" page in admin SiteAccess
    When I click on segment group creation popup button
    And I fill segment group configuration fields
      | label      | value     |
      | Name       | testname2 |
      | Identifier | testid2    |
    And I add segment with "testsegname1" name and "testsegid1" identifier to segment group during segment group creation
    And I confirm creation of new segment group
    And There's segment group with "testname2" name and "testid2" identifier in Segment Group Information section
    Then There's segment with "testsegname1" name and "testsegid1" identifier in Segments Under Segment Group section

  @javascript @testseg3
  Scenario: Delete segment from segment group during edition
    Given I am logged as admin
    And I open "Segmentation" page in admin SiteAccess
    And I click on segment group creation popup button
    And I fill segment group configuration fields
      | label      | value     |
      | Name       | testname3 |
      | Identifier | testid3   |
    When I add segment with "testsegname3" name and "testsegid3" identifier to segment group during segment group creation
    And I confirm creation of new segment group
    And There's segment group with "testname3" name and "testid3" identifier in Segment Group Information section
    And There's segment with "testsegname3" name and "testsegid3" identifier in Segments Under Segment Group section
    Then I delete segment from Segments group

  @javascript @testseg4
  Scenario: Add segment to segment group during edition
    Given I am logged as admin
    And I open "Segmentation" page in admin SiteAccess
    And I click on segment group creation popup button
    And I fill segment group configuration fields
      | label      | value     |
      | Name       | testname4 |
      | Identifier | testid4   |
    And I confirm creation of new segment group
    And There's segment group with "testname4" name and "testid4" identifier in Segment Group Information section
    When I add segment with "testsegname4" name and "testsegid4" identifier to segment group during segment group edition
    Then There's segment with "testsegname4" name and "testsegid4" identifier in Segments Under Segment Group section

  @javascript @testseg5
  Scenario: Delete segment group
    Given I am logged as admin
    And I open "Segmentation" page in admin SiteAccess
    And I click on segment group creation popup button
    And I fill segment group configuration fields
      | label      | value     |
      | Name       | testname5 |
      | Identifier | testid5  |
    And I confirm creation of new segment group
    When I open "Segmentation" page in admin SiteAccess
    Then I delete segment group

  # scenariusze z migracjami
#delete segment group

  #add segment
  #edit segment
  #delete segment





