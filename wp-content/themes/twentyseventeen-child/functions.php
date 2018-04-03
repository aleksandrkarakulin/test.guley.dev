<?php
/**
 * Child-Theme functions and definitions
 */

// подключаем CSS родительской темы
add_action( 'wp_enqueue_scripts', 'wpak_parent_theme_css' );
function wpak_parent_theme_css(){
	wp_enqueue_style( 'parent-theme', get_template_directory_uri() . '/style.css' );
}


// создаём пользовательский тип записи
add_action('init', 'wpak_register_custom_post_type_film');
function wpak_register_custom_post_type_film(){
	register_post_type('film', array(
		'labels' => array(
			'name'								=> 'Фильмы', //__('Film', 'twentyseventeen');
			'singular_name'				=> 'Фильм',
			'add_new'							=> 'Добавить Фильм',
			'add_new_item'				=> 'Добавление Фильма',
			'edit_item'						=> 'Редактирование Фильма',
			'new_item'						=> 'Новый Фильм',
			'view_item'						=> 'Смотреть Фильм',
			'search_items'				=> 'Искать Фильм',
			'not_found'						=> 'Не найдено',
			'not_found_in_trash'	=> 'Не найдено в корзине',
			'parent_item_colon'		=> '',
			'menu_name'						=> 'Фильмы',
		),
		'description'		=> '',
		'menu_icon'			=> 'dashicons-format-video',
		'public'				=> true,
		'hierarchical'	=> false,
		'supports'			=> array('title', 'excerpt', 'editor', 'thumbnail'),
		'taxonomies'		=> array('film_category'),
		'has_archive'		=> 'films'
	) );
}


// создаём таксономию (в ТЗ не совсем чётко сформулировано использовать стандартную таксономию category ('Рубрики' в русской версии WP) или создавать свою)
add_action('init', 'wpak_register_custom_taxonomy_film_category');
function wpak_register_custom_taxonomy_film_category(){
	register_taxonomy('film_category', array('film'), array(
		'labels'                => array(
			'name'							=> 'Категории',
			'singular_name'			=> 'Категория',
			'search_items'			=> 'Искать Категории',
			'all_items'					=> 'Все Категории',
			'view_item '				=> 'Показать Категорию',
			'parent_item'				=> 'Родительская Категория',
			'parent_item_colon'	=> 'Родительская:',
			'edit_item'					=> 'Редактировать Категорию',
			'update_item'				=> 'Обновить Категорию',
			'add_new_item'			=> 'Добавить новую Категорию',
			'new_item_name'			=> 'Заголовок новой Категории',
			'menu_name'					=> 'Категории',
		),
		'description'           => '',
		'public'                => true,
		'hierarchical'          => true,
		'update_count_callback' => '',
		'rewrite'               => true,
		'meta_box_cb'           => 'post_categories_meta_box',
		'show_admin_column'     => true
	) );
}


// добовляем пользовательское поле Цена для WC
//// добавляем metabox
add_action('add_meta_boxes', 'wpak_add_meta_box_for_films', 1);
function wpak_add_meta_box_for_films() {
	add_meta_box( 'price_meta_box', 'Цена', 'wpak_films_price_extra_fields_func', 'film', 'normal', 'high' );
}
//// HTML для metabox
function wpak_films_price_extra_fields_func( $post ){
	echo '
	<p><label for="_price">Цена <input id="_price" type="text" name="_price" value="' . get_post_meta($post->ID, '_price', 1) . '" style="width:50%" /> $</label></p>
	<input type="hidden" name="extra_fields_nonce" value="' . wp_create_nonce(__FILE__) . '" />';
}
//// сохраняем значение поля Цена
add_action('save_post', 'wpak_price_extra_fields_update', 0);
function wpak_price_extra_fields_update( $post_id ) {
	if( ! wp_verify_nonce($_POST['extra_fields_nonce'], __FILE__) ) return false;
	if( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE  ) return false;
	if( !current_user_can('edit_post', $post_id) ) return false;
	if( !isset($_POST['_price']) ) return false;

	if( !empty($_POST['_price']) ){
		$price = round( $_POST['_price'], 2 );
		update_post_meta($post_id, '_price', $price);
	} else {
		delete_post_meta($post_id, '_price');
	}

	return $post_id;
}


// добавляем кнопку Купить для корзины WC
add_filter('the_content','rei_add_to_cart_button', 20,1);
function rei_add_to_cart_button($content){
	global $post;
	if( $post->post_type !== 'film' ) { return $content; }
	
	$cart_button_html = '<form action="" method="post">
		<input name="add-to-cart" type="hidden" value="' . $post->ID . '" />
		<input name="quantity" type="hidden" value="1" />
		<input name="submit" type="submit" value="' . __('Add to cart', 'woocommerce') . '" />
	</form>';
	
	return $content . $cart_button_html;
}

