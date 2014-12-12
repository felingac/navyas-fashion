<?php
/**
 * Main payment controller
 *
 * @category    Controller
 * @package     Junaidbhura_Ccavenue
 * @author      Junaid Bhura <info@junaidbhura.com>
 */

class Junaidbhura_Ccavenue_PaymentController extends Mage_Core_Controller_Front_Action {
    // The cancel action is triggered when someone cancels a payment in CC Avenue
    public function cancelAction() {
        if ( Mage::getSingleton( 'checkout/session' )->getLastRealOrderId() ) {
            $order = Mage::getModel( 'sales/order' )->loadByIncrementId( Mage::getSingleton( 'checkout/session' )->getLastRealOrderId() );
            if ( $order->getId() ) {
                // Flag the order as 'cancelled' and save it
                $order->cancel()->setState( Mage_Sales_Model_Order::STATE_CANCELED, true, 'CCAvenue has declined the payment.' )->save();
            }
        }
    }

    // The review action is triggered when CCAvenue sends an AuthDesc as B
    public function reviewAction() {
        if ( Mage::getSingleton( 'checkout/session' )->getLastRealOrderId() ) {
            $order = Mage::getModel( 'sales/order' )->loadByIncrementId( Mage::getSingleton( 'checkout/session' )->getLastRealOrderId() );
            if ( $order->getId() ) {
                // Flag the order as 'payment review' and save it
                $order->setState( Mage_Sales_Model_Order::STATE_PAYMENT_REVIEW, true, 'CCAvenue has sent AuthDesc as B.' );
                $order->save();
            }
        }
    }

    // The redirect action is triggered when someone places an order
    public function redirectAction() {
        // Retrieve order
        $order = new Mage_Sales_Model_Order();
        $ccavenue['order_id'] = Mage::getSingleton( 'checkout/session' )->getLastRealOrderId();
        $order->loadByIncrementId( $ccavenue['order_id'] );

        // Get CCAvenue Parameters
        $ccavenue['action'] = Mage::getStoreConfig( 'payment/ccavenue/submit_url' );
        $ccavenue['merchant_id'] = Mage::getStoreConfig( 'payment/ccavenue/merchant_id' );
        $ccavenue['amount'] = round( $order->base_grand_total, 2 );
        $ccavenue['redirect_url'] = Mage::getBaseUrl() . 'ccavenue/payment/response';
        $ccavenue['working_key'] = Mage::getStoreConfig( 'payment/ccavenue/working_key' );
        $ccavenue['checksum'] = $this->getCheckSum( $ccavenue['merchant_id'], $ccavenue['amount'], $ccavenue['order_id'], $ccavenue['redirect_url'], $ccavenue['working_key'] );

        // Retrieve order details
        $billingAddress = $order->getBillingAddress();
        $billingData = $billingAddress->getData();
        $shippingAddress = $order->getShippingAddress();
        if ( $shippingAddress )
            $shippingData = $shippingAddress->getData();

        $ccavenue['billing_cust_name'] = $billingData['firstname'] . ' ' . $billingData['lastname'];
        $ccavenue['billing_cust_address'] = $billingAddress->street;
        $ccavenue['billing_cust_state'] = $billingAddress->region;
        $ccavenue['billing_cust_country'] = Mage::getModel( 'directory/country' )->load( $billingAddress->country_id )->getName();
        $ccavenue['billing_cust_tel'] = $billingAddress->telephone;
        $ccavenue['billing_cust_email'] = $order->customer_email;
        if ( $shippingAddress ) {
            $ccavenue['delivery_cust_name'] = $shippingData['firstname'] . ' ' . $shippingData['lastname'];
            $ccavenue['delivery_cust_address'] = $shippingAddress->street;
            $ccavenue['delivery_cust_state'] = $shippingAddress->region;
            $ccavenue['delivery_cust_country'] = Mage::getModel( 'directory/country' )->load( $shippingAddress->country_id )->getName();
            $ccavenue['delivery_cust_tel'] = $shippingAddress->telephone;
            $ccavenue['delivery_city'] = $shippingAddress->city;
            $ccavenue['delivery_zip'] = $shippingAddress->postcode;
        }
        else {
            $ccavenue['delivery_cust_name'] = '';
            $ccavenue['delivery_cust_address'] = '';
            $ccavenue['delivery_cust_state'] = '';
            $ccavenue['delivery_cust_country'] = '';
            $ccavenue['delivery_cust_tel'] = '';
            $ccavenue['delivery_city'] = '';
            $ccavenue['delivery_zip'] = '';
        }
        $ccavenue['merchant_param'] = '';
        $ccavenue['billing_city'] = $billingAddress->city;
        $ccavenue['billing_zip'] = $billingAddress->postcode;
        $ccavenue['billing_cust_notes'] = '';

        // Insert into CCAvenue Response Log Table
        $now = Mage::getModel('core/date')->timestamp( time() );
        Mage::getModel( 'ccavenue/ccavenueredirect' )
            ->setMerchantId( $ccavenue['merchant_id'] )
            ->setAmount( $ccavenue['amount'] )
            ->setOrderId( $ccavenue['order_id'] )
            ->setRedirectUrl( $ccavenue['redirect_url'] )
            ->setChecksum( $ccavenue['checksum'] )
            ->setBillingCustName( addslashes( $ccavenue['billing_cust_name'] ))
            ->setBillingCustAddress( addslashes( $ccavenue['billing_cust_address'] ))
            ->setBillingCustCountry( addslashes( $ccavenue['billing_cust_country'] ))
            ->setBillingCustState( addslashes( $ccavenue['billing_cust_state'] ))
            ->setBillingZip( $ccavenue['billing_zip'] )
            ->setBillingCustTel( $ccavenue['billing_cust_tel'] )
            ->setBillingCustEmail( $ccavenue['billing_cust_email'] )
            ->setDeliveryCustName( addslashes( $ccavenue['delivery_cust_name'] ))
            ->setDeliveryCustAddress( addslashes( $ccavenue['delivery_cust_address'] ))
            ->setDeliveryCustCountry( addslashes( $ccavenue['delivery_cust_country'] ))
            ->setDeliveryCustState( addslashes( $ccavenue['delivery_cust_state'] ))
            ->setDeliveryCustTel( $ccavenue['delivery_cust_tel'] )
            ->setBillingCustNotes( $ccavenue['billing_cust_notes'] )
            ->setMerchantParam( $ccavenue['merchant_param'] )
            ->setBillingCustCity( addslashes( $ccavenue['billing_city'] ))
            ->setBillingZipCode( $ccavenue['billing_zip'] )
            ->setDeliveryCustCity( addslashes( $ccavenue['delivery_city'] ))
            ->setDeliveryZipCode( $ccavenue['delivery_zip'] )
            ->setCcavenueRedirectIp( $this->get_uer_ip())
            ->setCcavenueRedirectDtime( date( 'Y-m-d H:i:s', $now ))
            ->save();

        // Add data to registry so it's accessible in the view file
        Mage::register( 'ccavenue', $ccavenue );

        // Render layout
        $this->loadLayout();
        $block = $this->getLayout()->createBlock( 'Mage_Core_Block_Template', 'ccavenue', array( 'template' => 'ccavenue/redirect.phtml' ) );
        $this->getLayout()->getBlock( 'content' )->append( $block );
        $this->renderLayout();
    }

