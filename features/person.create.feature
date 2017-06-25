Feature: Person
  Person CRUD'S create method

  Scenario: Creating a person with invalid data
    Given I Request "/person/create" with method "PUT" and content
    """
    {
      "firstName": "Test"
    }
    """
    Then I get response code "400"
    And I get response that is valid JSON

  Scenario: Creating a person with valid personal data and agreement
    Given I Request "/person/create" with method "PUT" and content
    """
    {
      "firstName": "John",
      "lastName": "Doe",
      "agreement": {
        "number": "edagb45dqwwrr234tgfbvbfsdvddwqerw",
        "signingDate": "2017-06-26 13:37:00"
      }
    }
    """
    Then I get response code "200"
    And I get response that is valid JSON
    And The JSON response at "response" should have key "id"

  Scenario: Creating a person with valid personal data and agreement and address (one with invalid type and data that should be ignored)
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
        },
        "invalidType": {
          "address": "a",
          "city": "s"
        }
      }
    }
    """
    Then I get response code "200"
    And I get response that is valid JSON
    And The JSON response at "response" should have key "id"

  Scenario: Creating a person with valid personal data and agreement and invalid address
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
          "address": "a",
          "city": "y"
        }
      }
    }
    """
    Then I get response code "400"
    And I get response that is valid JSON

  Scenario: Creating a person with valid personal data and invalid agreement
    Given I Request "/person/create" with method "PUT" and content
    """
    {
      "firstName": "John",
      "lastName": "Doe",
      "agreement": {
        "number": "too short",
        "signingDate": "xyz"
      }
    }
    """
    Then I get response code "400"
    And I get response that is valid JSON

  Scenario: Creating a person with valid personal data and agreement but invalid address
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
            "address": "x"
        },
      }
    }
    """
    Then I get response code "400"
    And I get response that is valid JSON
