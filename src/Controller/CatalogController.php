<?php
/**
 * Created by PhpStorm.
 * User: nsotiropoulos
 * Date: 13/04/2018
 * Time: 15:55
 */

namespace App\Controller;

use Commercetools\Core\Client;
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

        return $this->render('some.html.twig', ['product' => var_export($products->toArray(), true)]);
    }
}