<?php

namespace App\Repository;

use App\Entity\Campus;
use App\Entity\Sorties;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * @extends ServiceEntityRepository<Campus>
 *
 * @method Sorties|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sorties|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sorties[]    findAll()
 * @method Sorties[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortiesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sorties::class);
    }


    public function findFilteredSorties(\App\Entity\FiltersSorties $oFilters, ?\Symfony\Component\Security\Core\User\UserInterface $oUser)
    {
        // Récupérer les critères de filtrage depuis l'objet $oFilters
        $campus = $oFilters->getCampus();
        $startDate = $oFilters->getDateDebut();
        $endDate = $oFilters->getDateFin();
        $nom = $oFilters ->getNomSortie();
        $nomOrganisateur = $oFilters ->isOrganisateur();
        $inscrit = $oFilters ->isInscrit();
        $pasInscrit = $oFilters ->isPasInscrit();
        $passe = $oFilters ->isPassees();
        // Ajouter d'autres critères de filtrage si nécessaire...

        // Construire la requête pour récupérer les sorties
        $queryBuilder = $this->createQueryBuilder('s')
            ->leftJoin('s.organisateur', 'o')
            ->leftJoin('s.users', 'p');

        if ($campus !== null) {
            $queryBuilder->andWhere('s.campus = :campus')
                ->setParameter('campus', $campus);
        }

        if ($startDate !== null) {
            $queryBuilder->andWhere('s.dateHeureDebut >= :startDate')
                ->setParameter('startDate', $startDate);
        }

        if ($endDate !== null) {
            $queryBuilder->andWhere('s.dateLimiteInscription <= :endDate')
                ->setParameter('endDate', $endDate);
        }

        if ($nom !== null) {
            $queryBuilder->andWhere('s.nom LIKE :nom')
                ->setParameter('nom', '%'.$nom.'%');
        }

        if ($nomOrganisateur) {
            $queryBuilder->andWhere('s.organisateur = :organisateur')
                ->setParameter('organisateur', $oUser);
        }

        if ($inscrit) {
            $queryBuilder->andWhere(':user MEMBER OF s.users')
                ->setParameter('user', $oUser);
        }

        if ($pasInscrit) {
            $queryBuilder->andWhere(':user NOT MEMBER OF s.users')
                ->setParameter('user', $oUser);
        }

        if ($passe) {
            $queryBuilder->andWhere('s.dateLimiteInscription < :now')
                ->setParameter('now', new \DateTime());
        }

        // Exécuter la requête
        $query = $queryBuilder->getQuery();

        // Retourner les résultats de la requête
        return $query->getResult();
    }

  //  public function findStillDisplayedSorties(Sorties $sorties): array
  //  {
  //      $today = new DateTime('-1 month');
// Récupérer les critères de filtrage depuis l'objet $oFilters
     //   $datepasse = $sorties->getDateLimiteInscription();

        // Ajouter d'autres critères de filtrage si nécessaire...

        // Construire la requête pour récupérer les sorties
     //   $queryBuilder = $this->createQueryBuilder('s')
        //    ->leftJoin('s.organisateur', 'o')
          //  ->leftJoin('s.users', 'p');

        //if ($campus !== null) {
          //  $queryBuilder->andWhere('s.campus = :campus')
            //    ->setParameter('campus', $campus);
        //}
 //   }












}