    // The response action is triggered when CC Avenue sends a response
    public function responseAction() {
        if($this->getRequest()->isPost()) {
            // Retrieve POST Values
            // Retrieve POST Values
            $working_key = Mage::getStoreConfig( 'payment/ccavenue/working_key' );
            //$merchant_id = $this->getRequest()->getPost( 'Merchant_Id' );
            //$amount = $this->getRequest()->getPost( 'Amount' );
            //$order_id = $this->getRequest()->getPost( 'Order_Id' );
            $merchant_param = $this->getRequest()->getPost( 'Merchant_Param' );
            //$checksum = $this->getRequest()->getPost( 'Checksum' );
            //$auth_desc = $this->getRequest()->getPost( 'AuthDesc' );
            $card_category = $this->getRequest()->getPost( 'card_category' );
            $bank_name = $this->getRequest()->getPost( 'bank_name' );


            $encResponse=$_POST["encResponse"];			//This is the response sent by the CCAvenue Server


            $rcvdString=$this->decrypt($encResponse,$working_key);		//AES Decryption used as per the specified working key.
            $auth_desc="";
            $merchant_id="";
            $order_id="";
            $amount=0;
            $checksum=0;
            $veriChecksum=false;

            $decryptValues=explode('&', $rcvdString);
            $dataSize=sizeof($decryptValues);

            for($i = 0; $i < $dataSize; $i++)
            {
                $information=explode('=',$decryptValues[$i]);
                if($i==0)	$merchant_id=$information[1];
                if($i==1)	$order_id=$information[1];
                if($i==2)	$amount=$information[1];
                if($i==3)	$auth_desc=$information[1];
                if($i==4)	$checksum=$information[1];
            }

            $rcvdString=$merchant_id.'|'.$order_id.'|'.$amount.'|'.$auth_desc.'|'.$working_key;

            // Check whether AuthQuery is required
            if ( Mage::getStoreConfig( 'payment/ccavenue/enable_auth_query' ) ) {
                // Prepare cURL request
                $ch = curl_init();
                $post_data = 'Merchant_Id=' . Mage::getStoreConfig( 'payment/ccavenue/merchant_id' ) . '&Order_Id=' . $order_id;
                curl_setopt( $ch, CURLOPT_URL, 'http://www.ccavenue.com/servlet/new_txn.OrderStatusTracker' );
                curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
                curl_setopt( $ch, CURLOPT_HEADER, false );
                curl_setopt( $ch, CURLOPT_POST, count( $post_data ) );
                curl_setopt( $ch, CURLOPT_POSTFIELDS, $post_data ) ;

                // Run cURL request
                $order_details = curl_exec( $ch );

                // Close cURL request
                curl_close( $ch );

                // Check for error
                if ( stripos( $order_details, 'error=' ) !== false ) {
                    $auth_desc = 'AuthQuery!';
                }
                else {
                    // No error, check for validity
                    parse_str( $order_details, $order_details );
                    if ( ! isset( $order_details['Order_Id'] ) || ! isset( $order_details['AuthDesc'] ) || $order_details['Order_Id'] != $merchant_id . '-' . $order_id || $order_details['AuthDesc'] != $auth_desc )
                        $auth_desc = 'AuthQuery!';
                }
            }

            // Insert into CCAvenue Response Log Table
            $now = Mage::getModel( 'core/date' )->timestamp( time() );
            Mage::getModel( 'ccavenue/ccavenueresponse' )
                ->setMerchantId( $merchant_id )
                ->setAmount( $amount )
                ->setOrderId( $order_id )
                ->setMerchantParam( $merchant_param )
                ->setChecksum( $checksum )
                ->setAuthdesc( $auth_desc )
                ->setCcavenueResponseIp( $this->get_uer_ip() )
                ->setCardCategory( $card_category )
                ->setBankName( $bank_name )
                ->setCcavenueResponseDtime( date( 'Y-m-d H:i:s', $now ) )
                ->save();
            echo $merchant_id.' '. $order_id.' '. $amount.' '. $auth_desc.' '. $checksum.' '. $working_key;

            $checksum = $this->verifyChecksum( $merchant_id, $order_id, $amount, $auth_desc, $checksum, $working_key );

            // Check response and take appropriate actions
            if ( $checksum == "true" && $auth_desc == "Y" )	{
                echo "checksum == true && auth_desc == Y";die;
                // Payment was successful, so update the order's state, send order email and move to the success page
                $order = Mage::getModel( 'sales/order' );
                $order->loadByIncrementId( $order_id );
                $order->setState( Mage_Sales_Model_Order::STATE_PROCESSING, true, 'CCAvenue has authorized the payment.' );

                $order->sendNewOrderEmail();
                $order->setEmailSent( true );

                $order->save();

                Mage::getSingleton( 'checkout/session' )->unsQuoteId();

                Mage_Core_Controller_Varien_Action::_redirect( 'checkout/onepage/success', array( '_secure' => true ) );
            }
            else if($checksum == "true" && $auth_desc == "B") {
                echo "checksum == true && auth_desc == B";die;
                // Payment was successful as a 'Batch Processing' order. Status of such payments can only be determined after some time
                $this->reviewAction();
                Mage_Core_Controller_Varien_Action::_redirect( 'checkout/onepage/failure', array( '_secure' => true) );
            }
            else if($checksum == "true" && $auth_desc == "N") {
                echo "checksum == true && auth_desc == N";die;
                // CCAvenue has declined the payment, so cancel the order and redirect to fail page
                $this->cancelAction();
                Mage_Core_Controller_Varien_Action::_redirect( 'checkout/onepage/failure', array( '_secure' => true) );
            }
            else {
                echo "CanclAction";die;
                // There is a serious problem in getting a response from CCAvenue
                $this->cancelAction();
                Mage_Core_Controller_Varien_Action::_redirect( 'checkout/onepage/failure', array( '_secure ' => true) );
            }
        }
        else
            Mage_Core_Controller_Varien_Action::_redirect( '' );
    }

