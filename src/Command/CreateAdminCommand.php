<?php

namespace App\Command;

use App\Entity\Admin;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Crée un nouvel administrateur',
)]
class CreateAdminCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'Email de l\'admin')
            ->addArgument('password', InputArgument::REQUIRED, 'Mot de passe de l\'admin')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');

        // Vérifier si l'admin existe déjà
        $existingAdmin = $this->entityManager
            ->getRepository(Admin::class)
            ->findOneBy(['email' => $email]);
        
        if ($existingAdmin) {
            $io->error('Un administrateur avec cet email existe déjà !');
            return Command::FAILURE;
        }

        // Créer le nouvel admin
        $admin = new Admin();
        $admin->setEmail($email);
        $admin->setRoles(['ROLE_ADMIN']);
        
        // Hasher le mot de passe
        $hashedPassword = $this->passwordHasher->hashPassword($admin, $password);
        $admin->setPassword($hashedPassword);

        // Enregistrer en base
        $this->entityManager->persist($admin);
        $this->entityManager->flush();

        $io->success(sprintf(
            'Administrateur %s créé avec succès !',
            $email
        ));

        return Command::SUCCESS;
    }
}