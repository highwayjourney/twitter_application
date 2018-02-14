<?php if (!defined('BASEPATH'))
    dir('No direct script access allowed');


class Social_campaigns_item extends DataMapper
{
    var $has_one = array(
        'social_campaign',
        'social_trivia'
    );
    var $has_many = array();

    var $validation = array();

    var $created_field = 'created';
    
    var $updated_field = 'updated';
    
    var $table = 'social_campaigns_items';

    /**
     * Initialize social_campaigns_item model
     *
     * @access public
     *
     * @param $id (int)                     - id
     * @param $social_campaign_id (int)     - campaign id
     * @param $background_image (string)    - image link
     * @param $quote (string)               - quote text
     * @param $final_image (string)         - final image
     * @param $schedule_date (timestamp)    - scheduled time for posting of image to social media
     * @param $status (string)              - pending, submitted, failed
     *
     */
    public function __construct($id = null)
    {
        parent::__construct($id);
    }
}
