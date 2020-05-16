<?php

declare(strict_types=1);

namespace App\Controller\Form\Type;

use App\Controller\Form\FormHelper;
use App\Controller\Form\Type\Commemoratif\AlimentationFormType;
use App\Controller\Form\Type\Commemoratif\ComportementFormType;
use App\Controller\Form\Type\Commemoratif\ProblemesAuxYeuxFormType;
use App\Controller\Form\Type\Commemoratif\ProblemesCutanesFormType;
use App\Controller\Form\Type\Commemoratif\ProblemesDigestifsFormType;
use App\Controller\Form\Type\Commemoratif\ProblemesLocomoteursFormType;
use App\Controller\Form\Type\Commemoratif\ProblemesRespiratoiresFormType;
use App\Controller\Form\Type\Commemoratif\ProblemesUrinairesFormType;
use App\Controller\Form\Type\Commemoratif\TraitementPucesFormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Extension\Core\Type as Form;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;

final class CommemoratifFormType extends AbstractType
{
    use ParentActivatedValidationTrait;

    const GROUP_ENV_ANIMAUX_AUTRE = 'EnvironnementAnimauxAutre';
    const GROUP_ENV_CHANGEMENTS_AUTRE = 'EnvironnementChangementsAutre';
    const GROUP_GENERAL_DEJA_CONSULTE = 'GeneralDejaConsulte';

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $required = $options['required'] ?? true;
        $html5 = $options['html5'] ?? true;

        $defaults = [
            'html5' => $html5,
            'required' => $required,
        ];

