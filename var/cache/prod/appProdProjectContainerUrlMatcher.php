<?php

use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RequestContext;

/**
 * This class has been auto-generated
 * by the Symfony Routing Component.
 */
class appProdProjectContainerUrlMatcher extends Symfony\Bundle\FrameworkBundle\Routing\RedirectableUrlMatcher
{
    public function __construct(RequestContext $context)
    {
        $this->context = $context;
    }

    public function match($pathinfo)
    {
        $allow = array();
        $pathinfo = rawurldecode($pathinfo);
        $trimmedPathinfo = rtrim($pathinfo, '/');
        $context = $this->context;
        $request = $this->request;
        $requestMethod = $canonicalMethod = $context->getMethod();
        $scheme = $context->getScheme();

        if ('HEAD' === $requestMethod) {
            $canonicalMethod = 'GET';
        }


        if (0 === strpos($pathinfo, '/api/v1')) {
            if (0 === strpos($pathinfo, '/api/v1/article')) {
                // app_api_v1_article_new
                if ('/api/v1/article/new' === $pathinfo) {
                    if ('POST' !== $canonicalMethod) {
                        $allow[] = 'POST';
                        goto not_app_api_v1_article_new;
                    }

                    return array (  '_controller' => 'AppBundle\\Controller\\Api\\v1\\ArticleController::newAction',  '_route' => 'app_api_v1_article_new',);
                }
                not_app_api_v1_article_new:

                // app_api_v1_article_get
                if (preg_match('#^/api/v1/article/(?P<id>[^/]++)$#s', $pathinfo, $matches)) {
                    if ('GET' !== $canonicalMethod) {
                        $allow[] = 'GET';
                        goto not_app_api_v1_article_get;
                    }

                    return $this->mergeDefaults(array_replace($matches, array('_route' => 'app_api_v1_article_get')), array (  '_controller' => 'AppBundle\\Controller\\Api\\v1\\ArticleController::getAction',));
                }
                not_app_api_v1_article_get:

                // app_api_v1_article_delete
                if (preg_match('#^/api/v1/article/(?P<id>[^/]++)$#s', $pathinfo, $matches)) {
                    if ('DELETE' !== $canonicalMethod) {
                        $allow[] = 'DELETE';
                        goto not_app_api_v1_article_delete;
                    }

                    return $this->mergeDefaults(array_replace($matches, array('_route' => 'app_api_v1_article_delete')), array (  '_controller' => 'AppBundle\\Controller\\Api\\v1\\ArticleController::deleteAction',));
                }
                not_app_api_v1_article_delete:

            }

            // app_api_v1_article_getallarticle
            if ('/api/v1/getallarticle' === $pathinfo) {
                if ('GET' !== $canonicalMethod) {
                    $allow[] = 'GET';
                    goto not_app_api_v1_article_getallarticle;
                }

                return array (  '_controller' => 'AppBundle\\Controller\\Api\\v1\\ArticleController::getallarticleAction',  '_route' => 'app_api_v1_article_getallarticle',);
            }
            not_app_api_v1_article_getallarticle:

            // app_api_v1_topic_getalltopic
            if ('/api/v1/getalltopic' === $pathinfo) {
                if ('GET' !== $canonicalMethod) {
                    $allow[] = 'GET';
                    goto not_app_api_v1_topic_getalltopic;
                }

                return array (  '_controller' => 'AppBundle\\Controller\\Api\\v1\\TopicController::getalltopicAction',  '_route' => 'app_api_v1_topic_getalltopic',);
            }
            not_app_api_v1_topic_getalltopic:

            if (0 === strpos($pathinfo, '/api/v1/topic')) {
                // app_api_v1_topic_new
                if ('/api/v1/topic/new' === $pathinfo) {
                    if ('POST' !== $canonicalMethod) {
                        $allow[] = 'POST';
                        goto not_app_api_v1_topic_new;
                    }

                    return array (  '_controller' => 'AppBundle\\Controller\\Api\\v1\\TopicController::newAction',  '_route' => 'app_api_v1_topic_new',);
                }
                not_app_api_v1_topic_new:

                // app_api_v1_topic_get
                if (preg_match('#^/api/v1/topic/(?P<id>[^/]++)$#s', $pathinfo, $matches)) {
                    if ('GET' !== $canonicalMethod) {
                        $allow[] = 'GET';
                        goto not_app_api_v1_topic_get;
                    }

                    return $this->mergeDefaults(array_replace($matches, array('_route' => 'app_api_v1_topic_get')), array (  '_controller' => 'AppBundle\\Controller\\Api\\v1\\TopicController::getAction',));
                }
                not_app_api_v1_topic_get:

                // app_api_v1_topic_delete
                if (preg_match('#^/api/v1/topic/(?P<id>[^/]++)$#s', $pathinfo, $matches)) {
                    if ('DELETE' !== $canonicalMethod) {
                        $allow[] = 'DELETE';
                        goto not_app_api_v1_topic_delete;
                    }

                    return $this->mergeDefaults(array_replace($matches, array('_route' => 'app_api_v1_topic_delete')), array (  '_controller' => 'AppBundle\\Controller\\Api\\v1\\TopicController::deleteAction',));
                }
                not_app_api_v1_topic_delete:

            }

        }

        // homepage
        if ('' === $trimmedPathinfo) {
            if (substr($pathinfo, -1) !== '/') {
                return $this->redirect($pathinfo.'/', 'homepage');
            }

            return array (  '_controller' => 'AppBundle\\Controller\\DefaultController::indexAction',  '_route' => 'homepage',);
        }

        throw 0 < count($allow) ? new MethodNotAllowedException(array_unique($allow)) : new ResourceNotFoundException();
    }
}
