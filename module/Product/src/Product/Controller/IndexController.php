<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Product\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
	public function indexAction()
	{
		$plugin = $this->BasicPlugin();
		$users =  $plugin->getUserList();
		$json_data = file_get_contents('public/products.json');		
		$products = json_decode($json_data, true); 
		//$user_json = file_get_contents('public/users.json');		
		//$users = json_decode($user_json, true);
		return new ViewModel(array('products'=>$products,'users'=>$users));
	}
	

	public function addAction() {
		$formData=array();
		$plugin = $this->BasicPlugin();
		$users =  $plugin->getUserList();
	
		$id = $this->params()->fromRoute('id');
		if($this->getRequest()->isPost()){
			$records = $this->getRequest()->getPost()->toArray();
			$record_id = $records['id'];
			unset($records['id']);
			$file = file_get_contents('public/products.json');
			$data = json_decode($file,true);
			unset($file);//prevent memory leaks for large json.
			if($record_id!=""){
				$data[$record_id] = $records;
			}
			else{
				$data[] = $records;
			}
			
			//save the file
			file_put_contents('public/products.json',json_encode($data));
			unset($data);//release memory
			return $this->redirect()->toRoute('products');
		}
		else{
			if($id!=""){
				$json_data = file_get_contents('public/products.json');		
				$products = json_decode($json_data, true);
				unset($json_data);			
				$formData = $products[$id];
				$formData['id'] = $id;
				unset($products);//release memory
			}
			
		}
		return new ViewModel(array('formData'=>$formData,'users'=>$users));         
	}
	 
	public function deleteAction() {
		$id = $this->params()->fromRoute('id');
		if(isset($id) && is_numeric($id)){
			$json_data = file_get_contents('public/products.json');		
			$products = json_decode($json_data, true);
			unset($json_data);			
			unset($products[$id]);
			file_put_contents('public/products.json',json_encode($products));
			unset($products);//release memory
			
		}
		return $this->redirect()->toRoute('products');
		//return new ViewModel();         
	}
}
