|************************************************************************************************|
|*	CLASS NAME	: PHP SECURE PAYMENT GATEWAY												   	*|
|*	DEVELOPER	: Yahkal Fisher <info@lyndact.us>		        				  	*|
|*	LICENSE		: GPL(Generic Public License)												 	*|
|*	DATE		: 23 June 2015								   							    	*|
|*	DESCRIPTION	: This class uses the Authorize.Net SIM API to provide 					   		*|
|* 				  a secure payment platform on any website. It provides methods 		  		*|
|*				  for customizing the payment page to suite the merchant's website       		*|
|*				  in terms of color and layout.													*|
|*				  It also provides methods for changing form labels, this would make   			*|
|*				  it easy to translate the payment form into different languages	  			*|
|*				  depending on the customers locale.											*|
 ************************************************************************************************

CLASS OBJECTIVE:	Provide A PHP Interface / Object Oriented Implementation For The Authorize.Net SIM API		

CLASS ATTRIBUTES:	
			private $login; The API_LOGIN_ID, set this value in the auth.ini file, if you want to use it in a production environment

			private $key;  The TRANSACTION_KEY, also set this value in the auth.ini file

			private $amount; Total recurring cost of the transaction to be made

			private $sequence; A uniquely generated value, required for figerprint hash generation

			private $timestamp; 

			private $fingerprint; A Uniquely generated Hashed value as certificate

			private $customerid; Customer's ID

			private $receiptlinkmethod; Method for receipt generation (GET, LINK or POST)

			private $receiptlinktext; Text to show on receipt generation link button or anchor

			private $receiptlinkurl; URL of the page containing the generated receipt
CLASS METHODS:		
			__Construct():	Method responsible for the instantiation of the class, it accepts an array parameter

			_initializeGateway():	Accepts 5 parameters and is private method, the user need not worry about this method

			 __keyExists():	checks if a key exists in an array, returns a boolean value and is a private method

			customizeForm():	for changing the look and feel of the generated form. Accepts a single parameter which must be an array

			renameFormFields(): for changing the label texts on the generated form. It accepts an array which contains the field label to change and the value to change to.

			
			ALL OTHER METHODS BELOW EXCEPT THE COMPLETE TRANSACTION METHOD, GENERATES AN HTML FORM WHICH IS NEEDED TO
			PROCESS PAYMENT ON THE AUTHORIZE.NET PLATFORM. THEY ARE ALL PRIVATE METHODS
		

			_initGateway();
			_postData();
			_merchantCustomizationInterface();
			_merchantCustomizationDisplay();


			completeTransaction(): This method, completeTransaction(), displays and hidden form and a button which takes the user to the payment page



***ADDITIONAL INFORMATION:

You should register on the Authorize.Net website if you want to use this class in a production environment
in order to get your developer API_LOGIN_ID and your TRANSACTION_KEY.
Once you get them, edit the auth.ini file and insert them into the right field.
		
HAVE FUN WHILE CODING, DON'T FORGET TO RATE THIS PACKAGE ON THE PHPCLASSES SITE AND YOU CAN ALWAYS FIND
ME ON ONE OF THE ADDRESSES BELOW IF YOU HAVE A QUESTION OR YOU JUST WANT TO TALK ABOUT ENNGINEERING SOFTWARES!

facebook => 
email => info@lyndact.us
twitter =>
whatsapp =>
