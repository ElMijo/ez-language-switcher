<?php

/**
 * Este archivo es un servicio que permite obtener los diferentes lenguages
 * que se encuentran disponibles en la instalación de eZ Publish
 *
 */
namespace SmarterSolutions\EzComponents\EzLanguageSwitcherBundle\Services;


class LanguageSwitcherService
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * @var \eZ\Publish\Core\MVC\Symfony\Routing\ChainRouter
     */
    private $router;

    /**
     * @var \eZ\Publish\Core\Helper\TranslationHelper
     */
    private $translation_helper;

    /**
     * @var \eZ\Publish\Core\MVC\Symfony\Routing\Generator\RouteReferenceGenerator
     */
    private $route_reference_generator;

    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    private $request;

    /**
     * @var \Doctrine\DBAL\Driver\Connection
     */
    private $connection;

    /**
     * Arreglo que contiene la conversión de lenguages estandar con los de eZ Publish
     * @var array
     */
    private $conversion_map;

    function __construct($container)
    {
        $this->container = $container;
        $this->router = $container->get('router');
        $this->translation_helper = $container->get('ezpublish.translation_helper');
        $this->route_reference_generator = $container->get('ezpublish.route_reference.generator');
        $this->request = $container->get('request');
        $this->connection = $container->get('ezpublish.persistence.connection');        
        $this->conversion_map = $container->getParameter('ezpublish.locale.conversion_map');
    }

    /**
     * Permite obtener el lenguage actual con la codificación de eZ Publish
     * @return string
     */
    public function getCurrentEzLocale()
    {
        return array_search($this->request->get( '_locale'),$this->conversion_map);
    }

    /**
     * Permite obtener los lenguajes habilitados para los contenidos
     * @return array
     */
    public function getContentLanguages()
    {
        $statement = $this->connection->prepare("SELECT locale, name FROM ezcontent_language");
        $statement->execute();
        return $statement->fetchAll();
    }

    private function isValidRouter()
    {
        $isValid = false;
        if ($this->request->attributes->has('_route'))
        {
            $_router = $this->request->attributes->get('_route');
            $semantic_pathinfo = $this->request->attributes->get('semanticPathinfo');

            $isValid = $_router != 'ez_legacy' || $_router == 'ez_legacy' && in_array($semantic_pathinfo,$this->getValidEzLegacyPath());
        }
        return $isValid;
    }

    /**
     * Permite obtener los routers de ez_legacy que son traducibles.
     * @return array()
     */
    private function getValidEzLegacyPath()
    {
        return array(
            '/user/register',
            '/user/forgotpassword'
        );
    }


    /**
     * Permite obtener el objeto que peritira generar la url con los routers de eZ Publish
     * @param  [type] $location [description]
     * @return [type]           [description]
     */
    private function getRefRouter($location)
    {
        $isValidRouter = $this->isValidRouter();

        return $this->route_reference_generator->generate(
            $isValidRouter ? $this->request->attributes->get('_route') : $location,
            $isValidRouter ? $this->request->attributes->get('_route_params') : array()
        );
    }
}