<?php

/***************************************************************************
	@extension	: Dealer Inquiry Extension.
	@copyright	: Copyright (c) 2014 Capacity Web Solutions.
	( http://www.capacitywebsolutions.com )
	@author		: Capacity Web Solutions Pvt. Ltd.
	@support	: magento@capacitywebsolutions.com	
***************************************************************************/

class CapacityWebSolutions_Inquiry_IndexController extends Mage_Core_Controller_Front_Action
{	
    public function indexAction()
    {
		$this->loadLayout(array('default'));
        $this->getLayout()->getBlock('head')->setTitle($this->__('Wholesale Rugs, Carpet, Cashmere Scarves Online | Pashmina scarves Wholesale | cotton fabric Wholesale'));
        $this->getLayout()->getBlock('head')->setDescription($this->__('NavyasFashion supply wholesale carpet, rugs and wholesale cashmere scarves. We also offer p​ashmina scarves, quilted jackets and block printed cotton fabric online​ at wholesale prices.'));
        $this->getLayout()->getBlock('head')->setKeywords($this->__('Wholesale cashmere scarves, pashmina scarves wholesale, wholesale rugs online, wholesale carpet online, cotton fabric wholesale​'));
		$this->renderLayout();
		
	}

    public function productenquiryAction()
    {
        $productId = $this->getRequest()->getParam('productId');
        $product = Mage:: register  ('productId', $productId);
        $this->loadLayout(array('default'));
        $this->renderLayout();

    }
	
	public function delAction()
	{
		$getUrl=Mage::getSingleton('adminhtml/url')->getSecretKey("adminhtml_mycontroller","delAction");
		$delid = $this->getRequest()->getParam('delid');
		if(!empty($delid))
		{
			$collection = Mage::getModel("inquiry/inquiry")->load($delid);
			
			if($collection->delete())
			{
					
			}
			else
			{
				Mage::getSingleton('core/session')->addError("Sorry inquiry is not deleted.");
			}
		}
		$this->_redirectReferer();
	}
	