        // Informations générales.
        $builder
            ->add('general_date', Form\DateType::class, [
                'label' => "Date de la demande",
                'data' => new \DateTimeImmutable(),
                'attr' => [
                    'placeholder' => "Saississez la date au format " . (new \DateTimeImmutable())->format('d/m/Y'),
                ],
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'required' => $required,
                'html5' => false,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => "Veuillez renseigner la date de la demande.",
                    ]),
                ],
            ])
            ->add('general_motif', Form\TextareaType::class, [
                'label' => "Motif de la consultation (expliquez en quelques mots vos inquiétudes et attentes)",
                'attr' => [
                    'placeholder' => "Renseignez le motif de consultation...",
                ],
                'required' => $required,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => "Veuillez renseigner le motif de consultation.",
                    ]),
                ],
            ])
            ->add('general_deja_consulte', Form\ChoiceType::class, [
                'label' => "Avez-vous déjà consulté pour ce motif auparavant",
                'expanded' => false,
                'required' => $required,
                'placeholder' => FormHelper::PLACEHOLDER_DEFAULT,
                'choices' => [
                    "Oui" => 1,
                    "Non" => 0,
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => "Veuillez préciser si vous avez déjà consulté pour ce motif auparavant.",
                    ]),
                ],
            ])
            ->add('general_deja_consulte_date', Form\DateType::class, [
                'label' => "Si oui, précisez la date",
                'data' => null,
                'attr' => [
                    'placeholder' => "Saississez la date au format " . (new \DateTimeImmutable())->format('d/m/Y'),
                ],
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'required' => false,
                'html5' => false,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => "Veuillez renseigner la date de consultation.",
                        'groups' => [self::GROUP_GENERAL_DEJA_CONSULTE],
                    ]),
                ],
            ])
        ;

        // Informations de contact.
        $builder->add('contact', ContactFormType::class, $defaults);

        // Informations sur l'animal.
        $builder->add('animal', AnimalFormType::class, $defaults);

        // Vaccination.
        $builder
            ->add('vaccination', Form\ChoiceType::class, [
                'label' => "Est-il ou elle vacciné ?",
                'expanded' => false,
                'required' => $required,
                'placeholder' => FormHelper::PLACEHOLDER_DEFAULT,
                'choices' => FormHelper::map([
                    "Jamais vacciné",
                    "Vaccination non régulière – dernière visite il y a moins de 3 ans",
                    "Vacciné régulièrement – suivi annuel par un vétérinaire",
                    "Ne sait pas",
                ]),
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => "Veuillez préciser si votre animal est vacciné ou non.",
                    ]),
                ],
            ])
        ;

        // Historique médical.
        $builder
            ->add('historique_pathologies', Form\TextareaType::class, [
                'label' => "Votre animal a-t-il souffert de pathologies importantes ou récidivantes auparavant ?",
                'attr' => [
                    'placeholder' => "Renseignez les détails ici...",
                ],
                'required' => false,
            ])
            ->add('historique_traitement', Form\TextareaType::class, [
                'label' => "Suit-il actuellement un traitement médical, autre qu’antiparasitaire ? Si oui, précisez si possible ?",
                'attr' => [
                    'placeholder' => "Renseignez les détails ici...",
                ],
                'required' => false,
            ])
        ;

        // Environnement.
        $builder
            ->add('environnement_animaux', Form\ChoiceType::class, [
                'label' => "Autres animaux dans le foyer",
                'expanded' => true,
                'multiple' => true,
                'required' => false,
                'choices' => [
                    "Chien" => "Chien",
                    "Chat" => "Chat",
                    "Autre (précisez)" => "Autre",
                ],
            ])
            ->add('environnement_animaux_autre', Form\TextType::class, [
                'label' => "Si autre, précisez",
                'attr' => [
                    'placeholder' => "Chèvre, Lama, ...",
                ],
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => "Veuillez préciser les autres animaux présents dans l'environnement de votre animal.",
                        'groups' => [self::GROUP_ENV_ANIMAUX_AUTRE],
                    ]),
                ],
            ])
            ->add('environnement_exterieur', Form\ChoiceType::class, [
                'label' => "Accès à l'extérieur",
                'choices' => FormHelper::map([
                    "Oui, avec contacts possibles avec d'autres animaux",
                    "Oui, sans aucun contacts possibles",
                    "Non, jamais",
                ]),
                'placeholder' => FormHelper::PLACEHOLDER_DEFAULT,
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => "Veuillez préciser si votre animal à accès à l'extérieur.",
                    ]),
                ],
            ])
            ->add('environnement_changements', Form\ChoiceType::class, [
                'label' => "Changements dans l’environnement ces dernières semaines",
                'expanded' => true,
                'multiple' => true,
                'required' => false,
                'choices' => [
                    "Voyage/Chenil/Chatterie" => "Voyage",
                    "Déménagement/Travaux" => "Déménagement",
                    "Temps de présence" => "Présence",
                    "Arrivée, départ, naissance" => "Arrivée",
                    "Stress important" => "Stress",
                    "Autre, précisez" => "Autre",
                ],
            ])
            ->add('environnement_changements_autre', Form\TextType::class, [
                'label' => "Si autre, précisez",
                'attr' => [
                    'placeholder' => "Chèvre, Lama, ...",
                ],
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => "Veuillez préciser les autres animaux présents dans l'environnement de votre animal.",
                        'groups' => [self::GROUP_ENV_CHANGEMENTS_AUTRE],
                    ]),
                ],
            ])
        ;

        // Alimentation.
        $builder->add('alimentation', AlimentationFormType::class, $defaults); 

        // Vermifuge.
        $builder
            ->add('vermifuge', Form\ChoiceType::class, [
                'label' => "À quand remonte le dernier vermifuge ?",
                'expanded' => false,
                'required' => $required,
                'placeholder' => FormHelper::PLACEHOLDER_DEFAULT,
                'choices' => FormHelper::map([
                    "Moins de 3 mois",
                    "Moins de 6 mois",
                    "Moins d'un an",
                    "Plus d'un an",
                ]),
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => "Veuillez à quand remonte le dernier vermifuge.",
                    ]),
                ],
            ])
        ;

        // Traitement contre les puces.
        $builder->add('traitement_puces', TraitementPucesFormType::class, $defaults);

        // Comportement.
        $builder->add('comportement', ComportementFormType::class, $defaults);

        // Probèmes digestifs.
        $builder->add('problemes_digestifs', ProblemesDigestifsFormType::class, $defaults);

        // Problèmes urinaires.
        $builder->add('problemes_urinaires', ProblemesUrinairesFormType::class, $defaults);

        // Problèmes respiratoires.
        $builder->add('problemes_respiratoires', ProblemesRespiratoiresFormType::class, $defaults);

        // Problèmes aux yeux.
        $builder->add('problemes_aux_yeux', ProblemesAuxYeuxFormType::class, $defaults);

        // Problèmes locomoteurs.
        $builder->add('problemes_locomoteurs', ProblemesLocomoteursFormType::class, $defaults);

        $builder->add('problemes_cutanes', ProblemesCutanesFormType::class, $defaults);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'groups' => [Constraint::DEFAULT_GROUP],
            'html5' => true,
            'label' => false,
            'placeholder_email' => null,
            'required' => true,
        ]);

        $this->doConfigureValidationGroupsOptions($resolver);
    }

    /**
     * {@inheritdoc}
     */
    protected function resolveValidationGroups(FormInterface $form, ?array $groups): ?array
    {
        if (
            $groups && $form->has('general_deja_consulte') &&
            "Oui" === $form->get('general_deja_consulte')->getData()
        ) {
            $groups[] = self::GROUP_GENERAL_DEJA_CONSULTE;
        }

        if (
            $groups && $form->has('environnement_animaux') &&
            "Autre" === $form->get('environnement_animaux')->getData() // @todo \in_array() ?
        ) {
            $groups[] = self::GROUP_ENV_ANIMAUX_AUTRE;
        }

        if (
            $groups && $form->has('environnement_changements') &&
            "Autre" === $form->get('environnement_changements')->getData() // @todo \in_array() ?
        ) {
            $groups[] = self::GROUP_ENV_CHANGEMENTS_AUTRE;
        }

        return \array_unique($groups);
    }
}
