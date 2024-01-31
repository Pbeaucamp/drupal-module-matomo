<?php

namespace Drupal\matomo_dashboard\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Controller for the Matomo BPM module.
 */

class matomo_dashboardController extends ControllerBase
{

    /**
     * Display the Matomo BPM configuration form.
     *
     * @return array
     *   The configuration form.
     */
    public function dashboard()
    {

        $content = array();

        $matomo_dashboard_config = \Drupal::state()->get(\Drupal\matomo_dashboard\Form\matomo_dashboardApiConfigForm::MATOMOBPM_API_CONFIG_FORM);
        $api_url = ($matomo_dashboard_config['matomo_dashboard_api_base_url']) ?: '';
        $date = ($matomo_dashboard_config['date']) ?: 'today';
        $sparklineImages = $this->sparklineImages();
        $sparklineInfos = $this->sparklineInfos();
        $sparklineInfos2 = $this->sparklineInfos2();
        $visitsSum = $this->visitsSummary();
        $referrerType = $this->referrerType();
        $devicesType = $this->devicesType();
        $dataChart = $this->dataChart();
        $dataMap = $this->dataMap();


        $content['sparkline_images'] = $sparklineImages;
        $content['sparkline_infos'] = $sparklineInfos;
        $content['sparkline_infos2'] = $sparklineInfos2;
        $content['visits_summary'] = $visitsSum;
        $content['referrer_type'] = $referrerType;
        $content['devices_type'] = $devicesType;
        $content['api_url'] = $api_url;
        $content['date'] = $date;

        return array(
            '#theme' => 'matomo_dashboard_dashboard',
            '#content' => $content,
            '#attached' => array(
                'library' => array(
                    'matomo_dashboard/matomo_dashboard-styling',
                ),
                'drupalSettings' => array(
                    'matomo_dashboard' => array(
                        'chartData' => $dataChart,
                        'mapData' => $dataMap,
                    ),
                ),
            ),
        );
    }

    public function visitsSummary()
    {
        $matomo_dashboard_api_connector_service = \Drupal::service('matomo_dashboard.api_connector');
        $visits_summary = $matomo_dashboard_api_connector_service->getVisitsSummary();

        // Triez le tableau en fonction du nombre de visites (nb_visits) de manière décroissante.
        usort($visits_summary, function ($a, $b) {
            return $b['nb_visits'] <=> $a['nb_visits'];
        });

        // Prenez les 5 premières entrées.
        $top_five_sites = array_slice($visits_summary, 0, 5);

        // Formatez le label de chaque entrée pour qu'il ne contienne que le nom du site.
        foreach ($top_five_sites as &$site) {
            $site['label'] = (!empty($site['label']) && trim(explode("|", $site['label'])[0])) ? trim(explode("|", $site['label'])[0]) : "Les données";
        }
        return $top_five_sites;
    }
    // Ajoutez une nouvelle fonction pour récupérer les URLs des images sparkline
    public function sparklineImages()
    {
        $matomo_dashboard_api_connector_service = \Drupal::service('matomo_dashboard.api_connector');
        $sparkline_images = $matomo_dashboard_api_connector_service->getSparklineImages();

        return $sparkline_images;
    }
    public function sparklineInfos($function = '1')
    {
        $matomo_dashboard_api_connector_service = \Drupal::service('matomo_dashboard.api_connector');
        $sparkline_infos = $matomo_dashboard_api_connector_service->getSparklineInfos($function);

        return $sparkline_infos;
    }

    public function sparklineInfos2()
    {
        $matomo_dashboard_api_connector_service = \Drupal::service('matomo_dashboard.api_connector');
        $sparkline_infos2 = $matomo_dashboard_api_connector_service->getSparklineInfos2();

        return $sparkline_infos2;
    }

    public function referrerType()
    {
        $matomo_dashboard_api_connector_service = \Drupal::service('matomo_dashboard.api_connector');
        $referrer_type = $matomo_dashboard_api_connector_service->getReferrerType();

        return $referrer_type;
    }

    public function devicesType()
    {
        $matomo_dashboard_api_connector_service = \Drupal::service('matomo_dashboard.api_connector');
        $devices_type = $matomo_dashboard_api_connector_service->getDevicesType();

        return $devices_type;
    }

    public function dataChart()
    {
        $dataforChart = $this->sparklineInfos('2');

        // Traiter les données
        $formattedData = [];
        foreach ($dataforChart as $date => $visitData) {
            $formattedData[] = [
                'date' => $date,
                'nb_visits' => $visitData['nb_visits'],
                'nb_uniq_visitors' => $visitData['nb_uniq_visitors'],
            ];
        }

        // Trier les données par date (optionnel)
        usort($formattedData, function ($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
        });

        return $formattedData;
    }
    public function dataMap()
    {
        $matomo_dashboard_api_connector_service = \Drupal::service('matomo_dashboard.api_connector');
        $map_data = $matomo_dashboard_api_connector_service->getMapData();

        return $map_data;
    }
}
