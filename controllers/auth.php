<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends MY_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->form_validation->set_error_delimiters('', '<br>');
        $this->template->layout = 'layouts/auth';
        CssJs::getInst()->add_js('libs/test.js', null, 'footer'); //placeholder for old IE

        $this->load->library('form_validation');
        $this->load->config('manage_plans');
        $this->lang->load('auth', $this->language);
        JsSettings::instance()->add([
            'i18n' => $this->lang->load('auth', $this->language)
        ]);

        $this->template->set('showHeaderLinks', false);
    }

    //redirect if needed, otherwise display the user list
    function index()
    {
        if ( ! $this->ion_auth->logged_in()) {
            redirect('auth/login');
        }
        else {
            redirect('dashboard', 'refresh');
        }
    }

    public function login() {

        //validate form input
        $this->form_validation->set_rules('identity', 'Email', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() === TRUE) {

            $identity = $this->input->post('identity');
            $password = $this->input->post('password');
            $remember = (bool) $this->input->post('remember');

            $logged = $this->ion_auth->login($identity, $password, $remember);
            if ($logged) {
                //if the login is successful
                //redirect them back to the home page
                //$this->addFlash($this->ion_auth->messages(), 'success');

                $userData = $this->session->all_userdata();
                $redirectUri = (empty($userData['redirect_uri'])) ?
                                site_url('/') :
                                site_url($userData['redirect_uri']);
                redirect($redirectUri);
            }
            //if the login was un-successful
            //redirect them back to the login page
            // $this->session->set_flashdata('alert_error', $this->ion_auth->errors());
            $this->addFlash($this->ion_auth->errors());
            // redirect('auth/login'); 
        } else {
            if (validation_errors()) {
                $this->addFlash(validation_errors());
            }
        }
        //the user is not logging in so display the login page
        //set the flash data error message if there is one

        // $this->template->set('auth_message', (validation_errors()) ? validation_errors() : $this->session->flashdata('alert_error'));

        $identity = $this->input->post('identity') 
            ? $this->input->post('identity')
            : $this->form_validation->set_value('identity');
        // CssJs::getInst()
        //     ->add_css(array(
        //         'login.css'
        //     ));
        $this->template->set('identity', $identity);
        $this->template->render('login');
    }

    /**
     * 
     */
    public function login_hook()
    {
        $identity = $this->input->get('identity');
        $password = $this->input->get('password'); 
        if ( ! ($identity && $password)) {
            show_404();
        }
        $logged = $this->ion_auth->login($identity, $password, TRUE);
        if ( ! $logged) {
            show_404();
        }
        $this->addFlash($this->ion_auth->messages(), 'success');
        redirect(site_url('dashboard'));
    }

    //log the user out
    public function logout() {
        $this->ion_auth->clearManagerCode();
        $this->ion_auth->logout();
        redirect(site_url('auth/login'), 'refresh');
    }

    public function google($planId, $inviteCode = null) {
        try {
            $this->load->library('Socializer/socializer');
            /* @var Socializer_Google $google_socializer */
            $google_socializer = Socializer::factory('Google');
            $redirect_uri = $google_socializer->get_access_url();
            $state = json_encode(array(
                'planId' => $planId,
                'inviteCode' => $inviteCode
            ));
            $redirect_uri.='&state='.base64_encode($state);
            redirect($redirect_uri);
        } catch (Exception $e) {
            $this->addFlash($e->getMessage());
            redirect(site_url('auth/register/'.$planId.'/'.$inviteCode));
        }
    }

    public function google_auth() {
        try {
            $this->load->library('Socializer/socializer');
            /* @var Socializer_Google $google_socializer */
            $google_socializer = Socializer::factory('Google');
            $redirect_uri = $google_socializer->get_access_url();
            redirect($redirect_uri);
        } catch (Exception $e) {
            $this->addFlash($e->getMessage());
            redirect(site_url('auth/login'));
        }
    }

    public function google_login() {
        try {
            $this->load->library('Socializer/socializer');
            /* @var Socializer_Google $google_socializer */
            $google_socializer = Socializer::factory('Google');
            $profile = $google_socializer->sign_up($this->profile->id);
            if($profile) {
                if(isset($profile['emails'][0])) {
                    $email = $profile['emails'][0];
                    $user =  User::findByEmail($email['value']);
                    if(!$user) {
                        $this->addFlash('Register first. Go to <a href="http://smintly.com/#plans">Smintly</a>.');
                        redirect(site_url('auth/login'));
                    } else {
                        $logged = $this->ion_auth->login($email['value'], '', true, true);
                        if ($logged) {
                            redirect(site_url('dashboard'));
                        }
                        redirect(site_url('auth/login'));
                    }
                }
            }
            $this->addFlash('Error.');
            redirect(site_url('auth/login'));
        } catch (Exception $e) {
            $this->addFlash($e->getMessage());
            redirect(site_url('auth/login'));
        }
    }

    public function google_signup() {
        if($_GET['state']) {
            $params = json_decode(base64_decode($_GET['state']));
        } else {
            redirect('https://smintly.com/#plans');
            exit();
        }
        if($params->planId) {
            if($params->inviteCode) {
                $redirect_link = site_url('auth/register/' . $params->planId);
            } else {
                $redirect_link = site_url('auth/register/' . $params->planId. '/' . $params->inviteCode);
            }
        } else {
            $redirect_link = site_url('auth/plans');
        }
        try {
            $this->load->library('Socializer/socializer');
            /* @var Socializer_Google $google_socializer */
            $google_socializer = Socializer::factory('Google');
            $profile = $google_socializer->sign_up($this->profile->id);
            if($profile) {
                if(isset($profile['emails'][0])) {
                    $email = $profile['emails'][0];
                    $user =  User::findByEmail($email['value']);
                    if(!$user) {
                        $registered = $this->ion_auth->register($profile['displayName'], '', $email['value'], array(
                            'first_name' => $profile['name']['givenName'],
                            'last_name'  => $profile['name']['familyName'],
                        ), true);
                        if($registered) {
                            $user = new User($registered);
                            if($this->isTrialEnabled()) {
                                /* @var Core\Service\Subscriber\Subscriber $subscriber */
                                $subscriber = $this->get('core.subscriber');
                                $subscriber->setUser($user);
                                $plan = new Plan((int)$params->planId);
                                $period = $plan->getTrialPeriod();
                                $interval = new DateInterval('P' . $period->period . ucwords($period->qualifier));

                                $subscriber->addTrialSubscription($plan, $interval);
                                $this->addFlash('Registered', 'success');
                                $logged = $this->ion_auth->login($email['value'], '', true, true);
                                if ($logged) {
                                    redirect(site_url('dashboard'));
                                }
                                redirect($redirect_link);
                            } else {
                                redirect(site_url('subscript/subscribe/'.$user->id.'/'.$params->planId.'/'.$params->inviteCode));
                            }
                        } else {
                            $this->addFlash('Error.');
                            redirect($redirect_link);
                        }
                    } else {
                        $logged = $this->ion_auth->login($email['value'], '', true, true);
                        if ($logged) {
                            redirect(site_url('dashboard'));
                        }
                        redirect($redirect_link);
                    }
                }
            }
            $this->addFlash('Error.');
            redirect($redirect_link);
        } catch (Exception $e) {
            $this->addFlash($e->getMessage());
            redirect($redirect_link);
        }
    }

    /**
     * Used to connect user to Facebook account
     * Use Socializer Library
     *
     * @access public
     *
     * @param      $planId
     * @param null $inviteCode
     */
    public function facebook($planId = null, $inviteCode = null) {
        if($planId) {
            if($inviteCode) {
                $redirect_link = site_url('auth/register/' . $planId);
            } else {
                $redirect_link = site_url('auth/register/' . $planId. '/' . $inviteCode);
            }
        } else {
            $redirect_link = site_url('auth/plans');
        }
        try {
            $this->load->library('Socializer/socializer');
            /* @var Socializer_Facebook $facebook_socializer */
            $facebook_socializer = Socializer::factory('Facebook');
            $profile = $facebook_socializer->sign_up();
            if($profile) {
                $email = $profile['email'];
                $user = User::findByEmail($email);
                if (!$user && $planId) {
                    $registered = $this->ion_auth->register($profile['name'], '', $email, array(
                        'first_name' => $profile['first_name'],
                        'last_name' => $profile['last_name'],
                    ), true);
                    if ($registered) {
                        $user = new User($registered);
                        if($this->isTrialEnabled()) {
                            /* @var Core\Service\Subscriber\Subscriber $subscriber */
                            $subscriber = $this->get('core.subscriber');
                            $subscriber->setUser($user);
                            $plan = new Plan((int)$planId);
                            $period = $plan->getTrialPeriod();
                            $interval = new DateInterval('P' . $period->period . ucwords($period->qualifier));

                            $subscriber->addTrialSubscription($plan, $interval);
                            $this->addFlash('Registered', 'success');
                            $logged = $this->ion_auth->login($email, '', true, true);
                            if ($logged) {
                                redirect(site_url('dashboard'));
                            }
                            redirect($redirect_link);
                        } else {
                            redirect(site_url('subscript/subscribe/'.$user->id.'/'.$planId.'/'.$inviteCode));
                        }
                    } else {
                        $this->addFlash('Error.');
                        redirect($redirect_link);
                    }
                } elseif(!$user) {
                    $this->addFlash('Register first. Go to <a href="http://smintly.com/#plans">Smintly</a>.');
                    redirect(site_url('auth/login'));
                } else {
                    $logged = $this->ion_auth->login($email, '', true, true);
                    if ($logged) {
                        redirect(site_url('dashboard'));
                    }
                    redirect($redirect_link);
                }
            }
        } catch (Exception $e) {
            $this->addFlash($e->getMessage());
            redirect($redirect_link);
        }

    }

    public function register($planId = null, $inviteCode = null) {

        ///ddd($planId, $inviteCode);
        $password_min = $this->config->item('min_password_length', 'ion_auth');
        $password_max = $this->config->item('max_password_length', 'ion_auth');
        
        //if ($this->isPaymentEnabled()) {
            if ($planId == null) {
                redirect('auth/plans');
            }

            //if($this->isTrialEnabled()) {
                $plan = new Plan((int)$planId);
                $specialInvite = new Special_invite();
                if ($plan->id == null || ($plan->special && !$specialInvite->check($planId, $inviteCode))) {
                    $this->addFlash('Something went wrong');
                    redirect('auth/login');
                }
            //}
        //}

        //validate form input
        $this->form_validation->set_rules('first_name', 'First Name', 'required|xss_clean');
        $this->form_validation->set_rules('last_name', 'Last Name', 'required|xss_clean');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[' . $password_min 
            . ']|max_length[' . $password_max . ']|matches[confirm]'
        );
        $this->form_validation->set_rules('confirm', 'Confirm Password', 'required');
        $this->form_validation->set_rules('terms', 'Terms and Conditions', 'required');

        if ($this->form_validation->run() === TRUE) {
            $first_name = $this->input->post('first_name');
            $last_name = $this->input->post('last_name');
            $email = $this->input->post('email');
            $password = $this->input->post('password');

            $username = strtolower($first_name) . ' ' . strtolower($last_name);

            $additional_data = array(
                'first_name' => $first_name,
                'last_name'  => $last_name,
            );

            $registered = $this->ion_auth->register($username, $password, $email, $additional_data);

            if ($registered) {
                /* @var Core\Service\Mail\MailSender $sender */
                $sender = $this->get('core.mail.sender');
                $sender->sendRegistrationMail(array('user' => new User($registered)));
  
                $user =  User::findByEmail($email);

                $plan = new Plan((int)$planId);
                $user = new User($registered);
                $subscriber = $this->get('core.subscriber');
                $subscriber->setUser($user);
                $period = $plan->getTrialPeriod();
                //$interval = new DateInterval('P'.$period->period.ucwords($period->qualifier));
                $interval = new DateInterval('P20Y');
                $subscriber->addTrialSubscription($plan, $interval); 
                $payment = new Payment_transaction();
                $payment_id = $payment->newTransaction(array(), $user->id);
                $subscription = new Subscription();
                $_subscription = $subscription->getUserSubscriptions($user->id);
                $_subscription->addPayment($payment_id);
                //d($planId, $inviteCode); 
                $specialInvite->_delete($planId, $inviteCode);
                $this->addFlash('Registered', 'success');
                $logged = $this->ion_auth->login($email, $password, true);
                if ($logged) {
                    redirect('dashboard');
                }
                redirect('auth');

            } else {
                $this->addFlash($this->ion_auth->errors());
            }
        } else {
            if (validation_errors()) {
                $this->addFlash(validation_errors());
            }
        }

        CssJs::getInst()->c_js();

        $this->template->set('first_name', $this->form_validation->set_value('first_name'));
        $this->template->set('last_name', $this->form_validation->set_value('last_name'));
        $this->template->set('email', $this->form_validation->set_value('email'));
        $this->template->set('terms', $this->form_validation->set_value('terms'));
        $this->template->set('planId', $planId);
        $this->template->set('inviteCode', $inviteCode);
        $this->template->render();
    }

    public function plans()
    {
        $post = $this->input->post();

        if(!empty($post)) {
            if( isset($post['planId']) ) {
                $selectedPlan = $post['planId'];
                redirect('auth/register/'.$selectedPlan, 'refresh');
            }
        }

        $feature = new Feature();
        $plan = new Plan();
        $this->template->set('features', $feature->get());
        $withTrial = !$this->ion_auth->logged_in();
        CssJs::getInst()->add_js('libs/eq-height.js');
        $this->template->set('plans', $plan->getActualPlans($withTrial));
        $this->template->set('options', $this->config->config['period_qualifier']);
        $this->template->render();
    }

    //forgot password
    function forgot_password()
    {
        $data = array();

        $this->form_validation->set_rules('email', $this->lang->line('forgot_password_validation_email_label'), 'required');
        if ($this->form_validation->run() == false)
        {


            //setup the input
            $data['email'] = array(
                'name' => 'email',
                'id' => 'email',
                'placeholder' => 'Email',
            );

            //set any errors and display the form
            $message = (validation_errors()) ? validation_errors() : strip_tags($this->template->message());
            if ($message) {
                $this->addFlash($message);
            }

            $this->template->set($data);
            $this->template->render();
        }
        else
        {
            // get identity for that email
            $identity = $this->ion_auth->where('email', strtolower($this->input->post('email')))->users()->row();
            if(empty($identity)) {
                $this->ion_auth->set_message('forgot_password_email_not_found');

                $this->addFlash($this->ion_auth->messages());
                redirect("auth/forgot_password", 'refresh');
            }

            //run the forgotten password method to email an activation code to the user
            $forgotten = $this->ion_auth->forgotten_password($identity->{$this->config->item('identity', 'ion_auth')});

            if ($forgotten)
            {
                //if there were no errors
                $this->addFlash($this->ion_auth->messages(), 'success');
                redirect("auth/login", 'refresh'); //we should display a confirmation page here instead of the login page
            }
            else
            {
                $this->addFlash($this->ion_auth->errors());
                redirect("auth/forgot_password", 'refresh');
            }
        }
    }

    //reset password - final step for forgotten password
    public function reset_password($code = NULL)
    {
        if (!$code)
        {
            show_404();
        }

        $user = $this->ion_auth->forgotten_password_check($code);

        if ($user)
        {
            //if the code is valid then display the password reset form

            $this->form_validation->set_rules('new', $this->lang->line('reset_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[new_confirm]');
            $this->form_validation->set_rules('new_confirm', $this->lang->line('reset_password_validation_new_password_confirm_label'), 'required');

            if ($this->form_validation->run() == false)
            {
                //display the form

                //set the flash data error message if there is one
                $message = (validation_errors()) ? validation_errors() : strip_tags($this->template->message());
                if ($message) {
                    $this->addFlash($message);
                }

                $data['new_password'] = array(
                    'name' => 'new',
                    'id' => 'new',
                    'type' => 'password',

                    'placeholder' => 'New Password',
                );
                $data['new_password_confirm'] = array(
                    'name' => 'new_confirm',
                    'id' => 'new_confirm',
                    'type' => 'password',
                    'placeholder' => 'Confirm New Password',
                );
                $data['user_id'] = array(
                    'name' => 'user_id',
                    'id' => 'user_id',
                    'type' => 'hidden',
                    'value' => $user->id,
                );
                $data['csrf'] = $this->_get_csrf_nonce();
                $data['code'] = $code;

                //render
               // $this->_render_page('auth/reset_password', $data);
                $this->template->set($data);
                $this->template->render();
            }
            else
            {
                // do we have a valid request?
                if ($this->_valid_csrf_nonce() === FALSE || $user->id != $this->input->post('user_id'))
                {

                    //something fishy might be up
                    $this->ion_auth->clear_forgotten_password_code($code);

                    show_error($this->lang->line('error_csrf'));

                }
                else
                {
                    // finally change the password
                    $identity = $user->{$this->config->item('identity', 'ion_auth')};

                    $change = $this->ion_auth->reset_password($identity, $this->input->post('new'));

                    if ($change)
                    {
                        //if the password was successfully changed
                        $this->addFlash($this->ion_auth->messages(), 'success');
                        $this->logout();
                    }
                    else
                    {
                        $this->addFlash($this->ion_auth->errors());
                        redirect('auth/reset_password/' . $code, 'refresh');
                    }
                }
            }
        }
        else
        {
            //if the code is invalid then send them back to the forgot password page
            $this->addFlash($this->ion_auth->errors());
            redirect("auth/forgot_password", 'refresh');
        }
    }

    /**
     * Complete registration with invite
     */
    public function invite($code = null)
    {
        $this->load->config('manage_users');
        $timelimit = time() - $this->config->config['invite_timelimit'];
        $user = new User();

        if ($post = $this->input->post()) {
            $password_min = $this->config->item('min_password_length', 'ion_auth');
            $password_max = $this->config->item('max_password_length', 'ion_auth');
            $this->form_validation->set_rules('password', 'Password', 'required|min_length[' . $password_min
                . ']|max_length[' . $password_max . ']|matches[confirm]'
            );
            if ($this->form_validation->run() === TRUE) {
                $user->getInviteUser($post['code'], $timelimit);
                if (!$user->exists()) {
                    $this->addFlash('Params of your invitation are not valid');
                    redirect('auth');
                }
                $ion_auth = new Ion_auth_model();
                $result = $ion_auth->registerByInvite(array('id' => $user->id, 'password' => $post['password']));
                if ($result) {
                    $email = $user->email;
                    $sender = $this->get('core.mail.sender');
                    $sender->sendRegistrationMail(array('user' => $user));
                    $this->addFlash('Registered', 'success');
                    $remember = TRUE;
                    $logged = $this->ion_auth->login($email, $post['password'], $remember);
                    if ($logged) {
                        redirect('dashboard');
                    }
                    redirect('auth');
                } else {
                    redirect('auth');
                }
            } else {
                if (validation_errors()) {
                    $this->addFlash(validation_errors());
                }
                $this->template->set('code', $post['code']);
                $this->template->render();
            }

        } else {
            if ($code) {
                $user->getInviteUser($code, $timelimit);

                if (!$user->exists()) {
                    $this->addFlash('Your inviting link is not valid');
                    redirect('auth');
                }
                // $newCode = $this->ion_auth->createInviteCode();
                // $user->invite_code = md5($newCode);
                // if ($user->save()) {
                //     $this->template->set('code', $newCode);
                // }
                 $this->template->set('code', $code);
                $this->template->render();
            } else {
                redirect('auth');
            }
        }

    }

    protected function _get_csrf_nonce()
    {
        $this->load->helper('string');
        $key = random_string('alnum', 8);
        $value = random_string('alnum', 20);
        $this->session->set_userdata('csrfkey', $key);
        $this->session->set_userdata('csrfvalue', $value);

        return array($key => $value);
    }

    protected function _valid_csrf_nonce()
    {
        $csrfKey = $this->session->userdata('csrfkey');
        $csrfValue = $this->session->userdata('csrfvalue');
        if ($this->input->post((string)$csrfKey) !== FALSE &&
            $this->input->post((string)$csrfKey) == (string)$csrfValue)
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }
    public function welcome($transaction_id){
        //die();
        if ($transaction_id) {
            $payment = new Payment_transaction();
            $payment = $payment->checkPayment($transaction_id);
            if(!empty($payment['id'])){
                $user = new User($payment['user_id']);
                if($user->password == 0){
                    $inviteCode = $this->ion_auth->createInviteCode();
                    $_inviteCode = md5($inviteCode);
                    $user->invite_code = $_inviteCode;
                    $user->invite_time = time()+$this->config->item('invite_timelimit');
                    $user->save();
                    redirect('auth/invite/'.$inviteCode);
                } else {
                    $this->addFlash('You already have a Password.', 'error');
                    redirect('auth/login');
                }
            } else {
                $this->addFlash('Invalid ID', 'error');
                redirect('auth/login');
            }
        } else {
            $this->addFlash('Invalid ID', 'error');
            redirect('auth/login');
        }
        // } else {
        //     //redirect back
        // }
    }

    public function jvzoo_callback() {
        $jvzoo_products = array(
            "259978" => 1, //PRO 
            "259976" => 1, //LITE

            "259980" => 2, //MAX
            "259982" => 2, //MAX LITE

            "259994" => 3, //ECOM
            "259996" => 3, //ECOM LITE

            "259998" => 4, //ELITE
            "260000" => 4, //ELITE LITE

            "259990" => 5, //SPECIAL
            "259992" => 5, //SPECIAL LITE

            "259986" => 5, //EXCLUSIVE 
            "259988" => 5, //EXCLUSIVE LITE            
            );

        $log = new KLogger(APPPATH."logs/custom", KLogger::INFO);
        $log->logInfo('Proccessing  Jvzoo APN');
        $this->load->library('jvzoo');
        $post = $this->input->post();
        //$log->logInfo('JVZOO POST '.serialize($post));
        $jvzoo = new Jvzoo();
        $result = $jvzoo->initialize($post, $log);
        //$log->logInfo('Action '.$result['ctransaction']);
        $log->logInfo('PROCCESED DATA '.serialize($result));
        if(!empty($result)){
            switch ($result['ctransaction']) {
                case 'SALE':            
                $planId = $jvzoo_products[$result['product_id']];

                //check if user already exists 
                $user = new User();  
                $rsul = $user->findByEmail($result['username']); 
                if(!empty($rsul)){
                    $plan = $rsul->getActivePlan();
                    $_plan = $plan->to_array();
                    if($_plan['id'] != $planId || empty($plan)){
                        $log->logInfo('Updating Subscription');
                        $subscription = new Subscription();
                        $_subscription = $subscription->getUserSubscriptions($rsul->id);
                        if(empty($_subscription) || empty($plan)){
                            $log->logInfo('Updating Subscription MOD');
                            $user = new User($rsul->id);
                            $plan = new Plan((int)$planId);
                            $subscriber = $this->get('core.subscriber');
                            $subscriber->setUser($user);
                            $period = $plan->getTrialPeriod();
                            $interval = new DateInterval('P20Y');
                            $subscriber->addTrialSubscription($plan, $interval);  
                            $payment = new Payment_transaction();
                            $payment_id = $payment->newTransaction($post, $rsul->id);
                            $_subscription = $subscription->getUserSubscriptions($rsul->id);                            
                            $_subscription->addPayment($payment_id);  
                            $log->logInfo('Success Updating Subscription'); 
                            break;                     
                        }
                        $_subscription->modify($planId);
                        $payment = new Payment_transaction($_subscription->stored->payment_transaction_id);
                        //refresh payment
                        $payment->updateTransaction($post);
                        $log->logInfo('Success Updating Subscription');
                    }
                    break;
                }       
                $log->logInfo('Plan ID '.$planId);            
                $plan = new Plan((int)$planId);
                $managerGroup = $this->ion_auth->getGroupByName('members');
                $userId = null;
                $add_user = $this->ion_auth->invite($result['username'], array($managerGroup->id), $userId);
                if(!empty($add_user)){
                    $log->logInfo(__FUNCTION__ .' Adding New User'); 
                    try{
                        $user = new User($add_user['id']);
                        $subscriber = $this->get('core.subscriber');
                        $subscriber->setUser($user);
                        $period = $plan->getTrialPeriod();
                        //$interval = new DateInterval('P'.$period->period.ucwords($period->qualifier));
                        $interval = new DateInterval('P20Y');
                        $subscriber->addTrialSubscription($plan, $interval); 
                        $payment = new Payment_transaction();
                        $payment_id = $payment->newTransaction($post, $add_user['id']);
                        $subscription = new Subscription();
                        $_subscription = $subscription->getUserSubscriptions($add_user['id']);
                        $_subscription->addPayment($payment_id);


                    } catch (Exception $e) {
                        $log->logInfo(__FUNCTION__ .' Error Proccessing  Jvzoo APN -> '. $e->getMessage());
                        //var_dump($e->getMessage()); die();
                    }     
                    $sender = $this->get('core.mail.sender');
                    $params['to'] = $result['username'];
                    $params['data'] = array('link' => site_url('auth/invite/'.$add_user['code']),
                        'sitename' => $this->config->config['OCU_site_name']
                    );
                    $sender->sendInviteMail($params);
                    //adds user to get response mailing list
                    $result = $this->getResponse($post['ccustname'], $post['ccustemail']);
                    $log->logInfo(__FUNCTION__ .' Success Adding New User'. $result); 
                }
                break;
                case 'RFND':
                $log->logInfo(__FUNCTION__ .' Refund Task Started');
                //check if user has higher plan 
                $user = new User();  
                $rsul = $user->findByEmail($result['username']); 
                $planId = $jvzoo_products[$result['product_id']];

                if(!empty($rsul)){
                    $plan = $rsul->getActivePlan();
                    $_plan = $plan->to_array();
                    if($_plan['id'] >= $planId){
                        $log->logInfo(__FUNCTION__ .' User has higher plan, exiting..');
                        break;
                    }
                }            
 
                $user = User::findByEmail($result['username']);
                foreach ($user->access_token->get() as $access_token) {
                    $access_token->delete();
                }
                $this->ion_auth->delete_user($user->id);

                $sender = $this->get('core.mail.sender');
                $sender->sendUserDeleteMail(array('user' => $user));

                $user_subscription= new Subscription;
                $user_subscription->where('user_id', $user->id)->get()->delete_all();

                // $payment_transaction= new Payment_transaction;
                // $payment_transaction->where('user_id', $user->id)->get()->delete_all();
                $log->logInfo(__FUNCTION__ .' Refund Task Succeded'); 
                break;
            }
        }
        $log->logInfo('--------------------------------------------------------');
        header( "HTTP/1.1 200 OK" );
    }  

    public function getResponse($name, $email){
        $apikey = '8b876de0f250c776cd9f393eca6957be';
        $campaignId = 'T5l3l';
        $params= array(
                  'name' => $name,
                  'email' => $email,
                  'dayOfCycle' => '10',
                  'campaign'  => array('campaignId' => $campaignId),
                  'ipAddress' =>  '192.168.1.1',
            
        );
        $str_params = json_encode($params);
        //var_dump($str_params); die();
        if(!empty($apikey) && !empty($campaignId)){
            $url = "https://api.getresponse.com/v3";
            $url .= "/contacts";
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $str_params,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => $url,
                CURLOPT_HTTPHEADER => array(    'Content-Type: application/json',                                                                                
                                                'Content-Length: ' . strlen($str_params),
                                                 'X-Auth-Token: api-key '.$apikey)

            ));
            $response = curl_exec($curl);
            // Close request to clear up some resources
            curl_close($curl);  
            return $response;      
        }
    }
}
