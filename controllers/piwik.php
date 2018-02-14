<?php
/**
 * Created by PhpStorm.
 * User: beer
 * Date: 8.10.15
 * Time: 15.18
 */

class Piwik extends MY_Controller {

    const TYPE_WEB = 'web';
    const TYPE_SEARCH = 'search';
    const TYPE_ADWORDS = 'adwords';
    const TYPE_WEBSITES = 'websites';
    const TYPE_SOCIAL = 'social';

    protected $website_part = 'dashboard';

    // default dates for datepickers
    protected $dates;
    // default date format in traffic section
    protected $date_format = 'Y-m-d';

    /** @var \VisualAppeal\Piwik $piwik */
    protected $piwik;

    public function __construct() {
        parent::__construct($this->website_part);

        $this->lang->load('analytics', $this->language);
        JsSettings::instance()->add([
            'i18n' => $this->lang->load('analytics', $this->language)
        ]);

        $config = Api_key::build_config('piwik');
        $config['site_id'] = $this->c_user->ifUserHasConfigValue('piwik_site_id');

        if(!empty($config['domain']) && !empty($config['site_id'])) {
            $this->piwik = new \VisualAppeal\Piwik($config['domain'], $config['token'], $config['site_id']);
        }

        $now = new \DateTime();
        $to_str = $now->format($this->date_format);
        $now->modify('-30 days');
        $from_str = $now->format($this->date_format);
        // default date range
        $this->dates = array(
            'from' => $from_str,
            'to' => $to_str,
        );
    }


    public function index() {
        if(!$this->piwik) {
            $this->addFlash(lang('please_select_site'));
            redirect('settings/piwik');
        }
        JsSettings::instance()->add(array(
            'analytics' => array(
                'dates' => $this->dates,
                'date_format' => $this->date_format,
                'request_url' => site_url('piwik/data'),
            ),
        ));
        CssJs::getInst()->add_js(array(
            'libs/handlebar.js',
            'libs/handlebars_helpers.js',
            'libs/highcharts/highcharts.js'
        ));

        CssJs::getInst()->c_js();
        $this->template->set('dates', $this->dates);
        $this->template->render();
    }

