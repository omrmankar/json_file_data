<?php

namespace Drupal\json_file_data\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class DefaultForm.
 */
class JsonKeyValueForm extends FormBase {
	/**
	 * {@inheritdoc}
	 */
	public function getFormId() {
		return 'JsonKeyValueForm';
	}

	public function buildForm(array $form, FormStateInterface $form_state) {
		//create a list of radio boxes that will toggle the  textbox
		//below if 'other' is selected
		$form['colour_select'] = [
			'#type' => 'radios',
			'#title' => $this->t('Pick a colour'),
			'#options' => [
				'blue' => $this->t('Blue'),
				'white' => $this->t('White'),
				'black' => $this->t('Black'),
				'other' => $this->t('Other'),
			],
			'#attributes' => [
				//define static name and id so we can easier select it
				// 'id' => 'select-colour',
				'name' => 'field_select_colour',
			],
		];

		//this textfield will only be shown when the option 'Other'
		//is selected from the radios above.
		$form['custom_colour'] = [
			'#type' => 'textfield',
			'#size' => '60',
			'#placeholder' => 'Enter favourite colour',
			'#attributes' => [
				'id' => 'custom-colour',
			],
			'#states' => [
				//show this textfield only if the radio 'other' is selected above
				'visible' => [
					//don't mistake :input for the type of field. You'll always use
					//:input here, no matter whether your source is a select, radio or checkbox element.
					':input[name="field_select_colour"]' => ['value' => 'other'],
				],
			],
		];

		//create the submit button
		$form['submit'] = [
			'#type' => 'submit',
			'#value' => $this->t('Sorry, I\'m colour-blind'),
		];

		return $form;
	}

	public function validateForm(array &$form, FormStateInterface $form_state) {
		parent::validateForm($form, $form_state);
	}

	public function submitForm(array &$form, FormStateInterface $form_state) {
		// Display result.
		foreach ($form_state->getValues() as $key => $value) {
			drupal_set_message($key . ': ' . $value);
		}
	}
}
