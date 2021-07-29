<?php

namespace App\DataFixtures;

use App\Entity\Activite;
use App\Entity\Actualite;
use App\Entity\Admin;
use App\Entity\Agent;
use App\Entity\Categorie;
use App\Entity\Declaration;
use App\Entity\Entrepreneur;
use App\Entity\Facture;
use App\Entity\Produit;
use App\Entity\Temoignage;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Faker\Factory;
use Symfony\Component\Validator\Constraints\Date;

class AppFixtures extends Fixture
{
    private $encoder;
    private $faker;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create();
        // $product = new Product();
        // $manager->persist($product);


        // admin fixture
        $admin = new Admin();
        $admin->setNom('Anis')
            ->setPrenom('Brahmi')
            ->setCin($faker->biasedNumberBetween($min = 10000000, $max = 99999999))
            ->setEmail('admin@gmail.com')
            ->setPassword($this->encoder->encodePassword($admin, 'admin123'))
            ;
        $manager->persist($admin);

        //secteur
        $activite1 = new Activite();
        $activite2 = new Activite();
        $activite3= new Activite();
        $activite4= new Activite();
        $activite5 = new Activite();
        
        $activite1
            ->setNom("Secteur de l'industrie")
            ->setTaux(0.7)
            ->setRef(uniqid('Activite-'))
            ;
        $activite2
            ->setNom("Secteur de l'agriculture")
            ->setTaux(10)
            ->setRef(uniqid('Activite-'))
            ;
        $activite3
            ->setNom("Secteur du commerce")
            ->setTaux(20)
            ->setRef(uniqid('Activite-'))
            ;
        $activite4
            ->setNom("Secteur des services")
            ->setTaux(15)
            ->setRef(uniqid('Activite-'))
            ;
        $activite5
            ->setNom("Secteur de l’artisanat ou des métiers")
            ->setTaux(12)
            ->setRef(uniqid('Activite-'))
            ;
            $manager->persist($activite1);
            $manager->persist($activite2);
            $manager->persist($activite3);
            $manager->persist($activite4);
            $manager->persist($activite5);

        //agent fixture
        for ($ag=0; $ag < 20; $ag++) {
            $agent = new Agent();
            $agent->setNom($faker->name)
                ->setPrenom($faker->lastName)
                ->setCin($faker->biasedNumberBetween($min = 10000000, $max = 99999999))
                ->setEmail($faker->email)
                ->setEtat(true)
                ->setPassword($this->encoder->encodePassword($agent, 'agent123'))
                ;
            $manager->persist($agent);
        }

        //ajouter actualités
        for ($a=0; $a < 10; $a++) { 
            $actualite = new Actualite();
            $actualite
                ->setTitle($faker->sentence($nbWords = 6, $variableNbWords = true))
                ->setDescription($faker->paragraph)
                ->setRef(uniqid('Act-'))
                ->setImage($faker->randomElement(['https://www.dynamique-mag.com/wp-content/uploads/2b8b4313995737e29f938e99cc5eb9ff.jpg', 'https://www.mescertifications.com/wp-content/uploads/2019/08/managementstrategique.jpg', 'https://avocat-charlottedingaatipo.fr/wp-content/uploads/accomplishment-agreement-business-1249158-Pexels.jpg']))
                ;
            $manager->persist($actualite);
        }

