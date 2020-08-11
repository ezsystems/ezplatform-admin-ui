Feature: Trash management

    @javascript @common
    Scenario: Trash can be emptied
    Given I am logged as "admin"
        And I go to "Content structure" in "Content" tab
        And I click on the left menu bar button "Trash"
        And trash is not empty
    When I empty the trash
    Then trash is empty
