<?php
namespace SkydiveMarius\HWM\Server\Tests;

use PHPUnit\Framework\TestCase;
use SkydiveMarius\HWM\Server\Src\Controller;
use SkydiveMarius\HWM\Server\Src\InfluxDbRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ControllerTest
 *
 * @package SkydiveMarius\HWM\Server\Tests
 */
class ControllerTest extends TestCase
{
    /**
     * @var InfluxDbRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $repository;

    /**
     * @var Controller
     */
    private $controller;

    public function setUp()
    {
        $this->repository = $this->getMockBuilder(InfluxDbRepository::class)->disableOriginalConstructor()->getMock();
        $this->controller = new Controller($this->repository, 'correctToken');
    }

    public function test_handle_wrongAPIToken()
    {
        $request = new Request([], [], [], [], [], ['api-token' => 'wrong']);
        $response = $this->controller->handle($request);

        self::assertInstanceOf(JsonResponse::class, $response);
        self::assertEquals(500, $response->getStatusCode());
        self::assertEquals(['code' => 0, 'message' => 'Wrong API token submitted'], json_decode($response->getContent(), true));
    }

    public function test_handle_noJson()
    {
        $request = new Request(['api-token' => 'correctToken'], [], [], [], [], [], 'abc');
        $response = $this->controller->handle($request);

        self::assertInstanceOf(JsonResponse::class, $response);
        self::assertEquals(500, $response->getStatusCode());
        self::assertEquals(['code' => 24, 'message' => 'Value "<NULL>" is not an array.'], json_decode($response->getContent(), true));
    }

    public function test_handle_averageMissing()
    {
        $data = [];
        $request = new Request(['api-token' => 'correctToken'], [], [], [], [], [], json_encode($data));
        $response = $this->controller->handle($request);

        self::assertInstanceOf(JsonResponse::class, $response);
        self::assertEquals(500, $response->getStatusCode());
        self::assertEquals(['code' => 46, 'message' => 'The element with key "average" was not found'], json_decode($response->getContent(), true));
    }

    public function test_handle_averageNotFloat()
    {
        $data = ['average' => 'abc'];
        $request = new Request(['api-token' => 'correctToken'], [], [], [], [], [], json_encode($data));
        $response = $this->controller->handle($request);

        self::assertInstanceOf(JsonResponse::class, $response);
        self::assertEquals(500, $response->getStatusCode());
        self::assertEquals(['code' => 9, 'message' => 'Value "abc" is not a float.'], json_decode($response->getContent(), true));
    }

    public function test_handle_correct_repositoryCalled()
    {
        $this->repository->expects(self::once())
            ->method('add')
            ->with(self::equalTo(14.1));

        $data = ['average' => 14.1];
        $request = new Request(['api-token' => 'correctToken'], [], [], [], [], [], json_encode($data));
        $response = $this->controller->handle($request);

        self::assertInstanceOf(JsonResponse::class, $response);
        self::assertEquals(200, $response->getStatusCode());
    }
}