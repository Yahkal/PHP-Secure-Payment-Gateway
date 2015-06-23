<?php
/************************************************************************************************\
|*	CLASS NAME	: PHP SECURE PAYMENT GATEWAY												   	*|
|*	DEVELOPER	: Samuel Adeshina <samueladeshina73@gmail.com>		        				  	*|
|*	LICENSE		: GPL(Generic Public License)												 	*|
|*	DATE		: 23 June 2015								   							    	*|
|*	DESCRIPTION	: This class uses the Authorize.Net SIM API to provide 					   		*|
|* 				  a secure payment platform on any website. It provides methods 		  		*|
|*				  for customizing the payment page to suite the merchant's website       		*|
|*				  in terms of color and layout.													*|
|*				  It also provides methods for changing form labels, this would make   			*|
|*				  it easy to translate the payment form into different languages	  			*|
|*				  depending on the customers locale.											*|
\************************************************************************************************/
	require_once("sdk/autoload.php");
	class AuthorizePayment_Form extends AuthorizeNetSIM_Form
	{
		private $login; //The API_LOGINID, set this value in the auth.ini file, if you want to use it in a production environment
		private $key;  //The TRANSACTION_KEY, also set this value in the auth.ini file
		private $amount; //Total recurring amount of the transaction to be made
		private $sequence; //A uniquely generated value, required for figerprint hash generation
		private $timestamp; 
		private $fingerprint; //A Uniquely generated Hashed value as certificate
		private $customerid; //Customer's ID
		private $receiptlinkmethod; //Method for receipt generation (GET, LINK or POST)
		private $receiptlinktext; //Text to show on receipt generation link
		private $receiptlinkurl; //URL of the page containing the generated receipt
		private $_customizationFieldNames = array(
												"HEADER"=>"x_header_html_payment_form",
												"FOOTER"=>"x_footer_html_payment_form",
												"BACKGROUNDCOLOR"=>"x_color_background",
												"LINKCOLOR"=>"x_color_link",
												"TEXTCOLOR"=>"x_color_text",
												"LOGO"=>"x_logo_url",
												"BACKGROUNDIMAGE"=>"x_background_url",
												"CANCELURL"=>"x_cancel_url",
												"CANCELURLTEXT"=>"x_cancel_url _text",
												"CUSTOMCSS"=>"x_header_html_payment_form",
												"DESCRIPTION"=>"x_description"
											);
		private $_customizationFieldValues = array(
												"HEADER"=>NULL,
												"FOOTER"=>NULL,
												"BACKGROUNDCOLOR"=>NULL,
												"LINKCOLOR"=>NULL,
												"TEXTCOLOR"=>NULL,
												"LOGO"=>NULL,
												"BACKGROUNDIMAGE"=>NULL,
												"CANCELURL"=>NULL,
												"CANCELURLTEXT"=>NULL,
												"CUSTOMCSS"=>NULL,
												"DESCRIPTION"=>NULL,
												"SUBMITTEXT"=>NULL
											);
			private $_renameParamsNames = array(
												"RECURRINGBILLING"=>"x_recurring_billing",
												"CURRENCYCODE"=>"x_currency_code",
												"INVOICENUMBER"=>"x_invoice_num",
												"DESCRIPTION"=>"x_description",
												"FIRSTNAME"=>"x_first_name",
												"LASTNAME"=>"x_last_name",
												"COMPANY"=>"x_company",
												"ADDRESS"=>"x_address",
												"CITY"=>"x_city",
												"STATE"=>"x_state",
												"ZIP"=>"x_zip",
												"COUNTRY"=>"x_country",
												"PHONE"=>"x_phone",
												"FAX"=>"x_fax",
												"EMAIL"=>"x_email",
												"CUSTOMERID"=>"x_cust_id",
												"SHIPTOFIRSTNAME"=>"x_ship_to_first_name",
												"SHIPTOLASTNAME"=>"x_ship_to_last_name",
												"SHIPTOCOMPANY"=>"x_ship_to_company",
												"SHIPTOADDRESS"=>"x_ship_to_address",
												"SHIPTOCITY"=>"x_ship_to_city",
												"SHIPTOSTATE"=>"x_ship_to_state",
												"SHIPTOZIP"=>"x_ship_to_zip",
												"SHIPTOCOUNTRY"=>"x_ship_to_country",
												"TAX"=>"x_tax",
												"FREIGHT"=>"x_freight",
												"DUTY"=>"x_duty",
												"TAXEXEMPT"=>"x_tax_exempt",
												"PURCHASENUMBER"=>"x_po_num"
											);
		private $_renameParamsValues = array(
											"RECURRINGBILLING"=>NULL,
											"CURRENCYCODE"=>NULL,
											"INVOICENUMBER"=>NULL,
											"DESCRIPTION"=>NULL,
											"FIRSTNAME"=>NULL,
											"LASTNAME"=>NULL,
											"COMPANY"=>NULL,
											"ADDRESS"=>NULL,
											"CITY"=>NULL,
											"STATE"=>NULL,
											"ZIP"=>NULL,
											"COUNTRY"=>NULL,
											"PHONE"=>NULL,
											"FAX"=>NULL,
											"EMAIL"=>NULL,
											"CUSTOMERID"=>NULL,
											"SHIPTOFIRSTNAME"=>NULL,
											"SHIPTOLASTNAME"=>NULL,
											"SHIPTOCOMPANY"=>NULL,
											"SHIPTOADDRESS"=>NULL,
											"SHIPTOCITY"=>NULL,
											"SHIPTOSTATE"=>NULL,
											"SHIPTOZIP"=>NULL,
											"SHIPTOCOUNTRY"=>NULL,
											"TAX"=>NULL,
											"FREIGHT"=>NULL,
											"DUTY"=>NULL,
											"TAXEXEMPT"=>NULL,
											"PURCHASENUMBER"=>NULL
										);
		private $_defaultParams = array(
											"x_login"=>NULL, "x_fp_hash"=>NULL, "x_amount"=>NULL, "x_fp_timestamp"=>NULL, "x_fp_sequence"=>NULL,
											"x_version"=>NULL, "x_show_form"=>NULL, "x_test_request"=>NULL, "x_method"=>NULL
										);
		//The constructor: accepts a single parameter (must be an array)
		public function __construct($init)
		{
			$auth = parse_ini_file("auth.ini");
			$this->login = $auth["API_LOGIN_ID"];
			$this->key = $auth["TRANSACTION_KEY"];
			$this->timestamp = time();
			$this->sequence = rand(10, 1000) * $this->timestamp;
			self::_initializeGateway($init["CUSTOMERID"], $init["AMOUNT"], $init["METHOD"], $init["TEXT"], $init["URL"]);
			$this->fingerprint = parent::getFingerPrint($this->login, $this->key, $this->amount, $this->sequence, $this->timestamp);
			$this->_defaultParams["x_login"] = $this->login;
			$this->_defaultParams["x_fp_hash"] = $this->fingerprint;
			$this->_defaultParams["x_amount"] = $this->amount;
			$this->_defaultParams["x_fp_timestamp"] = $this->timestamp;
			$this->_defaultParams["x_fp_sequence"] = $this->sequence;
			$this->_defaultParams["x_version"] = "3.1";
			$this->_defaultParams["x_show_form"] = "payment_form";
			$this->_defaultParams["x_test_request"] = "false";
			$this->_defaultParams["x_method"] = "cc";
		}

		//_initializeGateway(...) method. Accepts 5 parameters and is private method, the user need not worry about this method
		private function _initializeGateway($customerid, $amount, $receiptlinkmethod, $receiptlinktext, $receiptlinkurl)
		{
			if ($receiptlinktext == '')
			{
				$receiptlinktext = "Click To Go Back To Our Homepage";
			}
			if ($receiptlinkmethod == '')
			{
				$receiptlinkmethod = "GET";
			}
			$this->amount = $amount;
			$this->customerid = $customerid;
			$this->receiptlinkurl = $receiptlinkurl;
			$this->receiptlinktext = $receiptlinktext;
			$this->receiptlinkmethod = $receiptlinkmethod;
		}

		//__keyExists method: checks if a key exists in an array, returns a boolean value and is a private method
		private function __keyExists($key, $keyArray)
		{
			if (array_key_exists($key, $keyArray))
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		//The customizeForm() Method: for changing the look and feel of the generated form.
		//Accepts a single parameter which must be an array
		public function customizeForm($arrayParams)
		{
			try
			{	
				foreach ($this->_customizationFieldValues as $key => $value) {
					if (!self::__keyExists($key, $arrayParams))
					{
						$this->_customizationFieldValues[$key] = false;
					}
					else
					{
						$this->_customizationFieldValues[$key] = $arrayParams[$key];
					}					
				}
				if (!self::__keyExists("SUBMITTEXT", $arrayParams))
				{
					$this->_customizationFieldValues["SUBMITTEXT"] = "Click here for the secure payment form";
				}
			}	
			catch (Exception $e)
			{
				return $e;
			}
		}

		//THe renameFormFields() Method: for changing the label texts on the generated form. It accepts an array which contains
		//the field label to change and the value to change to.
		public function renameFormFields($arrayParams)
		{
			try
			{	
				foreach ($this->_renameParamsValues as $key => $value) {
					if (!self::__keyExists($key, $arrayParams))
					{
						$this->_renameParamsValues[$key] = false;
					}
					else
					{
						$this->_renameParamsValues[$key] = $arrayParams[$key];
					}					
				}
			}	
			catch (Exception $e)
			{
				return $e;
			}
		}
		/*
			ALL OTHER METHODS BELOW EXCEPT THE COMPLETE TRANSACTION METHOD, GENERATES AN HTML FORM WHICH IS NEEDED TO
			PROCESS PAYMENT ON THE AUTHORIZE.NET PLATFORM. THEY ARE ALL PRIVATE METHODS
		*/
		private function _initGateway()
		{
			$string = "<input type='hidden' name='x_cust_id' value='".$this->customerid."' />";
			$string .= "<input type='hidden' name='x_receipt_link_method' value='".strtoupper($this->receiptlinkmethod)."' />";
			$string .= "<input type='hidden' name='x_receipt_link_text' value='".$this->receiptlinktext."' />";
			$string .= "<input type='hidden' name='x_receipt_link_URL' value='".$this->receiptlinkurl."' />";
			return $string;
		}
		private function _postData()
		{
			echo "<form method='post' action=\"https://test.authorize.net/gateway/transact.dll\">";
			foreach ($this->_defaultParams as $key => $value)
			{
				echo "<input type='hidden' name='$key' value='$value' />";
			}
			echo self::_initGateway().self::_merchantCustomizationInterface().self::_merchantCustomizationDisplay();
			echo "</form>";
		}

		private function _merchantCustomizationInterface()
		{
			$string = "";
			foreach ($this->_customizationFieldNames as $key => $value)
			{
				if ($this->_customizationFieldValues[$key])
				{
					$name = $value;
					$value = $this->_customizationFieldValues[$key];
					$string .= "<input type='hidden' name='$name' value = '$value' />";
				}
			}
			$string .= "<input type ='submit' value='".$this->_customizationFieldValues["SUBMITTEXT"]."' />";
			return $string;
		}
		private function  _merchantCustomizationDisplay()
		{
			$string = "";
			foreach ($this->_renameParamsNames as $key => $value)
			{
				if ($this->_renameParamsValues[$key] && $this->_renameParamsValues[$key] != NULL)
				{
					$old = $value;
					$new = $this->_renameParamsValues[$key];
					$string .= "<input type='hidden' name='x_rename' value='$old, $new' />";
				}
			}
			return $string;
		}

		//This method, completeTransaction(), displays an hidden form and a button which takes the user to
		//the payment page
		public function completeTransaction()
		{
			return self::_postData();
		}
	}
?>