	public function thanksAction()
    {
	
		$this->loadLayout(array('default'));
		$this->renderLayout();
		if($_POST['SUBMIT']=='SUBMIT')
		{
		
		$captcha =  $this->getRequest()->getParam("captcha");
		$captcha_code =  $this->getRequest()->getParam("captcha_code");
		if($captcha == $captcha_code)
		{		 
	
		$fname =  $this->getRequest()->getParam("fname");
		$lname =  $this->getRequest()->getParam("lname");
		$company =  $this->getRequest()->getParam("company");
		$taxvat =  $this->getRequest()->getParam("account_taxvat"); 
		$address =  $this->getRequest()->getParam("address");
		$city =  $this->getRequest()->getParam("city");
		$state =  $this->getRequest()->getParam("state_id");
		$country =  $this->getRequest()->getParam("country");
		$zip =  $this->getRequest()->getParam("zip");
		$phone =  $this->getRequest()->getParam("phone");
		$email =  $this->getRequest()->getParam("email");
        $categories =  $this->getRequest()->getParam("categories");
        $productname =  $this->getRequest()->getParam("productname");
        $sku =  $this->getRequest()->getParam("sku");
		$storeid = Mage::app()->getStore()->getStoreId();
		$website =  $this->getRequest()->getParam("website");
		$bdesc =  addslashes($this->getRequest()->getParam("bdesc"));
		$headers = "";
		$country1 = explode('$$$',$country);

		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');
		$query = 'SELECT * FROM ' . $resource->getTableName('dealerinquiry')." where email='".$email."' and storeid='".$storeid."'";

        $results = $readConnection->fetchRow($query);
   
		  if($results == false || $results == true)
		  {

		$insertArr = array("firstname"=>$fname,"lastname"=>$lname,"company"=>$company,"productname"=>$productname,"sku"=>$sku,"address"=>$address,"taxvat"=>$taxvat,"city"=>$city,"state"=>$state,"country"=>$country,"zip"=>$zip,"phone"=>$phone,"email"=>$email,"categories"=>implode(', ', $categories),"storeid"=>$storeid,"website"=>$website,"desc"=>$bdesc,"iscustcreated"=>0,"status"=>1,"createddt"=>date('Y-m-d H:i:s'));
	   	
		$collection = Mage::getModel("inquiry/inquiry");
		
		$collection->setData($insertArr); 
		
		$collection->save();
		
			$first_name = Mage::getStoreConfig('inquiry/change_label/f_name');
		if($first_name){
			$first_name = Mage::getStoreConfig('inquiry/change_label/f_name');
		}else {
			$first_name = "First Name";
		}
		$last_name = Mage::getStoreConfig('inquiry/change_label/l_name'); 
		if($last_name){
			$last_name = Mage::getStoreConfig('inquiry/change_label/l_name');
		}else {
			$last_name = "Last Name";
		}
		$company_name = Mage::getStoreConfig('inquiry/change_label/company_name'); 
		if($company_name){
			$company_name = Mage::getStoreConfig('inquiry/change_label/company_name');
		}else{
			$company_name = "Company Name";
		}
		
		$vat_number = Mage::getStoreConfig('inquiry/change_label/vat_number');
        if($vat_number){
			$vat_number = Mage::getStoreConfig('inquiry/change_label/vat_number');
		}else{
			$vat_number = "TAX/VAT Number";
		} 
	
		$address_name = Mage::getStoreConfig('inquiry/change_label/address'); 
		if($address_name){
			$address_name = Mage::getStoreConfig('inquiry/change_label/address');
		}else{
			$address_name = "Address";
		} 

		$city_name = Mage::getStoreConfig('inquiry/change_label/city'); 
		if($city_name){
			$city_name = Mage::getStoreConfig('inquiry/change_label/city');
		}else{
			$city_name = "City";
		} 

		$state_name = Mage::getStoreConfig('inquiry/change_label/state'); 
		if($state_name){
			$state_name = Mage::getStoreConfig('inquiry/change_label/state');
		}else{
			$state_name = "State/Province";
		} 
		
		$country = Mage::getStoreConfig('inquiry/change_label/country'); 
		if($country){
			$country = Mage::getStoreConfig('inquiry/change_label/country');
		}else{
			$country = "Country";
		} 
		$postal_code = Mage::getStoreConfig('inquiry/change_label/postal_code'); 
		if($postal_code){
			$postal_code = Mage::getStoreConfig('inquiry/change_label/postal_code');
		}else{
			$postal_code = "ZIP/Postal Code";
		} 
				
		$contact_number = Mage::getStoreConfig('inquiry/change_label/contact_number'); 
		if($contact_number){
			$contact_number = Mage::getStoreConfig('inquiry/change_label/contact_number');
		}else{
			$contact_number = "Contact Number";
		} 
					
		$email_name = Mage::getStoreConfig('inquiry/change_label/email'); 
		if($email_name){
			$email_name = Mage::getStoreConfig('inquiry/change_label/email');
		}else{
			$email_name = "Email";
		}

        $categories_name =  "Interested In";
        $productname_name =  "Enquiry For Product";
        $sku_name =  "SKU";

		$website_name = Mage::getStoreConfig('inquiry/change_label/website');
		if($website_name){
			$website_name = Mage::getStoreConfig('inquiry/change_label/website');
		}else{
			$website_name = "Website";
		} 
		$description = Mage::getStoreConfig('inquiry/change_label/description'); 
		if($description){
			$description = Mage::getStoreConfig('inquiry/change_label/description');
		}else{
			$description = "Business Description";
		} 
	
		$adminContent = '<table border="0">
							<tr>
								<td>
									<table border="0">
										<tr>
											<Td>
												<label><p style="Font-size:22px;"><b>Hello Administrator,</b></p></label>
											</Td>
										</tr>
										<tr>
											<Td>
												<p>Mr/Ms. '.$fname.' '.$lname.' have filled dealer inquiry form and details are below.</p>
											</td>
										</tr>
										<tr>
											<td>
											<table border="0">
													<tr>
														<td><label>'.$first_name.':</label></td>
														<td><label>'.$fname.'</label></td>
													</tr>
													<tr>
														<td><label>'.$last_name.':</label></td>
														<td><label>'.$lname.'</label></td>
													</tr>
													<tr>
														<td><label>'.$company_name.':</label></td>
														<td><label>'.$company.'</label></td>
													</tr>
													<tr>
														<td><label>'.$productname_name.':</label></td>
														<td><label>'.$productname.'</label></td>
													</tr>
													<tr>
														<td><label>'.$sku_name.':</label></td>
														<td><label>'.$sku.'</label></td>
													</tr>
													<tr>
														<td><label>'.$vat_number.':</label></td>
														<td><label>'.$taxvat.'</label></td>
													</tr>
													<tr>
														<td><label>'.$address_name.':</label></td>
														<td><label>'.$address.'</label></td>
													</tr>
													<tr>
														<td><label>'.$city_name.':</label></td>
														<td><label>'.$city.'</label></td>
													</tr>
													<tr>
														<td><label>'.$state_name.':</label></td>
														<td><label>'.$state.'</label></td>
													</tr>
													<tr>
														<td><label>'.$country.':</label></td>
														<td><label>'.$country1[1].'</label></td>
													</tr>
													<tr>
														<td><label>'.$postal_code.':</label></td>
														<td><label>'.$zip.'</label></td>
													</tr>
													<tr>
														<td><label>'.$contact_number.':</label></td>
														<td><label>'.$phone.'</label></td>
													</tr>
													<tr>
														<td><label>'.$email_name.':</label></td>
														<td><label>'.$email.'</label></td>
													</tr>
													<tr>
														<td><label>'.$categories_name.':</label></td>
														<td><label>'.implode(', ', $categories).'</label></td>
													</tr>
													<tr>
														<td><label>'.$website_name.':</label></td>
														<td><label>'.$website.'</label></td>
													</tr>
													<tr>
														<td valign="top" width="15%"><label>'.$description.':</label></td>
														<td><label>'.$bdesc.'</label></td>
													</tr>
													<tr>
														<td colspan="2">&nbsp;</td></tr>
													<tr>
														<td colspan="2"><label>Thank You.</label></td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>';
		$adminSubject = "New Dealer Inquiry from dealer";
		$adminName = Mage::getStoreConfig('trans_email/ident_general/name'); //sender name
		$adminEmail = Mage::getStoreConfig('trans_email/ident_general/email');
		$headers  .= 'MIME-Version: 1.0'."\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'From:'. $adminName.' <'.$adminEmail.'>';
		mail($adminEmail,$adminSubject,$adminContent,$headers);
		
		$email_logo = Mage::getStoreConfig('design/email/logo');
		$subject_title = Mage::getStoreConfig('inquiry/customer_email/heading');
		$email_desc = Mage::getStoreConfig('inquiry/customer_email/description');
		$email_desc = str_replace("{{Name}}",$fname.$lname,$email_desc);
		$store_name = Mage::getStoreConfig('general/store_information/name');
	
		$img_media =  Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'email/logo/'; 
	
		$img_logo_final = $img_media.$email_logo;
		$default_logo =  Mage::getStoreConfig('design/header/logo_src');	
		$logo_default = Mage::getDesign()->getSkinUrl().$default_logo; 
		$email_desc = str_replace("{{Storename}}",$store_name,$email_desc);		
			
		if($img_logo_final == $img_media)
		{
			$logo_img = "<img src='$logo_default'/>"; 
		}
		else
		{
			$logo_img =   "<img src='$img_logo_final'/>";
		}
	
		$customerContent = '<table border="0">
								<tr>
									<td>
										<table border="0">
											<tr>
												<Td>'.$logo_img.'</Td>
											</tr>
											<tr>
												<td colspan="2">&nbsp;</td></tr>
											<tr>
												<Td><p>'.$email_desc.'. </p></Td>
											</tr>
											
										</table>
									</td>
								</tr>
							</table>';
		$headers = "";
		$adminName = Mage::getStoreConfig('trans_email/ident_general/name'); //sender name
		$custSubject = $subject_title;
		$headers  .= 'MIME-Version: 1.0'."\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'From:'. $adminName.' <'.$adminEmail.'>';
		
		mail($email,$custSubject,$customerContent,$headers);
			}
			else
			{
			$message = "email_wrong";  
			Mage::getSingleton('core/session')->setSomeSessionVar($message);			
			$this->_redirectReferer();
			
			}
    	}
        else
		{
		$message = "wrong";  
		Mage::getSingleton('core/session')->setSomeSessionVar($message);			
		$this->_redirectReferer();
		}
		}		
	}

