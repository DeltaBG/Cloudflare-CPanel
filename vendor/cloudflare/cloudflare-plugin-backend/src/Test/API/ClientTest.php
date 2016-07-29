<?php

namespace CF\API\Test;

use CF\API\Client;
use CF\Integration\DefaultIntegration;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    private $mockConfig;
    private $mockClientAPI;
    private $mockAPI;
    private $mockDataStore;
    private $mockLogger;
    private $mockCpanelIntegration;

    public function setup()
    {
        $this->mockConfig = $this->getMockBuilder('CF\Integration\DefaultConfig')
            ->disableOriginalConstructor()
            ->getMock();
        $this->mockAPI = $this->getMockBuilder('CF\Integration\IntegrationAPIInterface')
            ->getMock();
        $this->mockDataStore = $this->getMockBuilder('CF\Integration\DataStoreInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->mockLogger = $this->getMockBuilder('CF\Integration\DefaultLogger')
            ->disableOriginalConstructor()
            ->getMock();
        $this->mockCpanelIntegration = new DefaultIntegration($this->mockConfig, $this->mockAPI, $this->mockDataStore, $this->mockLogger);

        $this->mockClientAPI = new Client($this->mockCpanelIntegration);
    }

    public function testBeforeSendAddsRequestHeaders() {
        $apiKey = "apiKey";
        $email = "email";

        $this->mockDataStore->method('getClientV4APIKey')->willReturn($apiKey);
        $this->mockDataStore->method('getCloudFlareEmail')->willReturn($email);

        $request = new \CF\API\Request(null, null, null, null);
        $beforeSendRequest = $this->mockClientAPI->beforeSend($request);

        $actualRequestHeaders = $beforeSendRequest->getHeaders();
        $expectedRequestHeaders = array(
            Client::X_AUTH_KEY => $apiKey,
            Client::X_AUTH_EMAIL => $email,
            Client::CONTENT_TYPE_KEY => Client::APPLICATION_JSON_KEY
        );

        $this->assertEquals($expectedRequestHeaders[Client::X_AUTH_KEY], $actualRequestHeaders[Client::X_AUTH_KEY]);
        $this->assertEquals($expectedRequestHeaders[Client::X_AUTH_EMAIL], $actualRequestHeaders[Client::X_AUTH_EMAIL]);
        $this->assertEquals($expectedRequestHeaders[Client::CONTENT_TYPE_KEY], $actualRequestHeaders[Client::CONTENT_TYPE_KEY]);
    }

    public function testClientApiErrorReturnsValidStructure()
    {
        $expectedErrorResponse = array(
            'result' => null,
            'success' => false,
            'errors' => array(
                array(
                    'code' => '',
                    'message' => 'Test Message',
                )
            ),
            'messages' => array()
        );
        $errorResponse = $this->mockClientAPI->createAPIError("Test Message");
        $this->assertEquals($errorResponse, $expectedErrorResponse);
    }

    public function testResponseOkReturnsTrueForValidResponse()
    {
        $v4APIResponse = array(
            "success" => true
        );

        $this->assertTrue($this->mockClientAPI->responseOk($v4APIResponse));
    }
}