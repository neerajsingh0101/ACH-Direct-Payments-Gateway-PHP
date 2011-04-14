<?php
class Services_ACHDirect_Transaction
{
	const ECHECK = 'ECHECK';
	const CREDIT = 'CREDIT';
	
	/**
	 * get the payment type (credit card or echeck)
	 */
	public function paymentType() {
		
		if ($this->AccountType == "C" || $this->AccountType == "S") {
			return Services_ACHDirect_Transaction::ECHECK;
		} else {	
			return Services_ACHDirect_Transaction::CREDIT;
		}

	}
}
