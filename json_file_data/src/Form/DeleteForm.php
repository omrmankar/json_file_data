<?php

namespace Drupal\json_file_data\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Url;
use Drupal\Core\Render\Element;
/**
 * Class DeleteForm.
 *
 * @package Drupal\json_file_data\Form
 */
class DeleteForm extends ConfirmFormBase {


    /**
     * {@inheritdoc}
     */
    public function getFormId() {
      return 'delete_form';
    }

    public $jkv_id;

    public function getQuestion() {
      return t('Do you want to delete %jkv_id?', array('%jkv_id' => $this->jkv_id));
    }

    public function getCancelUrl() {
      return new Url('json_file_data.jsondatalisting');
    }
    public function getDescription() {
      return t('Only do this if you are sure!');
    }

    /**
     * {@inheritdoc}
     */
    public function getConfirmText() {
      return t('Delete it!');
    }

    /**
     * {@inheritdoc}
     */
    public function getCancelText() {
      return t('Cancel');
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state, $jkv_id = NULL) {

      $this->jkv_id = $jkv_id;
      return parent::buildForm($form, $form_state);
    }

    /**
      * {@inheritdoc}
      */
    public function validateForm(array &$form, FormStateInterface $form_state) {
      parent::validateForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {

        $query = \Drupal::database();
        $query->delete('json_key_val_tbl')
              //->fields($field)
              ->condition('jkv_id',$this->jkv_id)
              ->execute();
              \Drupal::messenger()->addMessage("succesfully Deleted ID is $this->jkv_id.");
        $form_state->setRedirect('json_file_data.jsondatalisting');
    }


}
