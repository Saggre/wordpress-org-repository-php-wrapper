<?php

namespace Saggre\WordPress\Repository\Serializer;

use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;

class XmlDeserializer extends Serializer
{
    public function __construct()
    {
        parent::__construct(
            [
                new ArrayDenormalizer(),
                new DateTimeNormalizer(),
                new PropertyNormalizer(
                    null,
                    new MetadataAwareNameConverter(new ClassMetadataFactory(new AttributeLoader())),
                    new PropertyInfoExtractor([], [new PhpDocExtractor(), new ReflectionExtractor()])
                ),
            ],
            [
                new XmlEncoder()
            ]
        );
    }
}

{
}