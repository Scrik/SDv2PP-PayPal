<?php

namespace Arrow768\Sdv2ppPaypal;

class paypal_handler
{

    var $fields = array();

    function add_field($field, $value)
    {
        //Adds a field that will be sent to paypal
        $this->fields["$field"] = $value;
    }

    function submit_paypal_post()
    {
        echo "<html>\n<head><title>Processing Payment...</title></head>\n";
        echo "<body onLoad=\"document.forms['paypal_form'].submit();\">\n";
        echo "<center><h2>Please wait, your order is being processed and you will be redirected to the paypal website.</h2></center>\n";
        echo "<form method=\"post\" name=\"paypal_form\" action=\"" . $this->paypal_url . "\">\n";
        foreach ($this->fields as $name => $value)
        {
            echo "<input type=\"hidden\" name=\"$name\" value=\"$value\"/>\n";
        }
        echo "<center><br/><br/>If you are not automatically redirected to paypal within 5 seconds...<br/><br/>\n";
        echo "<input type=\"submit\" value=\"Click Here\"></center>\n";
        echo "</form>\n</body></html>\n";
    }

    function dump_fields()
    {
        echo "<h3>paypal_class->dump_fields() Output:</h3>";
        echo "<table width=\"95%\" border=\"1\" cellpadding=\"2\" cellspacing=\"0\">
            <tr>
               <td bgcolor=\"black\"><b><font color=\"white\">Field Name</font></b></td>
               <td bgcolor=\"black\"><b><font color=\"white\">Value</font></b></td>
            </tr>";

        ksort($this->fields);
        foreach ($this->fields as $key => $value)
        {
            echo "<tr><td>$key</td><td>" . urldecode($value) . "&nbsp;</td></tr>";
        }

        echo "</table><br>";
    }

}