    // Function to get user's IP
    private function get_uer_ip() {
        if ( isset( $_SERVER ) ) {
            if ( isset( $_SERVER["HTTP_X_FORWARDED_FOR"] ) )
                return $_SERVER["HTTP_X_FORWARDED_FOR"];

            if ( isset( $_SERVER["HTTP_CLIENT_IP"] ) )
                return $_SERVER["HTTP_CLIENT_IP"];

            return $_SERVER["REMOTE_ADDR"];
        }

        if ( getenv( 'HTTP_X_FORWARDED_FOR' ) )
            return getenv( 'HTTP_X_FORWARDED_FOR' );

        if ( getenv( 'HTTP_CLIENT_IP') )
            return getenv( 'HTTP_CLIENT_IP' );

        return getenv( 'REMOTE_ADDR' );
    }

    /* -------------------- DO NOT EDIT BELOW THIS LINE : CCAVENUE FUNCTIONS -------------------- */
    private function getchecksum($merchant_id, $amount, $order_id, $url, $working_key) {
        $str = "$merchant_id|$order_id|$amount|$url|$working_key";
        $adler = 1;
        $adler = $this->adler32($adler,$str);
        return $adler;
    }

    private function verifychecksum($merchant_id, $order_id, $amount, $auth_desc, $checksum, $working_key) {
        $str = "$merchant_id|$order_id|$amount|$auth_desc|$working_key";
        $adler = 1;
        $adler = $this->adler32($adler,$str);

        if($adler == $checksum)
            return "true" ;
        else
            return "false" ;
    }

