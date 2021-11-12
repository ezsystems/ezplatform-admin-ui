@test @javascript
Feature: Example of BDD development

  Scenario: Example Scenario
    Given I start with number 2
    When I add number 3
    Then The result should be 5

  Scenario: Example Scenario 2
    Given I start with number 2
    When I add number 3
    And I add number 5
    Then the result should be 10