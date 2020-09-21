<?php

namespace Drupal\json_file_data\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Database\Database;
use Drupal\Core\Language\Language;
use Symfony\Component\HttpFoundation\RedirectResponse;


/**
 * Class DefaultForm.
 */
class FilterForm extends FormBase
{
    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'filterform_jsondata';
    }

    /**
     * {@inheritdoc}
     */

    public function buildForm(array $form, FormStateInterface $form_state,$jkv_id = NULL)
    {

        $form['form'] = [
            '#type'  => 'fieldset',
            '#title' => $this->t('Filter'),
            '#open'  => true,
            '#prefix' => '<div class="container-inline">',
            '#suffix' => '</div>',
        ];

        $form['form']['key'] = [
            '#title' => 'Key',
            '#type'  => 'search',
            '#attributes' =>array('placeholder' => t('Enter Key name')),
            '#maxlength' => 64,
            '#size' => 32,
        ];

        $langcodes = \Drupal::languageManager()->getLanguages();

        foreach ($langcodes as $key => $value) {
            $lang_id[] = $key;
            $lang_name[] =$value->getName();
        }
        $langlist=array_combine($lang_id,$lang_name);

        $form['form']['lang_id'] = [
        '#title' => $this->t('Select Language'),
        '#type' => 'select',
        '#empty_value' => '',
        '#empty_option' => '- Select a value -',
        '#default_value' => (isset($record['lang_id']) && $jkv_id) ? $record['lang_id']:'',
        '#options' => $langlist,
        ];

        // content id code list

        $query = \Drupal::database()->select('json_name_tbl', 'cont');
        $query->fields('cont', ['json_name_id', 'page_name']);
        $result = $query->execute();
        while ($row = $result->fetchAssoc()) {
                $json_name_id[] = $row['json_name_id'];
                $page_name[] = $row['page_name'];
        }
        $json_name_id[] = '';
        $page_name[] = '';
        $JSONList=array_combine($json_name_id,$page_name);

        $form['form']['jn_id'] = [
            '#title' => $this->t('Select JSON name'),
            '#type' => 'select',
            '#empty_value' => '',
            '#empty_option' => '- Select a value -',
            '#default_value' => (isset($record['jn_id']) && $jkv_id) ? $record['jn_id']:'',
            '#options' => $JSONList,
            ];


        $form['form']['submit'] = [
            '#type'  => 'submit',
            '#value' => $this->t('Filter'),
        ];

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        foreach ($form_state->getValues() as $key => $value) {
            // @TODO: Validate fields.
        }
        parent::validateForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */

    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $field = $form_state->getValues();
        $key = $field["key"];
        $lang_id = $field["lang_id"];
        $json_file_name = $field["jn_id"];
        $url = \Drupal\Core\Url::fromRoute('json_file_data.jsondatalisting')
              ->setRouteParameters(array('key'=>$key,'lang_id'=>$lang_id,'json_file_name'=>$json_file_name));
        $form_state->setRedirectUrl($url);
    }
}