    private function adler32($adler , $str) {
        $BASE =  65521 ;

        $s1 = $adler & 0xffff ;
        $s2 = ($adler >> 16) & 0xffff;
        for($i = 0 ; $i < strlen($str) ; $i++)
        {
            $s1 = ($s1 + Ord($str[$i])) % $BASE ;
            $s2 = ($s2 + $s1) % $BASE ;

        }
        return $this->leftshift($s2 , 16) + $s1;
    }

    private function leftshift($str , $num) {

        $str = DecBin($str);

        for( $i = 0 ; $i < (64 - strlen($str)) ; $i++)
            $str = "0".$str ;

        for($i = 0 ; $i < $num ; $i++)
        {
            $str = $str."0";
            $str = substr($str , 1 ) ;
        }
        return $this->cdec($str) ;
    }

    private function cdec($num) {

        for ($n = 0 ; $n < strlen($num) ; $n++)
        {
            $temp = $num[$n] ;
            $dec =  $dec + $temp*pow(2 , strlen($num) - $n - 1);
        }

        return $dec;
    }

    function encrypt($plainText,$key)
    {
        $secretKey = hextobin(md5($key));
        $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);

        /* Open module and Create IV (Intialization Vector) */
        $openMode = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '','cbc', '');
        $blockSize = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, 'cbc');
        $plainPad = pkcs5_pad($plainText, $blockSize);

        /* Initialize encryption handle */
        if (mcrypt_generic_init($openMode, $secretKey, $initVector) != -1)
        {
            /* Encrypt data */
            $encryptedText = mcrypt_generic($openMode, $plainPad);
            mcrypt_generic_deinit($openMode);

        }

        return bin2hex($encryptedText);
    }

    function decrypt($encryptedText,$key)
    {
        $secretKey = $this->hextobin(md5($key));
        $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
        $encryptedText=$this->hextobin($encryptedText);

        /* Open module, and create IV */
        $openMode = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '','cbc', '');


        mcrypt_generic_init($openMode, $secretKey, $initVector);
        $decryptedText = mdecrypt_generic($openMode, $encryptedText);

        // Drop nulls from end of string
        $decryptedText = rtrim($decryptedText, "\0");

        // Returns "Decrypted string: some text here"

        mcrypt_generic_deinit($openMode);

        return $decryptedText;

    }
    function pkcs5_pad ($plainText, $blockSize)
    {
        $pad = $blockSize - (strlen($plainText) % $blockSize);
        return $plainText . str_repeat(chr($pad), $pad);
    }

    //********** Hexadecimal to Binary function for php 4.0 version ********

    function hextobin($hexString)
    {
        $length = strlen($hexString);
        $binString="";
        $count=0;
        while($count<$length)
        {
            $subString =substr($hexString,$count,2);
            $packedString = pack("H*",$subString);
            if ($count==0)
            {
                $binString=$packedString;
            }

            else
            {
                $binString.=$packedString;
            }

            $count+=2;
        }
        return $binString;
    }
    /* -------------------- DO NOT EDIT ABOVE THIS LINE : CCAVENUE FUNCTIONS -------------------- */
}