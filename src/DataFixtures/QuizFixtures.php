<?php

namespace App\DataFixtures;

use App\Entity\Question;
use App\Entity\Proposition;
use App\Entity\Questionnaire;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class QuizFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $qr = new Questionnaire();
        $qr->setName("Test de connaissances front");
        $qr->setDifficulty("easy");

        $manager->persist($qr);
    //question 1
        $q1 = new Question();
        $q1->setTitle("Quel est le seul langage de programmation côté client?");
        $q1->setScore("2");
        $q1->setQuestionnaire($qr);
        $manager->persist($q1);

        $p1q1 = new Proposition();
        $p1q1->setText("HTML");
        $p1q1->setCorrect(false);
        $p1q1->setQuestion($q1);
        $manager->persist($p1q1);
        $p2q1 = new Proposition();
        $p2q1->setText("JavaScript");
        $p2q1->setCorrect(true);
        $p2q1->setQuestion($q1);
        $manager->persist($p2q1);

    //question 2
        $q2 = new Question();
        $q2->setTitle("Quelle balise permet d'afficher une image?");
        $q2->setScore("1");
        $q2->setQuestionnaire($qr);
        $manager->persist($q2);

        $p1q2 = new Proposition();
        $p1q2->setText("<div>");
        $p1q2->setCorrect(false);
        $p1q2->setQuestion($q2);
        $manager->persist($p1q2);
        $p2q2 = new Proposition();
        $p2q2->setText("<img>");
        $p2q2->setCorrect(true);
        $p2q2->setQuestion($q2);
        $manager->persist($p2q2);

        $manager->flush();
    }
}
