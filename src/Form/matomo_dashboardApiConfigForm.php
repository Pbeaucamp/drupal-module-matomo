<?php

namespace Drupal\matomo_dashboard\Form;

use Drupal\Core\Form\FormBase;

class matomo_dashboardApiConfigForm extends FormBase
{

    const MATOMOBPM_API_CONFIG_FORM = 'matomo_dashboard_api_config_form:values';

    public function getFormId()
    {
        return 'matomo_dashboard_api_config_form';
    }

    public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state)
    {
        $values = \Drupal::state()->get(self::MATOMOBPM_API_CONFIG_FORM);
        $form = array();

        $form['matomo_dashboard_api_base_url'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('API Base URL'),
            '#description' => $this->t('The base URL of the Matomo API.'),
            '#required' => TRUE,
            '#default_value' => $values['matomo_dashboard_api_base_url']
        );

        $form['matomo_dashboard_api_token'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('API Token'),
            '#description' => $this->t('The API token for the Matomo API.'),
            '#required' => FALSE,
            '#default_value' => $values['matomo_dashboard_api_token']
        );

        $form['id_site'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Site ID'),
            '#description' => $this->t('The Site ID for the Matomo API.'),
            '#required' => TRUE,
            '#default_value' => $values['id_site']
        );

        // Add dropdown for 'period'
        $form['period'] = array(
            '#type' => 'select',
            '#title' => $this->t('Period'),
            '#description' => $this->t('The period for the Matomo API request.'),
            '#required' => TRUE,
            '#options' => [
                'day' => $this->t('Day'),
                'week' => $this->t('Week'),
                'month' => $this->t('Month'),
                'year' => $this->t('Year'),
                // Add more options as needed...
            ],
            '#default_value' => $values['period'],
        );

        // Add dropdown for 'date'
        $form['date'] = array(
            '#type' => 'select',
            '#title' => $this->t('Date'),
            '#description' => $this->t('The date for the Matomo API request.'),
            '#required' => TRUE,
            '#options' => [
                'yesterday' => $this->t('Yesterday'),
                'today' => $this->t('Today'),
                'last7' => $this->t('Last 7 days'),
                'last30' => $this->t('Last 30 days'),
                'last365' => $this->t('Last 365 days'),
                // Add more options as needed...
            ],
            '#default_value' => $values['date'],
        );

        $form['actions']['#type'] = 'actions';
        $form['actions']['submit'] = array(
            '#type' => 'submit',
            '#value' => $this->t('Save'),
            '#button_type' => 'primary'
        );
        return $form;
    }
    public function submitForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state)
    {
        $submitted_values = $form_state->cleanValues()->getValues();

        \Drupal::state()->set(self::MATOMOBPM_API_CONFIG_FORM, $submitted_values);

        $messenger = \Drupal::service('messenger');
        $messenger->addMessage($this->t('The Matomo BPM API configuration has been saved.'));
    }
}
