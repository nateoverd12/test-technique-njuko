<?php

namespace Participant\Form;

use Application\Entity\Event;
use Doctrine\Persistence\ObjectManager;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class ParticipantForm extends Form implements InputFilterProviderInterface
{
    /** @var  ObjectManager */
    protected $objectManager;

    public function __construct($name = 'participant-form')
    {
        parent::__construct($name);
    }

    public function init()
    {
        $this->setAttribute('class', 'form-horizontal');

        $this->add([
            'name' => 'id',
            'type' => 'Hidden',
        ]);

        $this->add([
            'name' => 'bibNumber',
            'type' => 'Hidden',
        ]);

        $this->add([
            'name' => 'time',
            'type' => 'Text',
            'options'=> [
                'label'  => 'Chrono de la course',
            ],
            'attributes' => [
                'placeholder' => '__:__:__',
                'pattern' => "\d{1,3}:\d{1,2}:\d{1,2}"
            ],
        ]);

        $this->add([
            'name'    => 'firstname',
            'type'    => 'Text',
            'options' => [
                'label' => 'Prénom',
            ],
        ]);

        $this->add([
            'name'    => 'lastname',
            'type'    => 'Text',
            'options' => [
                'label' => 'Nom',
            ],
        ]);

        $this->add([
            'name'    => 'sex',
            'type'    => 'Radio',
            'options'    => [
                'label'            => 'Sexe',
                'label_attributes' => ['class' => 'checkbox-inline'],
                'value_options'    => [
                    [
                        'value'      => 'male',
                        'label'      => 'Homme',
                    ],
                    [
                        'value'      => 'female',
                        'label'      => 'Femme',
                    ]
                ]
            ],
        ]);

        $this->add([
            'type' => 'DoctrineModule\\Form\\Element\\ObjectSelect',
            'name' => 'event',
            'required' => true,
            'attributes' => [
                'id' => 'selectEvent',
                'multiple' => false,
                'value' => null,
            ],
            'options' => [
                'label' => 'Evènement concerné',
                'object_manager' => $this->getObjectManager(),
                'target_class' => Event::class,
                'property' => 'id',
                'is_method' => true,
                'find_method' => [
                    'name' => 'findBy',
                    'params' => [
                        'criteria' => [],
                        'orderBy' => ['name' => 'ASC'],
                    ],
                ],
                'empty_option' => '--- Selectionner l\'évènement ---',
                'label_generator' => function (Event $entity) {
                    return $entity->getName();
                }
            ],
        ]);

        $this->add([
            'name'       => 'submit',
            'type'       => 'submit',
            'attributes' => [
                'class' => 'btn btn-primary',
                'value' => 'Sauvegarder'
            ],
        ]);
    }

    /**
     * @return ObjectManager
     */
    public function getObjectManager()
    {
        return $this->objectManager;
    }

    /**
     * @param ObjectManager $objectManager
     */
    public function setObjectManager(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function getInputFilterSpecification()
    {
        return [
            'time' => [
                'required' => false,
                'validators' => [
                    [
                        "name" => 'Zend\Validator\Regex',
                        "options" => [
                            "pattern" => '/^\d{1,3}:\d{1,2}:\d{1,2}$/',
                        ]
                    ]
                ]
            ]
        ];
    }
}