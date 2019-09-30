<?php
/**
 */

namespace App\Controller;

use GuzzleHttp\Client;
use Commercetools\Core\Request\Products\ProductProjectionSearchRequest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class CatalogController extends Controller
{
    /**
     * @var Client
     */
    private $client;

    /**
     * CatalogController constructor.
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function index()
    {
        $request = ProductProjectionSearchRequest::of();
        $response = $this->client->execute($request);
        $products = $request->mapFromResponse($response);

//        return $this->render('some.html.twig', ['product' => var_export($products->toArray(), true)]);
        return $this->render('@Example/pop.html.twig', ['product' => $products]);
    }
}
