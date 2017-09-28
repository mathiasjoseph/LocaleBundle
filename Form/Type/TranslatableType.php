<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Miky\Bundle\LocaleBundle\Form\Type;


use Knp\DoctrineBehaviors\Model\Translatable\Translatable;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;

class TranslatableType extends AbstractType
{
    protected $locales;

    const REQUIRED_BY_CURRENT_LOCALE = "current_locale";

    const REQUIRED_BY_DEFAULT_LOCALE = "default_locale";

    const ALL_REQUIRED = "all_required";

    /**
     * @var \Symfony\Component\PropertyAccess\PropertyAccessor
     */
    protected $accessor;

    /**
     * @var string
     */
    protected $defaultLocale;

    /**
     * @var string
     */
    protected $currentLocale;


    public function __construct($locales, $defaultLocale, RequestStack $requestStack)
    {
        $this->locales = $locales;
        $this->accessor = PropertyAccess::createPropertyAccessor();
        $this->defaultLocale = $defaultLocale;
        $this->currentLocale = $requestStack->getCurrentRequest()->getLocale();
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!isset($options['options']['error_bubbling'])) {
            $options['options']['error_bubbling'] = $options['error_bubbling'];
        }

        foreach ($this->locales as $locale) {
            $builder
                ->add($options[$locale . '_name'], $options['type'], array_merge($options['options'], $options[$locale . '_options']));
        };

        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();
            /** @var Translatable $parentData */
            $parentData = $event->getForm()->getParent()->getData();
            foreach ($this->locales as $locale) {
                $translation = $parentData->findTranslationByLocale($locale, false);
                if ($translation != null) {
                    $value = $this->accessor->getValue($translation, $form->getPropertyPath());
                    $form->get($locale)->setData($value);
                }
            }

        });
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $parentData = $form->getParent()->getData();
            switch ($form->getConfig()->getOptions("required_type")){
                case self::REQUIRED_BY_CURRENT_LOCALE :
                    $this->accessor->setValue($parentData, $form->getPropertyPath(), $form->get($this->currentLocale)->getData());
                    break;
                default:
                    $this->accessor->setValue($parentData, $form->getPropertyPath(), $form->get($this->defaultLocale)->getData());
                    break;
            }

            foreach ($this->locales as $locale) {
                $v = $form->get($locale)->getData();
                if ($v != null) {
                    $translation = $parentData->translate($locale, false);
                   $this->accessor->setValue($translation, $form->getPropertyPath(), $v);
                }
            }
            $parentData->mergeNewTranslations();
        });
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['locales'] = $this->locales;
        switch ($options["required_type"]){
            case self::REQUIRED_BY_CURRENT_LOCALE :
                $options["required"] = false;
                $options[$this->currentLocale . '_options']['required'] = true;
                break;
            case self::REQUIRED_BY_DEFAULT_LOCALE:
                $options["required"] = false;
                $options[$this->defaultLocale . '_options']["required"] = true;
                break;
            case self::ALL_REQUIRED :
                $options["required"] = true;
                break;
        }
        foreach ($this->locales as $locale) {
            if (array_key_exists('required', $options[$locale . '_options']) && $options[$locale . '_options']['required'] == true){
                $view->vars['required_options'][$locale] = $options[$locale . '_options']['required'];
            }else{
                $view->vars['required_options'][$locale] = false;
            }


        };
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $default = array(
            'type' => TextType::class,
            'options' => array(),
            'error_bubbling' => false,
            "mapped" => false,
            "required" => false,
            "required_type" => self::REQUIRED_BY_CURRENT_LOCALE
        );
        foreach ($this->locales as $locale) {
            $default[$locale . "_options"] = array();
            $default[$locale . "_name"] = $locale;
        }
        $resolver->setDefaults($default);
        $resolver->setAllowedTypes('options', 'array');
        $resolver->setAllowedTypes('required_type', 'string');
        foreach ($this->locales as $locale) {
            $resolver->setAllowedTypes($locale . '_options', 'array');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'translatable';
    }
}
