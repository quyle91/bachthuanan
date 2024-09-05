<?php
namespace Adminz\Helper;

class OptionField {
	public $id;
	public $args = [ 
		// adminz
		'object'      => '',
		'name'        => '',
		
		// 
		'field' => 'input',
		'attribute'   => [
			'name'        => '',
			'id'          => '',
			'class'     => [],
			// 'type' => 'text', 
			// 'placeholder' => '...',
			// 'value'       => '',
			// 'required'    => '',
			// 'is_checked'  => false,
		],
		'value' => '',
		'copy'        => '',
		'before'      => '<p>',
		'after'       => '</p>',
		'note'        => '',
		'label'       => '',
		'options'     => [ 
			// 1 => 1,
			// 2 => 2,
			// 3 => 3,
		],
		'term_select' => [ 
			// 'taxonomy'       => 'age-categories',
			// 'option_value'   => 'name',
			// 'option_display' => 'name',
		],
		'post_select' => [ 
			// 'post_type'      => 'club',
			// 'option_value'   => 'ID',
			// 'option_display' => 'post_title',
		],
		'selected' => '',
	];

	function __construct($args) {
		$keep_args = wp_parse_args( $args['attribute']?? [], $this->args['attribute'] ); // xxx
		$this->args = wp_parse_args( $args, $this->args );
		$this->args['attribute'] = $keep_args; // xxx
		$this->init_options();
	}

	function init_options() {
		// id
		$this->id = $this->args['attribute']['id']?? "" ? $this->args['attribute']['id'] : "adminz_field_".wp_rand();
		$this->args['attribute']['id'] = $this->id;

		// options
		$options = [];
		if ( !empty( $this->args['options'] ) ) {
			$options = [];
			$options = $this->args['options'];
		}

		if ( !empty( $this->args['term_select'] ) ) {
			$options = [];
			$terms   = get_terms( [ 
				'taxonomy'   => $this->args['term_select']['taxonomy'],
				'hide_empty' => 'false',
			] );
			foreach ( $terms as $key => $term ) {
				$_key             = $term->{$this->args['term_select']['option_value']};
				$_value           = $term->{$this->args['term_select']['option_display']};
				$options[ $_key ] = $_value;
			}
		}

		if ( !empty( $this->args['post_select'] ) ) {
			$options = [];
			$args    = [ 
				'post_type'      => [ $this->args['post_select']['post_type'] ],
				'post_status'    => [ 'publish' ],
				'posts_per_page' => -1,
				'orderby'        => 'name',
				'order'          => 'asc',
			];

			$__the_query = new \WP_Query( $args );
			if ( $__the_query->have_posts() ) {
				while ( $__the_query->have_posts() ) :
					$__the_query->the_post();
					global $post;
					$_key             = $post->{$this->args['post_select']['option_value']};
					$_value           = $post->{$this->args['post_select']['option_display']};
					$options[ $_key ] = $_value;
				endwhile;
				wp_reset_postdata();
			}
		}

		$this->args['options'] = $options;
	}

    function init(){
		ob_start();

        // call a method
		echo wp_kses_post( $this->args['before'] );
        $field = $this->args['field'];
		echo ( $this->args['label'] ?? "" ) ? "<label>" : "";
		if ( method_exists( $this, $field ) ) {
			echo $this->{$field}();
		}else{
			echo "method is not exists";
		}
		echo ( $this->args['label'] ?? "" ) ? "<small>{$this->args['label']}</small></label>" : "";
		echo $this->get_note();
		echo $this->get_copy();
		echo wp_kses_post($this->args['after']);
		return ob_get_clean();
    }

    function get_attribute(){
		// start 
		ob_start();
		$attribute = $this->args['attribute'];
		if ( !isset( $attribute['class'] ) or empty( $attribute['class'] ) ) {
			$attribute['class'] = [ 'adminz_field', 'regular-text' ];
		}

		foreach ( $attribute as $key => $value ) {
			$value = (array) $value;
			echo esc_attr( $key ) . '="' . esc_attr(implode( " ", $value )) . '" ';
		}

        return ob_get_clean();
    }

	function select(){
		ob_start();
		?>
		<select <?= $this->get_attribute(); ?>>
			<?php 
				foreach ($this->args['options'] as $key => $value) {
					$selected = in_array($key, (array)$this->args['selected']) ? 'selected' : "";
					?>
					<option 
						<?= esc_attr($selected) ?>
						value="<?= esc_attr($key); ?>"
						>
						<?= esc_attr($value); ?>
					</option>
					<?php
				}
			?>
		</select>
		<?php
		return ob_get_clean();
	}

	function textarea(){
		ob_start();
		?>
		<textarea <?= $this->get_attribute(); ?>><?= esc_attr( $this->args['value'] ) ?></textarea>
		<?php
		return ob_get_clean();
	}

    function input(){
        $type = $this->args['attribute']['type'] ?? "text";
		if ( method_exists( $this, "input_" . $type ) ) {
			return $this->{"input_" . $type}();
		}
		return "method is not exists";
    }

	function input_text(){
        ob_start();
        ?>
        <input <?php echo $this->get_attribute(); ?>>
        <?php
        return ob_get_clean();
    }

	function input_submit(){
		return $this->input_text();
	}

    function input_number(){
		return $this->input_text();
    }

    function input_date(){
		return $this->input_text();
    }

	function input_time() {
		return $this->input_text();
	}

	function input_button() {
		return $this->input_text();
	}

	function input_password() {
		return $this->input_text();
	}

	function input_file() {
		return $this->input_text();
	}

	function input_checkbox() {
		// set checked 
		if(isset($this->args['attribute']['checked']) and !$this->args['attribute']['checked']){
			unset($this->args['attribute']['checked']);
		}
		if ( !isset( $this->args['attribute']['class'] ) or empty( $this->args['attribute']['class'] ) ) {
			$this->args['attribute']['class'] = [ 'adminz_field'];
		}
		?>
		<input <?= $this->get_attribute(); ?>>
		<?php
	}

	function get_copy(){
		if ( !$this->args['copy'] ) {
			return;
		}
		$this->args['copy'] = (array) $this->args['copy'];
		ob_start();
		foreach ($this->args['copy'] as $key => $copy) {
			?>
			<small class="adminz_click_to_copy" data-text="<?= esc_attr( $copy ); ?>">
				<?= esc_attr( $copy ); ?>
			</small>
		<?php
		}
		return ob_get_clean();
	}

	function get_note(){
		if(!$this->args['note']){
			return;
		}
		$this->args['note'] = (array)$this->args['note'];
		ob_start();
		foreach ($this->args['note'] as $key => $note) {
			?>
				<small>
					<strong>*Note <?=($key) ? $key : ""; ?>:</strong> 
					<?= wp_kses_post($note) ?>.
				</small>
			<?php
		}
		return ob_get_clean();
	}
}