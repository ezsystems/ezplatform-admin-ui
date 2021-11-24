@test
Feature: Add new product to cart

  @javascript
  Scenario Outline: Wartosc koszyka jest dobra po dodaniu produktu
    Given my basket is empty
    When I add product with price <price1>
    And I add product with price <price2>
    Then total sum should be equal to <expectedTotal>
    And I open the Login Page test

    Examples:
     | price1 | price2 | expectedTotal |
     | 10     | 20     | 30            |