        //faker entrepreneur
        for($i=0; $i<=10; $i++){
            $entrepreneur = new Entrepreneur();
            $entrepreneur
                ->setNom($faker->name)
                ->setPrenom($faker->lastName)
                ->setEmail($faker->email)
                ->setCin($faker->biasedNumberBetween($min = 10000000, $max = 99999999))
                ->setGenre($faker->randomElement(['femme', 'homme']))
                ->setDatenais(new \DateTime())
                ->setPaynais('Tunisie')
                ->setTel($faker->biasedNumberBetween($min = 10000000, $max = 99999999))
                ->setDateexpcin(new \DateTime())
                ->setVillenais($faker->randomElement($array = array('Ariana',
                                                                    'Béja',
                                                                    'Ben Arous',
                                                                    'Bizerte',
                                                                    'Gabès',
                                                                    'Gafsa',
                                                                    'Jendouba',
                                                                    'Kairouan',
                                                                    'Kasserine',
                                                                    'Kébili',
                                                                    'Kef',
                                                                    'Mahdia',
                                                                    'Manouba',
                                                                    'Médenine',
                                                                    'Monastir',
                                                                    'Nabeul',
                                                                    'Sfax',
                                                                    'Sidi Bouzid',
                                                                    'Siliana',
                                                                    'Sousse',
                                                                    'Tataouine',
                                                                    'Tozeur',
                                                                    'Tunis',
                                                                    'Zaghouan'
                                                                    )))
                ->setEtat(false)
                ->setPassword($this->encoder->encodePassword($entrepreneur, 'test123'))
                ;

            // fixture Categorie
            $categorie = new Categorie();
            $categorie
                ->setAdresse($faker->address)
                ->setCodepostale((int)$faker->postcode)
                ->setDomicile($faker->boolean($chanceOfGettingTrue = 50))
                ->setActivite($faker->randomElement([$activite1, $activite2, $activite3, $activite4, $activite5]))
                ->setEntrepreneur($entrepreneur)
                ;
            $manager->persist($categorie);

                $nbT = 0;
                for($j=0; $j<=20; $j++){
                    $facture = new Facture();
                    $facture
                        ->setClient($faker->name." ".$faker->lastName)
                        ->setDateFact(new \DateTime('now'))
                        ->setMf($faker->ean13)
                        ->setTva(7)
                        ->setEntrepreneur($entrepreneur)
                        ;

                        for($k=0; $k<=$faker->biasedNumberBetween($min = 10, $max = 20); $k++){
                            $produit = new Produit();
                            $produit
                                ->setNom($faker->word)
                                ->setNb($faker->biasedNumberBetween($min = 1, $max = 10))
                                ->setPrixUnitaire(round($faker->randomFloat(), 3))
                                ->setPrixTotal($produit->getPrixUnitaire() * $produit->getNb())
                                ;
                            $nbT+=$produit->getPrixTotal();
                            $manager->persist($produit);
                            $facture->addProduit($produit);                
                        }
                    $facture
                        ->setPrixTTC($nbT)
                        ->setPrixHT(($nbT*100)/(100 + $facture->getTva()))                        
                        ->setType($faker->randomElement($array = array ('facture','devis')))
                    ;
                    if ($facture->getType() == 'facture') {
                        $facture->setRef(uniqid('Fac-'));
                    }else{
                        $facture->setRef(uniqid('Dev-'));
                    }

                    $manager->persist($facture);
                }           
            
            // fixture déclaration
            $declaration1 = new Declaration();
            $declaration1
                ->setEntrepreneur($entrepreneur)
                ->setDateCr(new \DateTime('yesterday'))
                ->setDateEx(date_add(new \DateTime('yesterday'), date_interval_create_from_date_string('14 days')))
                ->setRef(uniqid('Dec-'))
                ;
            $manager->persist($declaration1);

            for ($d=0; $d <= 20; $d++) { 
                $declaration = new Declaration();
                if((bool) mt_rand(0, 1)){
                    $declaration
                    ->setEntrepreneur($entrepreneur)
                    ->setChiffre($faker->randomFloat())
                    ->setDateDec($faker->dateTime())
                    ->setDateCr($faker->dateTime())
                    ->setDateEx($faker->dateTime())
                    ->setPenalite($faker->randomFloat())
                    ->setCotisation($faker->randomFloat())
                    ->setEtat(true)
                    ->setTotalapayer($declaration->getPenalite()+$declaration->getCotisation())
                    ->setRef(uniqid('Dec-'))
                    ;
                }else{
                    $declaration
                    ->setEntrepreneur($entrepreneur)
                    ->setDateEx($faker->dateTime())
                    ->setDateCr($faker->dateTime())
                    ->setRef(uniqid('Dec-'))
                    ;
                }                
                $manager->persist($declaration);
            }

            // fixture temoignage
            $temoignage = new Temoignage();
            $temoignage
                ->setTitle($faker->sentence($nbWords = 6, $variableNbWords = true))
                ->setEntrepreneur($entrepreneur)
                ->setUrl($faker->randomElement(['<iframe width="600" height="400" src="https://www.youtube.com/embed/VIQHxeCtCZs" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>', '<iframe width="853" height="480" src="https://www.youtube.com/embed/jaS6o5jQLVw" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>', '<iframe width="853" height="480" src="https://www.youtube.com/embed/utQLvM6Y2Ts" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>', '<iframe width="853" height="480" src="https://www.youtube.com/embed/8MJNuEPciJ8" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>']))
                ->setRef(uniqid('Tem-'))
                ;
            $manager->persist($temoignage);
        }

        $manager->flush();
    }
}