// расширение для класса WC для возможности добавления пользовательского типа записи в корзину
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	class WCCPT_Product_Data_Store_CPT extends WC_Product_Data_Store_CPT {

		/**
		 * Method to read a product from the database.
		 * @param WC_Product
		 */

		public function read( &$product ) {

			$product->set_defaults();

			if ( ! $product->get_id() || ! ( $post_object = get_post( $product->get_id() ) ) || ! in_array( $post_object->post_type, array( 'film', 'product' ) ) ) { // change birds with your post type
				throw new Exception( __( 'Invalid product.', 'woocommerce' ) );
			}

			$id = $product->get_id();

			$product->set_props( array(
				'name'							=> $post_object->post_title,
				'slug'							=> $post_object->post_name,
				'date_created'			=> 0 < $post_object->post_date_gmt ? wc_string_to_timestamp( $post_object->post_date_gmt ) : null,
				'date_modified'			=> 0 < $post_object->post_modified_gmt ? wc_string_to_timestamp( $post_object->post_modified_gmt ) : null,
				'status'						=> $post_object->post_status,
				'description'				=> $post_object->post_content,
				'short_description'	=> $post_object->post_excerpt,
				'parent_id'					=> $post_object->post_parent,
				'menu_order'				=> $post_object->menu_order,
				'reviews_allowed'		=> 'open' === $post_object->comment_status,
			) );

			$this->read_attributes( $product );
			$this->read_downloads( $product );
			$this->read_visibility( $product );
			$this->read_product_data( $product );
			$this->read_extra_data( $product );
			$product->set_object_read( true );
		}

		/**
		 * Get the product type based on product ID.
		 *
		 * @since 3.0.0
		 * @param int $product_id
		 * @return bool|string
		 */
		public function get_product_type( $product_id ) {
			$post_type = get_post_type( $product_id );
			if ( 'product_variation' === $post_type ) {
				return 'variation';
			} elseif ( in_array( $post_type, array( 'film', 'product' ) ) ) { // change birds with your post type
				$terms = get_the_terms( $product_id, 'product_type' );
				return ! empty( $terms ) ? sanitize_title( current( $terms )->name ) : 'simple';
			} else {
				return false;
			}
		}
	}
}

add_filter( 'woocommerce_data_stores', 'wpak_woocommerce_data_stores' );
function wpak_woocommerce_data_stores ( $stores ) {      
	$stores['product'] = 'WCCPT_Product_Data_Store_CPT';
	return $stores;
}


// направляем пользователя на страницу оплаты
add_filter('woocommerce_add_to_cart_redirect', 'wpak_add_to_cart_redirect');
function wpak_add_to_cart_redirect() {
	global $woocommerce;
	$checkout_url = wc_get_checkout_url();
	return $checkout_url;
}


// добавляем поле Skype в форме регистрации пользователей
add_action( 'woocommerce_register_form', 'wpak_extra_register_fields' );
function wpak_extra_register_fields() {
	echo '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
			<label for="skype">Skype </label>
			<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="skype" id="skype" value="' . esc_attr_e( $_POST['skype'] ) . '" />
		</p>';
 }
//// сохраняем кастомное поле при регистрации юзера
 add_action( 'woocommerce_created_customer', 'wpak_save_extra_register_fields' );
function wpak_save_extra_register_fields( $customer_id ) {
	if ( isset( $_POST['skype'] ) ) {
		update_user_meta( $customer_id, 'skype', sanitize_text_field( $_POST['skype'] ) );
	}
}
//// добавляем кастомное поле на страницу редактирования профиля
add_action( 'woocommerce_edit_account_form', 'wpak_woocommerce_edit_account_form' );
function wpak_woocommerce_edit_account_form() {
	$user_id = get_current_user_id();
	$user = get_userdata( $user_id );
	if ( !$user )
		return;
	if( isset($_POST[ 'skype' ]) ) {
		$skype = htmlentities( $_POST[ 'skype' ] );
	} else {
		$skype = get_user_meta( $user_id, 'skype', true );
	}
	echo '
		<p class="form-row form-row-thirds">
			<label for="skype">Skype: </label>
			<input id="skype" type="text" name="skype" value="' . esc_attr( $skype ) . '" class="input-text" />
		</p>';
}
//// сохраняем кастомное поле на странице редактирования профиля
add_action( 'woocommerce_save_account_details', 'wpak_woocommerce_save_account_details' );
function wpak_woocommerce_save_account_details( $user_id ) {
	update_user_meta( $user_id, 'skype', htmlentities( $_POST[ 'skype' ] ) );
}


// добовляем пользовательское поле Избранное
//// добавляем metabox
add_action('add_meta_boxes', 'wpak_add_meta_box_for_favorites', 1);
function wpak_add_meta_box_for_favorites() {
	add_meta_box( 'favorites_film_meta_box', 'Избранное', 'wpak_films_favorites_extra_fields_func', 'film', 'side', 'default' );
}
//// HTML для metabox
function wpak_films_favorites_extra_fields_func( $post ){
	echo '
	<p><label for="favorites_film">
	<input type="checkbox" name="favorites_film" id="favorites_film" ' , get_post_meta($post->ID, 'favorites_film', true) ? ' checked="checked"' : '' , ' /> отметить избранным</label></p>
	<input type="hidden" name="extra_fields_nonce" value="' . wp_create_nonce(__FILE__) . '" />';
}
//// сохраняем значение поля Избранное (разделил функции сохранения поля Избранное и поля Цена для лучшей читаемости кода)
add_action('save_post', 'wpak_favorites_film_extra_fields_update', 0);
function wpak_favorites_film_extra_fields_update( $post_id ) {
	if( ! wp_verify_nonce($_POST['extra_fields_nonce'], __FILE__) ) return false;
	if( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE  ) return false;
	if( !current_user_can('edit_post', $post_id) ) return false;
	if( !isset($_POST['_price']) ) return false;

	if( !empty($_POST['favorites_film']) ){
		update_post_meta($post_id, 'favorites_film', $_POST['favorites_film']);
	} else {
		delete_post_meta($post_id, 'favorites_film');
	}

	return $post_id;
}

// фильтр по пользовательскому полю Избранное
add_action('pre_get_posts', 'wpak_favorites_alter_query');
function wpak_favorites_alter_query($query) {
	global $wp_query;
	if( get_query_var('post_type') == 'film' && $_GET['film_type'] == 'favorites') {
		$query-> set('meta_key', 'favorites_film');
	}
}

// редирект после регистрации в WC
add_action('woocommerce_registration_redirect', 'wpak_custom_registration_redirect', 2);
function wpak_custom_registration_redirect() {
	return '/films/?film_type=favorites';
}


?>