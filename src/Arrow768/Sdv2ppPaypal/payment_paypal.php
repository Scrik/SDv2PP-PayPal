<?php

namespace Arrow768\Sdv2ppPaypal;

/**
 * SDv2 Payment Provider for Paypal
 * 
 * A PayPal Payment Provider for SDv2
 * Contains the required functions that are used by SDv2
 */
class payment_paypal
{

    /**
     * Returns a array with information about this payment provider
     * 
     * @return mixed Returns a Array of the handled payment providers
     * 
     */
    function get_paymentprovider_info()
    {
        $provider = array(
            "id" => "paypal",
            "name" => "PayPal",
            "description" => "PayPal is a global e-commerce business allowing payments and money transfers to be made through the Internet.",
        );
        return $provider;
    }

    /**
     * Initiates the payment with the selected payment provider
     * 
     * @param int $amount The amount of the payment
     * @param string $transaction_id The transaction ID of the payment
     * @param string $currency The currency of the Transaction 3Chars
     * @param mixed $attrs Array with optional attrs
     */
    function initiate_payment($amount, $transaction_id, $currency, $attrs=array())
    {

        $item_name = 'Order ID: ' . $transaction_id;

        $p = new paypal_handler;

        if (\Config::get('itemsviewer.payment_paypal_sandbox') == "enabled")
        {
            $p->paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
        }
        else
        {
            $p->paypal_url = "https://www.paypal.com/cgi-bin/webscr";
        }

        $p->add_field('custom', $transaction_id); // Add the transaction ID here
        $p->add_field('no_shipping', '1'); //No shipping is needed for the payment
        $p->add_field('business', \Config::get('itemsviewer.payment_paypal_email')); //the receiver of the payment
        $p->add_field('return', \URL::to('payment/success')); // redirect user to the success page when he made the paypal payment
        $p->add_field('cancel_return', \URL::to('payment/cancel')); // redirect user to the cancel page when he has aborted the payment
        $p->add_field('notify_url', \URL::to('ipn/paypal')); // the IPN Processer
        $p->add_field('item_name', $item_name); //the name of the item
        $p->add_field('amount', $amount); //the price of the item
        $p->add_field('currency_code', $currency); //the currency of the price
        $p->add_field('rm', '2'); // the return method; 2 = Post
        $p->add_field('cmd', '_donations'); //the payment is a donation
        $p->submit_paypal_post(); //submits the post
        if (\Config::get('itemsviewer.payment_paypal_debug') == "enabled")
        {
            $p->dump_fields();
        }
    }

    /**
     * Processes the IPN
     * 
     * TODO: Perform Fraund checks
     * 
     * @return string Returns valid or invalid if the payment is valid/invalid
     */
    function process_ipn()
    {

        $logfile = "pp_ipn_log.txt";
        $fh = fopen($logfile, 'w');
        fwrite($fh, 'New IPN \n');

        $listener = new ipnlistener; // Create a new ipn listener

        if (\Config::get('itemsviewer.payment_paypal_sandbox') == "enabled")
            $listener->use_sandbox = true; //check if sandbox mode is enabled

        try
        {
            $verified = $listener->processIpn(); // try to verify the IPN
        }
        catch (Exception $e)
        {
            // fatal error trying to process IPN.
            exit(0);
        }

        fwrite($fh, $listener->getTextReport());

        if ($verified)
        {
            fwrite($fh, '\n valid \n\n\n');
            return "valid";
        }
        else
        {
            fwrite($fh, '\n invalid \n\n\n');
            return "invalid";
        }
    }

}

?>