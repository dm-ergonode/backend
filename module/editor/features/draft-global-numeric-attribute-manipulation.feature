Feature: Draft edit and inheritance value for product draft with numeric attribute

  Background:
    Given I am Authenticated as "test@ergonode.com"
    And I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"

  Scenario: Get language en
    When I send a GET request to "/api/v1/en_GB/languages/en_GB"
    Then the response status code should be 200
    And store response param "id" as "language_id_en"

  Scenario: Get language pl
    When I send a GET request to "/api/v1/en_GB/languages/pl_PL"
    Then the response status code should be 200
    And store response param "id" as "language_id_pl"

  Scenario: Get language fr
    When I send a GET request to "/api/v1/en_GB/languages/fr_FR"
    Then the response status code should be 200
    And store response param "id" as "language_id_fr"

  Scenario: Update Tree
    Given I am Authenticated as "test@ergonode.com"
    And I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    When I send a PUT request to "/api/v1/en_GB/language/tree" with body:
      """
        {
          "languages":
            {
              "language_id":"@language_id_en@",
              "children":[
                {
                  "language_id":"@language_id_pl@",
                  "children":[]
                },
                {
                  "language_id":"@language_id_fr@",
                  "children":[]
                }
              ]
            }

        }
      """
    Then the response status code should be 204

  Scenario: Create numeric attribute
    Given remember param "attribute_code" with value "numeric_@@random_code@@"
    When I send a POST request to "/api/v1/en_GB/attributes" with body:
      """
      {
        "code": "@attribute_code@",
        "type": "NUMERIC",
        "scope": "global",
        "groups": []
      }
      """
    Then the response status code should be 201
    And store response param "id" as "attribute_id"

  Scenario: Create template
    When I send a POST request to "/api/v1/en_GB/templates" with body:
      """
      {
        "name": "@@random_md5@@",
        "elements": []
      }
      """
    Then the response status code should be 201
    And store response param "id" as "template_id"

  Scenario: Create product
    When I send a POST request to "/api/v1/en_GB/products" with body:
      """
      {
        "type": "SIMPLE-PRODUCT",
        "sku": "SKU_@@random_code@@",
        "templateId": "@template_id@",
        "categoryIds": []
      }
      """
    Then the response status code should be 201
    And store response param "id" as "product_id"

  Scenario: Edit product numeric value in "en_GB" language
    When I send a PUT request to "api/v1/en_GB/products/@product_id@/draft/@attribute_id@/value" with body:
      """
      {
        "value": 100
      }
      """
    Then the response status code should be 200

  Scenario: Edit product numeric value in "pl_PL" language
    When I send a PUT request to "api/v1/pl_PL/products/@product_id@/draft/@attribute_id@/value" with body:
      """
      {
        "value": 200
      }
      """
    Then the response status code should be 403

  Scenario: Get draft values in "pl_PL" language
    When I send a GET request to "api/v1/pl_PL/products/@product_id@/draft"
    Then the response status code should be 200
    And the JSON nodes should be equal to:
      | attributes.@attribute_code@ | 100 |

  Scenario: Get draft values in "en_GB" language
    When I send a GET request to "api/v1/en_GB/products/@product_id@/draft"
    Then the response status code should be 200
    And the JSON nodes should be equal to:
      | attributes.@attribute_code@ | 100 |

  Scenario: Get draft values in "fr_FR" language
    When I send a GET request to "api/v1/fr_FR/products/@product_id@/draft"
    Then the response status code should be 200
    And the JSON nodes should be equal to:
      | attributes.@attribute_code@ | 100 |

  Scenario: Remove value for "pl_PL" language
    When I send a DELETE request to "api/v1/pl_PL/products/@product_id@/draft/@attribute_id@/value"
    Then the response status code should be 403

  Scenario: Edit product numeric value in "en_GB" language
    When I send a PUT request to "api/v1/en_GB/products/@product_id@/draft/@attribute_id@/value" with body:
      """
      {
        "value": 200
      }
      """
    Then the response status code should be 200

  Scenario: Get draft values in "en_GB" language
    When I send a GET request to "api/v1/en_GB/products/@product_id@/draft"
    Then the response status code should be 200
    And the JSON nodes should be equal to:
      | attributes.@attribute_code@ | 200 |

  Scenario: Get draft values in "fr_FR" language
    When I send a GET request to "api/v1/fr_FR/products/@product_id@/draft"
    Then the response status code should be 200
    And the JSON nodes should be equal to:
      | attributes.@attribute_code@ | 200 |

  Scenario: Edit product numeric value (zero) in "en_GB" language
    When I send a PUT request to "api/v1/en_GB/products/@product_id@/draft/@attribute_id@/value" with body:
      """
      {
        "value": 0
      }
      """
    Then the response status code should be 200

  Scenario: Get draft values in "en_GB" language
    When I send a GET request to "api/v1/en_GB/products/@product_id@/draft"
    Then the response status code should be 200
    And the JSON nodes should be equal to:
      | attributes.@attribute_code@ | 0 |
