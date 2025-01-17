<?php

namespace App\Test\Controller;

use App\Entity\Remise;
use App\Repository\RemiseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RemiseControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private RemiseRepository $repository;
    private string $path = '/remise/';
    private EntityManagerInterface $manager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Remise::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Remise index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'remise[montant]' => 'Testing',
            'remise[pourcentage]' => 'Testing',
            'remise[montantaprespourcentage]' => 'Testing',
        ]);

        self::assertResponseRedirects('/remise/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Remise();
        $fixture->setMontant('My Title');
        $fixture->setPourcentage('My Title');
        $fixture->setMontantaprespourcentage('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Remise');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Remise();
        $fixture->setMontant('My Title');
        $fixture->setPourcentage('My Title');
        $fixture->setMontantaprespourcentage('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'remise[montant]' => 'Something New',
            'remise[pourcentage]' => 'Something New',
            'remise[montantaprespourcentage]' => 'Something New',
        ]);

        self::assertResponseRedirects('/remise/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getMontant());
        self::assertSame('Something New', $fixture[0]->getPourcentage());
        self::assertSame('Something New', $fixture[0]->getMontantaprespourcentage());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Remise();
        $fixture->setMontant('My Title');
        $fixture->setPourcentage('My Title');
        $fixture->setMontantaprespourcentage('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/remise/');
    }
}
