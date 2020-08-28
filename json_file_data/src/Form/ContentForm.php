<?php

namespace Drupal\json_file_data\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Database\Database;

/**
 * Class ContentFrom.
 */
class ContentForm extends FormBase
{
    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'contentform';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $form['page_name'] = [
          '#type' => 'textfield',
          '#title' => $this->t('page Name'),
          '#description' => $this->t('Please enter page name in underscore i.e "common_header".'),
          '#maxlength' => 64,
          '#size' => 64,
          '#weight' => '0',
       ];
        $form['is_active'] = [
          '#type' => 'checkbox',
          '#title' => $this->t('Default Value'),
          '#description' => $this->t('Please select page is active or not'),
          '#weight' => '0',
        ];

        $form['submit'] = [
          '#type' => 'submit',
          '#value' => $this->t('Submit'),
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
        $conn = Database::getConnection();
        $conn->insert('json_name_tbl')->fields(
      array(
        'page_name' => $form_state->getValue('page_name'),
        'is_active' => $form_state->getValue('is_active'),
        'last_updated' => date('Y-m-d H:i:s'),
        'created_date' => date('Y-m-d H:i:s'),
      )
    )->execute();
        $url = Url::fromRoute('json_file_data.jsondatalisting');
        $form_state->setRedirectUrl($url);
    }
}
