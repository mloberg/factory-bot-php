<?php

declare(strict_types=1);

namespace Mlo\FactoryBot\Storage;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;

class DoctrineStorage
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * Constructor
     *
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Save object to entity manager
     *
     * @param object $fixture
     */
    public function __invoke($fixture)
    {
        $metadata = $this->manager->getClassMetadata(get_class($fixture));
        $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

        if ($metadata->isEmbeddedClass) {
            return;
        }

        $this->manager->persist($fixture);
        $this->manager->flush();
        $this->manager->refresh($fixture);
    }
}
