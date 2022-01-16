<?php

namespace Classement\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ClassementController extends AbstractActionController
{
    /** @var EntityManager $entityManager */
    private $entityManager;

    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function indexAction()
    {
        $events = $this->entityManager->getRepository('Application\Entity\Event')->findAll();

        return new ViewModel(
            array(
                "events" => $events
            )
        );
    }

    public function rankingAction()
    {
        $eventId = (int) $this->params()->fromRoute('id');
        $type = $this->params()->fromQuery('type');

        $event = $this->entityManager->getRepository('Application\Entity\Event')->findOneBy(['id' => $eventId]);

        $criteria = ['event' => $event, 'hasRun' => true];
        if ($type && in_array($type, ['homme', 'femme'])) {
            $gender = $type === 'homme' ? 'male' : 'female';
            $criteria['sex'] = $gender;
        }

        $participants = $this->entityManager->getRepository('Application\Entity\Participant')->findBy(
            $criteria,
            ['runningTime' => 'ASC']
        );

        return new ViewModel(
            array(
                "event" => $event,
                "participants" => $participants,
                "type_ranking" => $type ?? 'général',
            )
        );
    }    
}
