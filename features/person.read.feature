Feature: Person
  Person CRUD'S read method

  Scenario: Creating a person for other reading tests
    Given I Request "/person/create" with method "PUT" and content
    """
    {
      "firstName": "John",
      "lastName": "Doe",
      "agreement": {
        "number": "edagb45dqwwrr234tgfbvbfsdvddwqerw",
        "signingDate": "2017-06-26 13:37:00"
      },
      "addresses": {
        "home": {
          "address": "Some Street",
          "city": "Some City"
        }
      }
    }
    """
    Then I get response code "200"
    And I get response that is valid JSON
    And The JSON response at "response" should have key "id"

  Scenario: Reading everyone
    Given I Request "/person/{response.id}" with method "GET"
    Then I get response code "200"
    And I get response that is valid JSON
    And The JSON response at "response" should have key "id"
    And The JSON response at "response" should have key "firstName" with value "John"
    And The JSON response at "response" should have key "lastName" with value "Doe"
    And The JSON response at "response" should have key "agreement"
    And The JSON response at "response" should have key "addresses"


  Scenario: Reading everyone
    Given I Request "/person" with method "GET"
    Then I get response code "200"
    And I get response that is valid JSON
    And The JSON response at "response" should have key "0"

  Scenario: Reading a person that does not exist
    Given I Request "/person/idthatshouldneverexist" with method "GET"
    Then I get response code "404"
    And I get response that is valid JSON