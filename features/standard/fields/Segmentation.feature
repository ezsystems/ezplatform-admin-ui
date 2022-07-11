@IbexaOSS @IbexaContent @IbexaExperience @IbexaCommerce
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
    And I add segment with "testsegname1" name and "testsegid1" identifier to segment group
    And I confirm creation of new segment group
    And There's segment group with "testname2" name and "testid2" identifier in Segment Group Information section
    Then There's segment with "testsegname1" name and "testsegid1" identifier in Segments Under Segment Group section


  # scenariusze z migracjami






