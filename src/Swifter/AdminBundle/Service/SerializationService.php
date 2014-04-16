<?php

namespace Swifter\AdminBundle\Service;

use JMS\Serializer\SerializationContext;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SerializationService
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function serializeToJsonByGroup($entity, $serializationGroup)
    {
        $serializationContext = SerializationContext::create()->setGroups(array($serializationGroup));
        return $this->serializeToJsonByContext($entity, $serializationContext);
    }

    public function serializeToJsonByContext($entity, $serializationContext)
    {
        $serializer = $this->container->get('serializer');
        $json = $serializer->serialize($entity, 'json', $serializationContext);

        return $json;
    }

    public function deserializeFromJson($json, $className)
    {
        $serializer = $this->container->get('serializer');
        $entity = $serializer->deserialize($json, $className, 'json');

        return $entity;
    }
}