    public function productthanksAction()
    {

		$this->loadLayout(array('default'));
		$this->renderLayout();
		if($_POST['SUBMIT']=='SUBMIT')
		{

		$captcha =  $this->getRequest()->getParam("captcha");
		$captcha_code =  $this->getRequest()->getParam("captcha_code");
		if($captcha == $captcha_code)
		{

		$fname =  $this->getRequest()->getParam("fname");
		$lname =  $this->getRequest()->getParam("lname");
		$company =  $this->getRequest()->getParam("company");
		$taxvat =  $this->getRequest()->getParam("account_taxvat");
		$address =  $this->getRequest()->getParam("address");
		$city =  $this->getRequest()->getParam("city");
		$state =  $this->getRequest()->getParam("state_id");
		$country =  $this->getRequest()->getParam("country");
		$zip =  $this->getRequest()->getParam("zip");
		$phone =  $this->getRequest()->getParam("phone");
		$email =  $this->getRequest()->getParam("email");
        $categories =  $this->getRequest()->getParam("categories");
        $productname =  $this->getRequest()->getParam("productname");
        $sku =  $this->getRequest()->getParam("sku");
		$storeid = Mage::app()->getStore()->getStoreId();
		$website =  $this->getRequest()->getParam("website");
		$bdesc =  addslashes($this->getRequest()->getParam("bdesc"));
		$headers = "";
		$country1 = explode('$$$',$country);

		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');
		$query = 'SELECT * FROM ' . $resource->getTableName('dealerinquiry')." where email='".$email."' and storeid='".$storeid."'";

        $results = $readConnection->fetchRow($query);

		  if($results == false || $results == true)
		  {

		$insertArr = array("firstname"=>$fname,"lastname"=>$lname,"company"=>$company,"productname"=>$productname,"sku"=>$sku,"address"=>$address,"taxvat"=>$taxvat,"city"=>$city,"state"=>$state,"country"=>$country,"zip"=>$zip,"phone"=>$phone,"email"=>$email,"categories"=>implode(', ', $categories),"storeid"=>$storeid,"website"=>$website,"desc"=>$bdesc,"iscustcreated"=>0,"status"=>1,"createddt"=>date('Y-m-d H:i:s'));

		$collection = Mage::getModel("inquiry/inquiry");

		$collection->setData($insertArr);

		$collection->save();

			$first_name = Mage::getStoreConfig('inquiry/change_label/f_name');
		if($first_name){
			$first_name = Mage::getStoreConfig('inquiry/change_label/f_name');
		}else {
			$first_name = "First Name";
		}
		$last_name = Mage::getStoreConfig('inquiry/change_label/l_name');
		if($last_name){
			$last_name = Mage::getStoreConfig('inquiry/change_label/l_name');
		}else {
			$last_name = "Last Name";
		}
		$company_name = Mage::getStoreConfig('inquiry/change_label/company_name');
		if($company_name){
			$company_name = Mage::getStoreConfig('inquiry/change_label/company_name');
		}else{
			$company_name = "Company Name";
		}

		$vat_number = Mage::getStoreConfig('inquiry/change_label/vat_number');
        if($vat_number){
			$vat_number = Mage::getStoreConfig('inquiry/change_label/vat_number');
		}else{
			$vat_number = "TAX/VAT Number";
		}

		$address_name = Mage::getStoreConfig('inquiry/change_label/address');
		if($address_name){
			$address_name = Mage::getStoreConfig('inquiry/change_label/address');
		}else{
			$address_name = "Address";
		}

		$city_name = Mage::getStoreConfig('inquiry/change_label/city');
		if($city_name){
			$city_name = Mage::getStoreConfig('inquiry/change_label/city');
		}else{
			$city_name = "City";
		}

		$state_name = Mage::getStoreConfig('inquiry/change_label/state');
		if($state_name){
			$state_name = Mage::getStoreConfig('inquiry/change_label/state');
		}else{
			$state_name = "State/Province";
		}

		$country = Mage::getStoreConfig('inquiry/change_label/country');
		if($country){
			$country = Mage::getStoreConfig('inquiry/change_label/country');
		}else{
			$country = "Country";
		}
		$postal_code = Mage::getStoreConfig('inquiry/change_label/postal_code');
		if($postal_code){
			$postal_code = Mage::getStoreConfig('inquiry/change_label/postal_code');
		}else{
			$postal_code = "ZIP/Postal Code";
		}

		$contact_number = Mage::getStoreConfig('inquiry/change_label/contact_number');
		if($contact_number){
			$contact_number = Mage::getStoreConfig('inquiry/change_label/contact_number');
		}else{
			$contact_number = "Contact Number";
		}

		$email_name = Mage::getStoreConfig('inquiry/change_label/email');
		if($email_name){
			$email_name = Mage::getStoreConfig('inquiry/change_label/email');
		}else{
			$email_name = "Email";
		}

        $categories_name =  "Interested In";
        $productname_name =  "Enquiry For Product";
        $sku_name =  "SKU";

		$website_name = Mage::getStoreConfig('inquiry/change_label/website');
		if($website_name){
			$website_name = Mage::getStoreConfig('inquiry/change_label/website');
		}else{
			$website_name = "Website";
		}
		$description = Mage::getStoreConfig('inquiry/change_label/description');
		if($description){
			$description = Mage::getStoreConfig('inquiry/change_label/description');
		}else{
			$description = "Business Description";
		}

		$adminContent = '<table border="0">
							<tr>
								<td>
									<table border="0">
										<tr>
											<Td>
												<label><p style="Font-size:22px;"><b>Hello Administrator,</b></p></label>
											</Td>
										</tr>
										<tr>
											<Td>
												<p>Mr/Ms. '.$fname.' '.$lname.' have filled dealer inquiry form and details are below.</p>
											</td>
										</tr>
										<tr>
											<td>
											<table border="0">
													<tr>
														<td><label>'.$first_name.':</label></td>
														<td><label>'.$fname.'</label></td>
													</tr>
													<tr>
														<td><label>'.$last_name.':</label></td>
														<td><label>'.$lname.'</label></td>
													</tr>
													<tr>
														<td><label>'.$company_name.':</label></td>
														<td><label>'.$company.'</label></td>
													</tr>
													<tr>
														<td><label>'.$productname_name.':</label></td>
														<td><label>'.$productname.'</label></td>
													</tr>
													<tr>
														<td><label>'.$sku_name.':</label></td>
														<td><label>'.$sku.'</label></td>
													</tr>
													<tr>
														<td><label>'.$vat_number.':</label></td>
														<td><label>'.$taxvat.'</label></td>
													</tr>
													<tr>
														<td><label>'.$address_name.':</label></td>
														<td><label>'.$address.'</label></td>
													</tr>
													<tr>
														<td><label>'.$city_name.':</label></td>
														<td><label>'.$city.'</label></td>
													</tr>
													<tr>
														<td><label>'.$state_name.':</label></td>
														<td><label>'.$state.'</label></td>
													</tr>
													<tr>
														<td><label>'.$country.':</label></td>
														<td><label>'.$country1[1].'</label></td>
													</tr>
													<tr>
														<td><label>'.$postal_code.':</label></td>
														<td><label>'.$zip.'</label></td>
													</tr>
													<tr>
														<td><label>'.$contact_number.':</label></td>
														<td><label>'.$phone.'</label></td>
													</tr>
													<tr>
														<td><label>'.$email_name.':</label></td>
														<td><label>'.$email.'</label></td>
													</tr>
													<tr>
														<td><label>'.$categories_name.':</label></td>
														<td><label>'.implode(', ', $categories).'</label></td>
													</tr>
													<tr>
														<td><label>'.$website_name.':</label></td>
														<td><label>'.$website.'</label></td>
													</tr>
													<tr>
														<td valign="top" width="15%"><label>'.$description.':</label></td>
														<td><label>'.$bdesc.'</label></td>
													</tr>
													<tr>
														<td colspan="2">&nbsp;</td></tr>
													<tr>
														<td colspan="2"><label>Thank You.</label></td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>';
		$adminSubject = "New Dealer Inquiry from dealer";
		$adminName = Mage::getStoreConfig('trans_email/ident_general/name'); //sender name
		$adminEmail = Mage::getStoreConfig('trans_email/ident_general/email');
		$headers  .= 'MIME-Version: 1.0'."\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'From:'. $adminName.' <'.$adminEmail.'>';
		mail($adminEmail,$adminSubject,$adminContent,$headers);

		$email_logo = Mage::getStoreConfig('design/email/logo');
		$subject_title = Mage::getStoreConfig('inquiry/customer_email/heading');
		$email_desc = Mage::getStoreConfig('inquiry/customer_email/description');
		$email_desc = str_replace("{{Name}}",$fname.$lname,$email_desc);
		$store_name = Mage::getStoreConfig('general/store_information/name');

		$img_media =  Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'email/logo/';

		$img_logo_final = $img_media.$email_logo;
		$default_logo =  Mage::getStoreConfig('design/header/logo_src');
		$logo_default = Mage::getDesign()->getSkinUrl().$default_logo;
		$email_desc = str_replace("{{Storename}}",$store_name,$email_desc);

		if($img_logo_final == $img_media)
		{
			$logo_img = "<img src='$logo_default'/>";
		}
		else
		{
			$logo_img =   "<img src='$img_logo_final'/>";
		}

		$customerContent = '<table border="0">
								<tr>
									<td>
										<table border="0">
											<tr>
												<Td>'.$logo_img.'</Td>
											</tr>
											<tr>
												<td colspan="2">&nbsp;</td></tr>
											<tr>
												<Td><p>'.$email_desc.'. </p></Td>
											</tr>

										</table>
									</td>
								</tr>
							</table>';
		$headers = "";
		$adminName = Mage::getStoreConfig('trans_email/ident_general/name'); //sender name
		$custSubject = $subject_title;
		$headers  .= 'MIME-Version: 1.0'."\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'From:'. $adminName.' <'.$adminEmail.'>';

		mail($email,$custSubject,$customerContent,$headers);
			}
			else
			{
			$message = "email_wrong";
			Mage::getSingleton('core/session')->setSomeSessionVar($message);
			$this->_redirectReferer();

			}
    	}
        else
		{
		$message = "wrong";
		Mage::getSingleton('core/session')->setSomeSessionVar($message);
		$this->_redirectReferer();
		}
		}
	}
}	
?>
