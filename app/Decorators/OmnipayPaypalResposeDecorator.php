<?php namespace App\Decorators;


use Omnipay\PayPal\Message\Response;

class OmnipayPaypalResposeDecorator {

    /**
     * @var Response
     */
    protected $response;

    function __construct(Response $response)
    {
        $this->response = $response;
    }

    /**
     * Handle dynamic method calls on the response
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        $result = call_user_func_array(array($this->response, $method), $parameters);

        if ($result === $this->response) return $this;

        return $result;
    }

    public function isPending()
    {
        return $this->getStatus() == 'Pending';
    }

    public function getStatus()
    {
        $data = $this->response->getData();
        return $data['PAYMENTINFO_0_PAYMENTSTATUS'];
    }

    public function getAmount()
    {
        $data = $this->response->getData();
        return $data['PAYMENTINFO_0_AMT'];
    }

}