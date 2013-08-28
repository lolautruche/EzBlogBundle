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
        $response->setVary( 'X-User-Hash' );

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

    public function showBlogPostAction( $locationId, $viewType, $layout = false, array $params = array() )
    {
        // We need the author, whatever the view type is.
        $repository = $this->getRepository();
        $location = $repository->getLocationService()->loadLocation( $locationId );
        $author = $repository->getUserService()->loadUser( $location->getContentInfo()->ownerId );

        // Delegate view rendering to the original ViewController (makes it possible to continue using defined template rules)
        // We just add "author" to the list of variables exposed to the final template
        return $this->get( 'ez_content' )->viewLocation(
            $locationId,
            $viewType,
            $layout,
            array( 'author' => $author )
        );
    }
}
