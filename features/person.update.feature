Feature: Person
  Person CRUD'S update method

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

  Scenario: Updating with invalid data
    Given I Request "/person/update/{response.id}" with method "PATCH" and content
    """
    {
      "firstName": "A",
      "lastName": "a",
    }
    """
    Then I get response code "400"
    And I get response that is valid JSON

  Scenario: Updating with valid data
    Given I Request "/person/update/{response.id}" with method "PATCH" and content
    """
    {
      "firstName": "John",
      "lastName": "Doe"
    }
    """
    Then I get response code "200"
    And I get response that is valid JSON

  Scenario: Updating with valid data and invalid agreement
    Given I Request "/person/update/{response.id}" with method "PATCH" and content
    """
    {
      "firstName": "John",
      "lastName": "Doe",
      "agreement": {
        "number": "a",
        "signingDate": "xyz"
      },
    }
    """
    Then I get response code "400"
    And I get response that is valid JSON

  Scenario: Updating with valid data and agreement
    Given I Request "/person/update/{response.id}" with method "PATCH" and content
    """
    {
      "firstName": "John",
      "lastName": "Doe",
      "agreement": {
        "number": "321321321321321321321321321321321321",
        "signingDate": "2017-02-02 20:20:20"
      }
    }
    """
    Then I get response code "200"
    And I get response that is valid JSON

  Scenario: Updating with valid data and agreement but invalid address
    Given I Request "/person/update/{response.id}" with method "PATCH" and content
    """
    {
      "firstName": "John",
      "lastName": "Doe",
      "agreement": {
        "number": "321321321321321321321321321321321321",
        "signingDate": "2017-02-02 20:20:20"
      },
      "addresses": {
        "home": {
            "address": "x",
            "city": "x"
        }
      }
    }
    """
    Then I get response code "400"
    And I get response that is valid JSON

  Scenario: Updating with valid data and agreement and valid address
    Given I Request "/person/update/{response.id}" with method "PATCH" and content
    """
    {
      "firstName": "John",
      "lastName": "Doe",
      "agreement": {
        "number": "321321321321321321321321321321321321",
        "signingDate": "2017-02-02 20:20:20"
      },
      "addresses": {
        "home": {
            "address": "Some Street 23/37",
            "city": "City"
        }
      }
    }
    """
    Then I get response code "200"
    And I get response that is valid JSON

  Scenario: Updating with valid data and agreement and valid address (and one that has invalid type and should be ignored)
    Given I Request "/person/update/{response.id}" with method "PATCH" and content
    """
    {
      "firstName": "John",
      "lastName": "Doe",
      "agreement": {
        "number": "321321321321321321321321321321321321",
        "signingDate": "2017-02-02 20:20:20"
      },
      "addresses": {
        "home": {
            "address": "Some Street 23/37",
            "city": "City"
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