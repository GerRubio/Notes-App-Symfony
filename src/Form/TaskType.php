<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class TaskType extends AbstractType 
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('title', TextType::class, array(
			'label' => 'Title: '
		))
		->add('content', TextareaType::class, array(
			'label' => 'Content: '
		))
		->add('priority', ChoiceType::class, array(
			'label' => 'Priority: ',
			'choices' => array(
				'High' => 'High',
				'Medium' => 'Medium',
				'Low' => 'Low'
			)
		))
		->add('hours', TextType::class, array(
			'label' => 'Budgeted hours: '
		))
		->add('submit', SubmitType::class, array(
			'label' => 'Save'
		));
	}
}