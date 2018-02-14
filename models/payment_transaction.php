<?php

class Payment_transaction extends DataMapper {

    const STATUS_PENDING = 0;
    const STATUS_COMPLETED = 1;

    const TEST_MODE = 1;

    var $table = 'payment_transactions';
    
    var $has_one = array('subscription', 'user');

    var $has_many = array();

    var $validation = array();

    public function __construct($id = null)
    {
        $this->status = self::STATUS_PENDING;

        parent::__construct($id);

    }

    /**
     * Is transaction completed
     *
     * @return bool
     */
    public function isCompleted()
    {
        return $this->status == self::STATUS_COMPLETED;
    }

    /**
     * Is transaction pending
     *
     * @return bool
     */
    public function isPending()
    {
        return $this->status == self::STATUS_PENDING;
    }

    /**
     * Is transaction in test mode
     *
     * @return bool
     */
    public function isTest()
    {
        return $this->test_mode == self::TEST_MODE;
    }

    /**
     * Return amount in currency
     * @return float
     * @internal param string $currency
     *
     */
    public function getAmount()
    {
        $factor = 1;
        $currency = strtolower($this->currency);

        switch ($currency) {
            case 'usd':
                $factor = 100;
                break;
        }

        return floatval($this->amount/$factor);
    }

    /**
     * Get formated amount
     *
     * @return string
     */
    public function getFormatedAmount()
    {
        return number_format($this->getAmount(), 2);
    }

    /**
     * Get unique id
     *
     * @return string
     */
    public function getUniqId()
    {
        $parsedUrl = parse_url(site_url());

        $uniquePrefix = $parsedUrl['host'].$parsedUrl['path'];
        $uniquePrefix = trim($uniquePrefix, '/');

        return $uniquePrefix.'_'.$this->id;
    }

    /**
     * Get filtered transactions
     *
     * @param null $limit
     * @param null $offset
     * @param null|string $filter
     * @return DataMapper
     */
    public static function getFiltered($limit = null, $offset = null, $filter = '')
    {
        $result =  static::create()->include_related('user');
        if ($filter != '') {
            $result->where('test_mode', $filter);
        }

        return $result->order_by('created', 'ASC')->get($limit, $offset);
    }
    
    public function updateTransaction($result){
        $this->payment_id = $result['ctransreceipt'];
        $this->updated = $result['ctranstime'];
        $this->description = $result['cprodtitle'];
        $this->amount = $result['ctransamount'];
        $this->save();
    }

    public function newTransaction($result, $user_id){
        $object->payment_id = $result['ctransreceipt'];
        $object->user_id = $user_id;
        $object->amount = $result['ctransamount'];        
        $object->created = $result['ctranstime'];
        $object->updated = $result['ctranstime'];
        $object->status = 1;
        $object->test_mode = 0;
        $object->currency = '$';
        $object->user_info = $result['ccustemail'];
        $object->description = $result['cprodtitle'];

        $transaction = new self;
        foreach (get_object_vars($object) as $key => $value) {
            $transaction->$key = $value;
        }     
        $transaction->save();   
        return $this->db->insert_id();
    }    

    public function checkPayment($transaction_id){
        return $this->where('payment_id', $transaction_id)->get()->to_array();
    }
}