    /**
     * Get data from Piwik by ajax
     *
     * @param string $type
     */
    public function data($type = self::TYPE_WEB) {
        if ($this->input->is_ajax_request()) {

            // get dates from form
            $from_str = $this->input->post('from');
            $to_str = $this->input->post('to');

            if(empty($from_str)) {
                $from_str = $this->dates['from'];
            } else {
                $date = DateTime::createFromFormat('m/d/Y', $from_str);
                $from_str = $date->format($this->date_format);
            }
            if(empty($to_str)) {
                $to_str = $this->dates['to'];
            } else {
                $date = DateTime::createFromFormat('m/d/Y', $to_str);
                $to_str = $date->format($this->date_format);
            }

            try {

                if($type == self::TYPE_WEB) {

                    $this->piwik->setPeriod(\VisualAppeal\Piwik::PERIOD_RANGE);
                    $this->piwik->setRange($from_str, $to_str);
                    $this->piwik->setFormat(\VisualAppeal\Piwik::FORMAT_JSON);
                    $this->piwik->setLanguage($this->language_small);

                    $visits = $this->piwik->getVisits();
                    $unique_visitors = $this->piwik->getUniqueVisitors();
                    $bounce_count = $this->piwik->getBounceCount();
                    $bounce_count = ($bounce_count) ? $bounce_count : 0;

                    $bounce_rate = (!$visits) ? 0 : $bounce_count / $visits * 100;
                    $bounce_rate = round($bounce_rate, 2);

                    $sum_visit_duration = $this->piwik->getSumVisitsLength();
                    if(!$sum_visit_duration || !$visits) {
                        $average_visit_duration_pretty = '00:00:00';
                    } else {
                        $average_visit_duration = $sum_visit_duration / $visits;
                        $seconds = $average_visit_duration%60;
                        $minutes = $average_visit_duration/60%60;
                        $hours = ($average_visit_duration-$average_visit_duration%3600)/3600;
                        if($hours < 1) {
                            $hours = 0;
                        }
                        $seconds_pretty = ($seconds < 10) ? '0'.$seconds : $seconds;
                        $minutes_pretty = ($minutes < 10) ? '0'.$minutes : $minutes;
                        $hours_pretty = ($hours < 10) ? '0'.$hours : $hours;
                        $average_visit_duration_pretty = $hours_pretty . ':' . $minutes_pretty . ':' . $seconds_pretty;
                    }

                    $data = [
                        'visits' => ($visits) ? $visits : 0,
                        'unique_visits' => ($unique_visitors) ? $unique_visitors : 0,
                        'bounce_rate' => $bounce_rate.'%',
                        'average_visit_duration' => $average_visit_duration_pretty
                    ];

                    $this->piwik->reset();

                    $this->piwik->setPeriod(\VisualAppeal\Piwik::PERIOD_DAY);
                    $this->piwik->setRange($from_str, $to_str);
                    $this->piwik->setFormat(\VisualAppeal\Piwik::FORMAT_JSON);
                    $this->piwik->setLanguage($this->language_small);

                    $data['visits_chart'] = $this->piwik->getVisits();

                    $result['success'] = TRUE;
                    $result = Arr::merge($result, $data);
                } elseif($type == self::TYPE_ADWORDS) {
                    $this->piwik->setPeriod(\VisualAppeal\Piwik::PERIOD_RANGE);
                    $this->piwik->setRange($from_str, $to_str);
                    $this->piwik->setFormat(\VisualAppeal\Piwik::FORMAT_JSON);
                    $this->piwik->setLanguage($this->language_small);

                    $data = [
                        'data' => [
                            'headers' => [
                                lang('keyword'),
                                lang('visits'),
                                lang('average_visit_duration'),
                                lang('bounce_rate')
                            ],
                            'result' => []
                        ]
                    ];
                    $referrers = $this->piwik->getKeywords();

                    $data['data']['result'] = $this->_getDataToTable($referrers);
                    $data['caption'] = lang('adwords_traffic');
                    $result['success'] = TRUE;
                    $result = Arr::merge($result, $data);
                } elseif($type == self::TYPE_SEARCH) {
                    $this->piwik->setPeriod(\VisualAppeal\Piwik::PERIOD_RANGE);
                    $this->piwik->setRange($from_str, $to_str);
                    $this->piwik->setFormat(\VisualAppeal\Piwik::FORMAT_JSON);
                    $this->piwik->setLanguage($this->language_small);

                    $data = [
                        'data' => [
                            'headers' => [
                                lang('source'),
                                lang('visits'),
                                lang('average_visit_duration'),
                                lang('bounce_rate')
                            ],
                            'result' => []
                        ]
                    ];
                    $referrers = $this->piwik->getSearchEngines();

                    $data['data']['result'] = $this->_getDataToTable($referrers);
                    $data['caption'] = lang('search_traffic');
                    $result['success'] = TRUE;
                    $result = Arr::merge($result, $data);
                } elseif($type == self::TYPE_WEBSITES) {
                    $this->piwik->setPeriod(\VisualAppeal\Piwik::PERIOD_RANGE);
                    $this->piwik->setRange($from_str, $to_str);
                    $this->piwik->setFormat(\VisualAppeal\Piwik::FORMAT_JSON);
                    $this->piwik->setLanguage($this->language_small);

                    $data = [
                        'data' => [
                            'headers' => [
                                lang('website'),
                                lang('visits'),
                                lang('average_visit_duration'),
                                lang('bounce_rate')
                            ],
                            'result' => []
                        ]
                    ];
                    $referrers = $this->piwik->getWebsites();

                    $data['data']['result'] = $this->_getDataToTable($referrers);
                    $data['caption'] = lang('websites_traffic');
                    $result['success'] = TRUE;
                    $result = Arr::merge($result, $data);
                } elseif($type == self::TYPE_SOCIAL) {
                    $this->piwik->setPeriod(\VisualAppeal\Piwik::PERIOD_RANGE);
                    $this->piwik->setRange($from_str, $to_str);
                    $this->piwik->setFormat(\VisualAppeal\Piwik::FORMAT_JSON);
                    $this->piwik->setLanguage($this->language_small);

                    $data = [
                        'data' => [
                            'headers' => [
                                lang('social'),
                                lang('visits'),
                                lang('average_visit_duration'),
                                lang('bounce_rate')
                            ],
                            'result' => []
                        ]
                    ];
                    $referrers = $this->piwik->getSocials();

                    $data['data']['result'] = $this->_getDataToTable($referrers);
                    $data['caption'] = lang('social_traffic');
                    $result['success'] = TRUE;
                    $result = Arr::merge($result, $data);
                }

            } catch (Exception $e) {

                $result['success'] = FALSE;
                $result['error'] = $e->getMessage();

            }

            $result['dates'] = array(
                'from' => date($this->date_format, strtotime($from_str)),
                'to' => date($this->date_format, strtotime($to_str)),
            );

            exit( json_encode($result) );
        }
    }

    private function _getDataToTable($referrers) {
        $data = [];
        foreach($referrers as $referrer) {
            $visits = $referrer->nb_visits;
            $bounce_count = $referrer->bounce_count;
            $bounce_rate = (!$visits) ? 0 : $bounce_count / $visits * 100;
            $bounce_rate = round($bounce_rate, 2);

            $sum_visit_duration = $referrer->sum_visit_length;
            if(!$sum_visit_duration || !$visits) {
                $average_visit_duration_pretty = '00:00:00';
            } else {
                $average_visit_duration = $sum_visit_duration / $visits;
                $seconds = $average_visit_duration%60;
                $minutes = $average_visit_duration/60%60;
                $hours = ($average_visit_duration-$average_visit_duration%3600)/3600;
                if($hours < 1) {
                    $hours = 0;
                }
                $seconds_pretty = ($seconds < 10) ? '0'.$seconds : $seconds;
                $minutes_pretty = ($minutes < 10) ? '0'.$minutes : $minutes;
                $hours_pretty = ($hours < 10) ? '0'.$hours : $hours;
                $average_visit_duration_pretty = $hours_pretty . ':' . $minutes_pretty . ':' . $seconds_pretty;
            }
            $data[] = [
                $referrer->label,
                $visits,
                $average_visit_duration_pretty,
                $bounce_rate,
            ];
        }
        return $data;
    }

}