<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * @var \Psr\Http\Message\ResponseInterface
     */
    protected $lastRequest;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $lastRequestResponse;

    /**
     * @var array
     */
    protected static $savedResponseValues = [];

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct($baseUrl)
    {
        $this->client   = new GuzzleHttp\Client([
            'http_errors' => false
        ]);
        $this->url      = $baseUrl;
    }

    public function performRequest($url, $method, $content)
    {
        // Save values in url
        $that = $this;
        $url = preg_replace_callback('/\{(.*?)\}/i', function($match) use ($that) {
            if (!array_key_exists($match[1], self::$savedResponseValues)) {
                throw new Exception($match[1] . ' not found, did you forget to "@Then The JSON response at :key should have key :key2"');
            }

            return self::$savedResponseValues[$match[1]];
        }, $url);

        $request = $this->client->request(
            $method,
            $this->url . $url,
            [
                'body' => $content
            ]
        );
        $this->lastRequest          = $request;
        $this->lastRequestResponse  = $this->lastRequest->getBody()->getContents();
    }

    /**
     * @Given I Request :url with method :method and content
     * :content
     */
    public function iRequestUrlWithMethodAndContent($url, $method, $content)
    {
        $this->performRequest($url, $method, $content);
    }

    /**
     * @Given I Request :url with method :method
     */
    public function iRequestUrlWithMethod($url, $method)
    {
        $this->performRequest($url, $method, null);

    }

    /**
     * @Then I get response code :code
     */
    public function iGetResponseWithCode($code)
    {
        \Assert\Assertion::eq($code, $this->lastRequest->getStatusCode());
    }

    /**
     * @Then I get response that is valid JSON
     */
    public function iGetResponseThatIsJSON()
    {
        $this->decodeJsonString($this->lastRequestResponse);

        if (json_last_error()) {
            throw new Exception('I was expecting a JSON response and that failed');
        }
    }

    /**
     * @Then The JSON response at :key should have key :key2
     */
    public function theJSONResponseAtKeyShouldHave($key1, $key2)
    {
        $response = $this->decodeJsonString($this->lastRequestResponse);

        if (!array_key_exists($key1, $response)) {
            throw new Exception('Key `'. $key1 . '`` not found in json response');
        } else {
            if (!array_key_exists($key2, $response[$key1])) {
                throw new Exception('Key `'. $key2 . '`` not found at `' . $key2 . '` in json response');
            }
        }

        self::$savedResponseValues[$key1 . '.' . $key2] = $response[$key1][$key2];
    }

    /**
     * @Then The JSON response at :key should have key :key2 with value :value
     */
    public function theJSONResponseAtKeyShouldHaveWithValue($key1, $key2, $value)
    {
        $response = $this->decodeJsonString($this->lastRequestResponse);

        if (!array_key_exists($key1, $response)) {
            throw new Exception('Key `'. $key1 . '`` not found in json response');
        } else {
            if (!array_key_exists($key2, $response[$key1])) {
                throw new Exception('Key `'. $key2 . '`` not found at `' . $key2 . '` in json response');
            }
        }

        \Assert\Assertion::eq($value, $response[$key1][$key2]);

        self::$savedResponseValues[$key1 . '.' . $key2] = $response[$key1][$key2];
    }

    /**
     * @param string $json
     * @return mixed
     */
    public function decodeJsonString(string $json)
    {
        return json_decode($json, JSON_BIGINT_AS_STRING);
    }
}
