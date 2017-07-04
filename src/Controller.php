<?php
namespace SkydiveMarius\HWM\Server\Src;

use Assert\Assertion;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class Controller
 *
 * @package SkydiveMarius\HWM\Server\Src
 */
class Controller
{
    /**
     * @var InfluxDbRepository
     */
    private $repository;
    /**
     * @var null|string
     */
    private $apiToken;

    /**
     * Controller constructor.
     *
     * @param InfluxDbRepository $repository
     * @param string|null        $apiToken
     */
    public function __construct(InfluxDbRepository $repository = null, string $apiToken = null)
    {
        $this->repository = $repository ?: new InfluxDbRepository();
        $this->apiToken = $apiToken ?: getenv('AUTH_TOKEN');
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function handle(Request $request): Response
    {
        try {
            $this->authenticate($request);
            $this->persist($request);
        } catch (\Throwable $e) {
            $response = [
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ];
            return new JsonResponse($response, 500);
        }

        return new JsonResponse([]);
    }

    /**
     * @param Request $request
     */
    private function persist(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        Assertion::isArray($data);
        Assertion::keyIsset($data, 'average');
        Assertion::float($data['average']);

        $this->repository->add($data['average']);
    }

    /**
     * @param Request $request
     */
    private function authenticate(Request $request)
    {
        if ($request->headers->get('api-token') !== $this->apiToken
            && $request->get('api-token') !== $this->apiToken) {
            throw new \InvalidArgumentException('Wrong API token submitted');
        }
    }
}