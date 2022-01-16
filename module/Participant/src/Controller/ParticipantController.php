<?php

namespace Participant\Controller;

use Doctrine\ORM\EntityManager;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ParticipantController extends AbstractActionController
{

    /** @var EntityManager $entityManager */
    private $entityManager;
    private $formElementManager;

    public function __construct($entityManager, $formElementManager)
    {
        $this->entityManager = $entityManager;
        $this->formElementManager = $formElementManager;
    }

    public function indexAction()
    {
        return new ViewModel();
    }

    public function listAction()
    {

        $participants = $this->entityManager->getRepository('Application\Entity\Participant')->findAll();

        return new ViewModel(
            array(
                "participants" => $participants
            )
        );

    }

    public function participantFormAction(){

        /** @var \Zend\Form\Form $form */
        $form = $this->formElementManager->get('participant_form');

        $id = (int) $this->params()->fromRoute('id', 0);

        /** @var \Application\Entity\Participant $participant */
        if (0 !== $id) {
            try {
                $participant = $this->entityManager->getRepository('Application\Entity\Participant')->find($id);

                if (null !== $participant->getBibNumber()) {
                    $form->remove('event');
                }

                if ($runningTime = $participant->getRunningTime()) {
                    $form->get('time')->setValue($runningTime);
                }

                $form->bind($participant);
            } catch (\Exception $e) {
                return $this->redirect()->toRoute('participant/list');
            }
        }

        /** @var Request $request */
        $request = $this->getRequest();

        if (!$request->isPost()) {
            return ['form' => $form];
        }

        $form->setData($request->getPost());

        if (!$form->isValid()) {
            return ['form' => $form];
        } else {
            $participant = $form->getData();

            $time = $form->get('time')->getValue();
            $participant->setRunningTime($time);

            $this->entityManager->persist($participant);
            $this->entityManager->flush();

            return $this->redirect()->toRoute('participant/list');
        }
    }

    public function generateBibNumbersAction()
    {
        /** @var Participant[] $participants */
        $participants = $this->entityManager->getRepository('Application\Entity\Participant')->findBy(
            [],
            ['id' => 'ASC']
        );

        $bibIndexByEvents = [];

        foreach ($participants as $participant) {
            $eventId = $participant->getEvent()->getId();

            if (!array_key_exists($eventId, $bibIndexByEvents)) {
                $bibIndexByEvents[$eventId] = 0;
            }

            $newBib = $bibIndexByEvents[$eventId] + 1; 
            $bibIndexByEvents[$eventId] = $newBib;
            
            if ($participant->getBibNumber() !== $newBib) {
                $participant->setBibNumber($newBib);

                $this->entityManager->persist($participant);
            }
        }

        $this->entityManager->flush();

        return $this->redirect()->toRoute('participant/list');

    }

    public function deleteAction(){

        $id = (int) $this->params()->fromRoute('id', 0);

        /** @var \Application\Entity\Participant $participant */
        if (0 !== $id) {
            try {
                $participant = $this->entityManager->getRepository('Application\Entity\Participant')->find($id);
            } catch (\Exception $e) {
                return $this->redirect()->toRoute('participant/list');
            }
        }

        $this->entityManager->remove($participant);
        $this->entityManager->flush();

        return $this->redirect()->toRoute('participant/list');

    }
}
