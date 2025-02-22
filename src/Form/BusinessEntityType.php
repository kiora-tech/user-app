<?php

namespace App\Form;

use App\Entity\BusinessEntity;
use App\Entity\Customer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class BusinessEntityType extends AbstractType
{
    public function __construct(private readonly RouterInterface $router)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('siret');
        if (!$options['customer'] instanceof Customer) {
            $builder->add('customer', EntityType::class, [
                'class' => Customer::class
            ]);
        }
        $builder->setAction($this->router->generate('app_business_entity_new', ['customer' => $options['customer']?->getId()]));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BusinessEntity::class,
            'customer' => null,
        ]);

        $resolver->setAllowedValues('customer', fn($value) => $value instanceof Customer || $value === null);
    }
}
