Feature: Person
  Person CRUD'S delete method

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

  Scenario: Deleting a person
    Given I Request "/person/delete/{response.id}" with method "DELETE"
    Then I get response code "200"
    And I get response that is valid JSON

  Scenario: Deleting a person that does not exist
    Given I Request "/person/delete/{response.id}" with method "DELETE"
    Then I get response code "404"
    And I get response that is valid JSON