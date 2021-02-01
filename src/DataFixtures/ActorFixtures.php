<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Actor;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker;

class ActorFixtures extends Fixture implements DependentFixtureInterface
{   
    const ACTORS = [
    'Andrew Lincoln',
    'Norman Reedus',
    'Lauren Cohan',
    'Danai Gurira',
];   public function getDependencies()  
    {
        return [ProgramFixtures::class];  
    }

    public function load(ObjectManager $manager)
    {
        foreach (self::ACTORS as $key => $actorName) {  
            $actor = new Actor();  
            $actor->setName($actorName); 
            for ($i = 0; $i <= 4; $i++){
                $actor->addProgram($this->getReference('program_' . rand(0,5)), $actor);
            }
        
            $manager->persist($actor);
            $this->addReference('actor_' . $key, $actor);  
        }  
        

        $faker=Faker\Factory::create('fr_FR');

        for ($i = 0; $i <= 50; $i++){
            $actor = new Actor();
            $actor->setName($faker->name);
            $actor->addProgram($this->getReference('program_' . rand(0, 5), $actor));
            $manager->persist($actor);
        }
        $manager->flush();
    }


 
}
