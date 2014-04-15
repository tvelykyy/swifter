<?php

namespace Swifter\AdminBundle\Service;

class SerializationService
{
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

    public function deserializeFromRequest($className)
    {
        $requestBody = $this->get('request')->getContent();
        $entity = $this->deserializeFromJson($requestBody, $className);

        return $entity;
    }

    public function deserializeFromJson($json, $className)
    {
        $serializer = $this->container->get('serializer');
        $entity = $serializer->deserialize($json, $className, 'json');

        return $entity;
    }
}