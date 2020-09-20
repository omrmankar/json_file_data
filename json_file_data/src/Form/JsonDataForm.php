<?php

namespace Drupal\json_file_data\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Database\Database;
use Drupal\Core\Language\Language;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\file\Entity\File;


/**
 * Class DefaultForm.
 */
class JsonDataForm extends FormBase
{
    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'JsonDataForm';
    }

    /**
     * {@inheritdoc}
     */

    public function buildForm(array $form, FormStateInterface $form_state,$jkv_id = NULL)
    {
      $conn = Database::getConnection();
      $record = array();
      if (isset($jkv_id)) {
          $query = $conn->select('json_key_val_tbl', 'm')
              ->condition('jkv_id', $jkv_id)
              ->fields('m');
          $record = $query->execute()->fetchAssoc();
      }
     // $form_state->disableCache();
    //  print_r($record['key_value_img']);
    //  exit();

      $form['jkv_id'] = [
        '#type' => 'hidden',
        '#value' => $record['jkv_id'],
      ];

      $form['json_key'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Data Key'),
        '#description' => $this->t('Please enter data key.'),
        '#maxlength' => 64,
        '#size' => 64,
        '#default_value' => (isset($record['json_key']) && $jkv_id) ? $record['json_key']:'',
      ];

      $form['select_type'] = [
        '#type' => 'radios',
        '#title' => t('Select Type Of Value'),
        '#options' => [
                       'textfield'=>t('TextField'),
                       'textarea'=>t('Textarea'),
                       'image'=>t('Image'),
                     ],
         '#disabled' => (isset($record['select_type']) && $jkv_id) ? $record['select_type']:'',
         '#attributes' => [
          'name' => 'field_select',
        ],
        '#default_value' => (isset($record['select_type']) && $jkv_id) ? $record['select_type']:'',
      ];

      $form['key_value_fmt'] = [
          '#type' => 'text_format',
          '#title' => $this->t('Data Value Formatted'),
          '#format' => 'full_html',
          '#description' => $this->t('Please enter value of you key.'),
          '#default_value' => (isset($record['key_value_fmt']) && $jkv_id) ? $record['key_value_fmt']:'',
          '#states' => [
            //show this textfield only if the radio 'other' is selected above
            'visible' => [
              //don't mistake :input for the type of field. You'll always use
              //:input here, no matter whether your source is a select, radio or checkbox element.
              ':input[name="field_select"]' => ['value' => 'textarea'],
          ],
          ],
      ];


       $form['key_value_text'] = [
          '#type' => 'textfield',
          '#title' => $this->t('Data Key Textfield'),
          '#description' => $this->t('Please enter data key.'),
          '#maxlength' => 64,
          '#size' => 64,
          '#default_value' => (isset($record['key_value_text']) && $jkv_id) ? $record['key_value_text']:'',
          '#states' => [
          //show this textfield only if the radio 'other' is selected above
          'visible' => [
            //don't mistake :input for the type of field. You'll always use
            //:input here, no matter whether your source is a select, radio or checkbox element.
            ':input[name="field_select"]' => ['value' => 'textfield'],
          ],
          ],
      ];

        $form['fieldset_classic'] = [
          '#type' => 'fieldset',
          '#title' => $this->t('Image'),
          '#states' => [
            //show this textfield only if the radio 'other' is selected above
            'visible' => [
              //don't mistake :input for the type of field. You'll always use
              //:input here, no matter whether your source is a select, radio or checkbox element.
              ':input[name="field_select"]' => ['value' => 'image'],
            ],
          ],
        ];
      //  $values['fids'][0] = $record['key_value_img']->target_id;

        $form['fieldset_classic']['key_value_img'] = [
          '#title' => t('Image Value'),
          '#type' => 'managed_file',
          '#upload_location' => 'public://jsondata/',
          '#multiple' => FALSE,
          //'#required' => TRUE,
          '#field_prefix' => '<div id="mtrend-wrapper">',
          '#field_suffix' => '</div>',
          '#default_value' => [(isset($record['key_value_img']) && $jkv_id) ? $record['key_value_img']:''],
          '#upload_validators' => [
            'file_validate_extensions' => ['png gif jpg jpeg jfif svg'],
            'file_validate_size' => [25600000],
            //'file_validate_image_resolution' => array('800x600', '400x300'),
            'disable-refocus' => FALSE,
          ],
      ];

      $form['fieldset_classic']['key_value_img_alt'] = [
          '#title' => $this->t('Alternative text'),
          '#description' => $this->t('Short description of the image used by screen readers and displayed when the image is not loaded. This is important for accessibility.'),
          '#type' => 'textfield',
          //'#required' => TRUE,
          '#required_error' => $this->t('Alternative text is required.<br />(Only in rare cases should this be left empty. To create empty alternative text, enter <code>""</code> — two double quotes without any content).'),
          '#default_value' => (isset($record['key_value_img_alt']) && $jkv_id) ? $record['key_value_img_alt']:'',
          '#maxlength' => 2048,
      ];

       $langcodes = \Drupal::languageManager()->getLanguages();

        foreach ($langcodes as $key => $value) {
              $lang_id[] = $key;
              $lang_name[] =$value->getName();
        }
        $langlist=array_combine($lang_id,$lang_name);

        $form['lang_id'] = [
          '#title' => $this->t('Select Language'),
          '#type' => 'select',
          '#empty_value' => '',
          '#empty_option' => '- Select a value -',
          '#default_value' => (isset($record['lang_id']) && $jkv_id) ? $record['lang_id']:'',
          '#options' => $langlist,
        ];

      //code lang id

      // content id code list

      $query = \Drupal::database()->select('json_name_tbl', 'cont');
      $query->fields('cont', ['json_name_id', 'page_name']);
      $result = $query->execute();
      while ($row = $result->fetchAssoc()) {
              $json_name_id[] = $row['json_name_id'];
              $page_name[] = $row['page_name'];
      }

        $JSONList=array_combine($json_name_id,$page_name);


      $form['jn_id'] = [
          '#title' => $this->t('Select JSON name'),
          '#type' => 'select',
          '#empty_value' => '',
          '#empty_option' => '- Select a value -',
          '#default_value' => (isset($record['jn_id']) && $jkv_id) ? $record['jn_id']:'',
          '#options' => $JSONList,
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

       $keysvalues = $form_state->getUserInput();



      $key_value_fmt_val = isset($keysvalues['key_value_fmt']['value']) ? $keysvalues['key_value_fmt']['value'] : '';
      $key_value_img_alt = isset($keysvalues['key_value_img_alt']) ? $keysvalues['key_value_img_alt'] : '';
      $key_value_text = isset($keysvalues['key_value_text']) ? $keysvalues['key_value_text'] : '';
      $key_value_img = isset($keysvalues['key_value_img']['fids']) ?  $keysvalues['key_value_img']['fids'] : '';

      /* Fetch the array of the file stored temporarily in database */
        $image = $key_value_img;

      // // /* Load the object of the file by it's fid */
        $file = File::load($image);

      // /* Set the status flag permanent of the file object */
        $file->setPermanent();

      // /* Save the file in database */
        $file->save();

       $uri = $file->getFileUri();
      //  print_r($uri);
      //  exit();

      $jid = $form_state->getValue('jkv_id');
      if (isset($jid)){
        $query = \Drupal::database();
        $query->update('json_key_val_tbl')
              ->fields(array(
                  'lang_id' =>  $keysvalues['lang_id'],
                  'jn_id' =>  $keysvalues['jn_id'],
                  'json_key' =>  $keysvalues['json_key'],
                 // 'select_type' =>  $keysvalues['select_type'],
                  'key_value_fmt' =>  $key_value_fmt_val,
                  'key_value_text' => $key_value_text,
                  'key_value_img' =>  $key_value_img,
                  'key_value_img_alt' =>  $key_value_img_alt,
                  'last_updated' => date('Y-m-d H:i:s'),
                  'created_date' => date('Y-m-d H:i:s'),
                ))
              ->condition('jkv_id',$jid)
              ->execute();
        \Drupal::messenger()->addMessage("succesfully updated $jid");
        $form_state->setRedirect('json_file_data.jsondatalisting');
      }else{
          $conn = Database::getConnection();
          $conn->insert('json_key_val_tbl')->fields(array(
                  'lang_id' =>  $keysvalues['lang_id'],
                  'jn_id' =>  $keysvalues['jn_id'],
                  'json_key' =>  $keysvalues['json_key'],
                  'select_type' =>  $keysvalues['field_select'],
                  'key_value_fmt' =>  $key_value_fmt_val,
                  'key_value_text' => $key_value_text,
                  'key_value_img' =>  $key_value_img,
                  'key_value_img_alt' =>  $key_value_img_alt,
                  'last_updated' => date('Y-m-d H:i:s'),
                  'created_date' => date('Y-m-d H:i:s'),
          ))->execute();
          $url = Url::fromRoute('json_file_data.jsondatalisting');
          $form_state->setRedirectUrl($url);
    }
  }
}
