<?php
	/* IMPORTANT POINT: Make Sure You set your api login id and transaction key
		values in the auth.ini file if you are using this package in a production environment.
		But you can work with the ones that came with this package if you just want to test it
	*/

	//require the AuthorizePayment.class.php file
	require_once("AuthorizePayment.class.php");

	/*
		We need to instantiate the AuthorizePayment_Form class
		contained in the file we included above.

		The constructor accepts one parameter which must be an array.

		This parameter must contain key index of: CUSTOMERID, AMOUNT, METHOD, URL, TEXT.

		* The CUSTOMERID index should be the actual id of the customer and this id will appear on the generated form
		* THE AMOUNT index should be the total recurring amount the transaction worths
		* The METHOD index should be the http method for submitting the form data to the receipt page, it could be
			one of LINK, GET, POST
		* The URL should be the actual url of the receipt page
		* The TEXT should be the text that'ld be displayed on the receipt page link

		for example, a parameter for the constructor method would be

		$parameter = array(
							"CUSTOMERID"=>"1009",
						 	"AMOUNT"=>"43.00",
						 	"METHOD"=>"POST",
						 	"URL"=>"www.mydomain.com.ng",
						 	"TEXT"=>"Click Here To Go Back To Our HomePage"
						 );
		
		Consult the documentation accompanying this package for more info
	*/

	//Class Instantiation
	$parameter = array(
						"CUSTOMERID"=>"1009",
					 	"AMOUNT"=>"19800",
					 	"METHOD"=>"POST",
					 	"URL"=>"www.mydomain.com.ng",
					 	"TEXT"=>"Click Here To Go Back To Our HomePage"
					 );
	$authorizepaymentInstance = new AuthorizePayment_Form($parameter);

	/*
		The generated page can be customized by supplying an approprate value to one of the
		following
			HEADER, FOOTER, LINKCOLOR, BACKGROUNDCOLOR, TEXTCOLOR, LOGO, BACKGROUNDIMAGE
			CANCELURL, CANCELURLTEXT, CUSTOMCSS, DESCRIPTION, SUBMITTEXT indexes of an array

		for instance to set the backgroundcolor to blue, the following would be passed
		as parameter to the customizeForm() method:
		$array = array("BACKGROUNDCOLOR"=>"#00f");


		consult the documentation for more information
	*/

	//Below is an example initialization of the customizeForm() method
	$customParams = array(
				"HEADER"=>"PLEASE FILL IN THIS FORM TO PAY FOR THE BEST ITEM MONEY CAN BUY!",
				"FOOTER"=>"Please Note That Your Credit Card Details Are Safe",
				"BACKGROUNDCOLOR"=>"#87CEFA",
				"LINKCOLOR"=>"#f00",
				"TEXTCOLOR"=>"#000",
				"LOGO"=>"https://sp.yimg.com/ib/th?id=JN.VacOaoed%2b6TU2B3wRrmgCQ&pid=15.1&P=0",
				"CANCELURL"=>"http://www.mydomain.com",
				"CANCELURLTEXT"=>"Cancel, I don't have my card details yet",
				"DESCRIPTION"=>"Arduino Kit for PHP Programmers ;)",
				"SUBMITTEXT"=>"Click Here To Enter Your Payment Details"
		);
	$authorizepaymentInstance->customizeForm($customParams);

	/*
		You may want to change the labels of a particular field on the generated form,
		the renameFormFields() method helps you do this by supplying an array with the 
		field to change and the new value as parameters.

		The following are the default names for the field labels: 
		RECURRINGBILLING, CURRENCYCODE, INVOICENUMBER, DESCRIPTION, FIRSTNAME, LASTNAME,
		COMPANY, ADDRESS, CITY, STATE, ZIP, COUNTRY, PHONE, FAX, EMAIL, CUSTOMERID,
		SHIPTOFIRSTNAME, SHIPTOLASTNAME, SHIPTOCOUNTRY, SHIPTOADDRESS, SHIPTOCITY,
		SHIPTOSTATE, SHIPTOZIP, TAX, FREIGHT, DUTY, TAXEXEMPT, PURCHASENUMBER
	*/

	//The Example below demonstrates how to rename a label

	$renameParams = array("DESCRIPTION"=>"WHAT IS THIS?", "FIRSTNAME"=>"Enter Your FirstName", "PHONE"=>"Mobile Number");
	$authorizepaymentInstance->renameFormFields($renameParams);


	/*
		The next thing is display a button that takes the user to the form.
		This can be done by instantiating the completeTransaction() method.

		It accepts no parameter
	*/

	$authorizepaymentInstance->completeTransaction();


	/*
		..... And thats all. The user is redirected to the authorize.net website where he/she
		can complete the form to make payments. After that, the user is redirected back to the
		URL you supplied in the constructor parameter.


		You should go through the readme file accompanying this package, if you need a quick
		explanation about a method or the class as a whole Or you can ask for more help
		on this class' message forum on the PHPClasses site.
	*/


?>