<?php 
Class Products extends CI_Controller{
	function __construct(){
		parent::__construct();
		$this->load->model('user');
		$this->load->model('product');
	}
	function index(){
		$this->load->view('user/welcome');
	}
	function to_home(){
		$data['search'] = $this->session->userdata('current_search');
		$this->load->view('user/all_products', $data);
	}
	function new_search($search = null){
		if($search){
			$sp_search = str_replace('%20', " ", $search);
			$data['search'] = $sp_search;
		} else{
			$data['search'] = $this->input->post('search');
		}
		$this->session->set_userdata('current_search', "");
		$this->load->view('user/all_products', $data);
	}
	function set_search(){
		$current_search = $this->input->post('search');
		$this->session->set_userdata('current_search', $current_search);
		return $this->session->userdata('current_search');
	}
	function show_product($id){
		$data['album_id'] = $id;
		$this->load->view('user/show_product', $data);
	}
	function add_to_cart(){
		$product_exists = false;
		$productArray = [
			'id' => $this->input->post('id'),
			'name' => $this->input->post('name'),
			'artist' => $this->input->post('artist'),
			'price' => $this->input->post('price'),
			'qty' => $this->input->post('qty')
			];


		$cartArray = $this->session->userdata('cart');
		foreach ($cartArray as &$cartProduct){
			if($productArray['id'] == $cartProduct['id']){
				$cartProduct['qty'] += $productArray['qty'];
				$product_exists = true;
				break;
			}
		}
		if(!$product_exists){
			array_push($cartArray, $productArray);
		}
		$this->session->set_userdata('cart', $cartArray);
		$cartCount = $this->session->userdata('cart_count');
		$this->session->set_userdata('cart_count', ($cartCount + $productArray['qty']));
		$this->load->view('/partials/header');
	}
	function to_cart(){
		$data['cart'] = $this->session->userdata('cart');
		$this->load->view('user/cart', $data);
	}

	function admin_dashboard(){
		$this->session->set_userdata('page_number', 0);
		if ($this->session->userdata('admin_level') == 9) {
			$this->load->view('/admin/dashboard_products');
		}
		elseif ($this->session->userdata('admin_level') == 0) {
			redirect('/products/to_home');
		}
		else {
			redirect('/users');
		}	
	}

	function get_all_products($page, $search=null){
		

		if ($search == "") {
			$data['search'] = null;
		}
		else {
			$sp_search = str_replace('%20', " ", $search);
			$data['search'] = $sp_search;
		}

		//compare $page to page number in session
		if ($page == 0) {
			$this->session->set_userdata('page_number', 0);
		}
		else {
			$new_page_number = $page / 5;
			$this->session->set_userdata('page_number', $new_page_number);
		}
		$data['page_number'] = $page;
		$products = $this->product->get_all_products($data);
		$data['products'] = $products;
		$this->load->view('/admin_partials/products_table', $data);
	}
}
?>