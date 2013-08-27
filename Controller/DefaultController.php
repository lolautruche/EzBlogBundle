<?php

namespace Acme\EzBlogBundle\Controller;

use eZ\Bundle\EzPublishCoreBundle\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function listBlogPostsAction( $locationId )
    {
        $response = new Response();
        // Set a 1h max age in cache
        $response->setSharedMaxAge( 3600 );
        // Make the response location cache aware for the reverse proxy
        $response->headers->set( 'X-Location-Id', $locationId );

        $request = $this->getRequest();
        $offset = $request->query->get( 'offset', 0 );
        // TODO: Make this limit configurable.
        $limit = 10;

        // Use Public API to load current location/content and its children (posts)
        // TODO: We might use the search API instead
        $repository = $this->getRepository();
        $locationService = $repository->getLocationService();
        $contentService = $repository->getContentService();
        $location = $locationService->loadLocation( $locationId );
        $content = $contentService->loadContentByContentInfo( $location->getContentInfo() );
        $children = $locationService->loadLocationChildren( $location, $offset, $limit );

        return $this->render(
            'AcmeEzBlogBundle:full:blog.html.twig',
            array(
                'location' => $location,
                'content' => $content,
                'posts' => $children
            ),
            $response
        );
    }
}
