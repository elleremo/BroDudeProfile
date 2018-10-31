<?php


namespace BroDudeProfile\Classes;


use BroDudeProfile\Utils\Assets;

class Settings {
	use Assets;

	private $uid = false;
	private $user_data = [];

	public function __construct( $action ) {
		add_action( $action, [ $this, 'router' ], 10, 1 );
	}

	public function router( $uid ) {
		$this->uid = $uid;

		$data = get_userdata( $this->uid );

		$this->user_data = [
			'user_email'   => $data->data->user_email,
			'user_login'   => $data->data->user_login,
			'display_name' => $data->data->display_name,
			'first_name'   => $data->get( 'first_name' ),
			'last_name'    => $data->get( 'last_name' ),
		];

		$this->content();

	}

	public function add_js_css() {
        if(isset($_GET['edit'])){
            set_query_var('tab_active','edit');
        }

		if ( 'edit' === get_query_var( 'tab_active', false ) ) {
			$this->addCss( "ProfileSettings" );
		}
	}

	public function content() {

		?>
        <form action="<?php echo SettingsSave::$ajax_url; ?>" enctype="multipart/form-data" method="post" class="profile__settings">

            <div class="profile__settings-row message">

            </div>

			<?php
			$this->row(
				'Аватар:',
				$this->ellements( 'file', 'avatar', [ 'value' => "" ] )
			);

			?>
			<?php
			$this->row(
				'Логин для входа (нельзя менять):',
				$this->ellements( 'text', 'user_login', [ 'value' => $this->user_data['user_login'] ], true )
			);
			?>

			<?php
			$this->row(
				'Имя:',
				$this->ellements( 'text', 'first_name', [ 'value' => $this->user_data['first_name'] ] )
			);
			?>

			<?php
			$this->row(
				'Фамилия:',
				$this->ellements( 'text', 'last_name', [ 'value' => $this->user_data['last_name'] ] )
			);
			?>

			<?php
			$this->row(
				'Отображать имя как:',
				$this->ellements(
					'select',
					'display_name',
					[
						'value'   => $this->user_data['display_name'],
						'options' => [
							$this->user_data['user_login']                                       => $this->user_data['user_login'],
							$this->user_data['first_name']                                       => $this->user_data['first_name'],
							$this->user_data['last_name']                                        => $this->user_data['last_name'],
							$this->user_data['first_name'] . " " . $this->user_data['last_name'] => $this->user_data['first_name'] . " " . $this->user_data['last_name'],
							$this->user_data['last_name'] . " " . $this->user_data['first_name'] => $this->user_data['last_name'] . " " . $this->user_data['first_name'],
						]
					]
				)
			);
			?>

			<?php
			$this->row(
				'Email:',
				$this->ellements( 'email', 'user_email', [ 'value' => $this->user_data['user_email'] ] )
			);
			?>

			<?php
			$this->row(
				'Пароль:',
				$this->ellements( 'password', 'user_pass', [ 'value' => '' ] )
			);
			?>

			<?php
			$this->row(
				'Включить прогресс чтения:',
				$this->ellements(
					'checkbox', 'reed_progress',
					[ 'value' => get_user_meta( get_current_user_id(), 'reed_progress', true ) ]
				)
			);
			?>

			<?php
			$this->row(
				'Включить классический фон:',
				$this->ellements(
					'checkbox', 'wooden_bg',
					[ 'value' => get_user_meta( get_current_user_id(), 'wooden_bg', true ) ]
				)
			);
			?>
            <div class="profile__settings-row">
                <button type='submit' class="profile__submit">Сохранить</button>
            </div>
			<?php wp_nonce_field( SettingsSave::$action_name ); ?>

        </form>
		<?php
	}


	public function row( $text, $control ) {
		?>
        <div class="profile__settings-row">
            <div class="text">
				<?php echo $text; ?>
            </div>

            <div class="control">
				<?php echo $control; ?>
            </div>

        </div>
		<?php
	}

	public function ellements( $element, $name = '', $args = [], $disabled = false ) {
		$args = shortcode_atts(
			[
				'value'   => '',
				'options' => [],
				'accept'  => 'image/jpeg,image/png'
			]
			, $args
		);
		if ( $disabled ) {
			$disabled = "disabled='disabled'";
		} else {
			$disabled = "";
		}

		if ( 'text' === $element ) {
			return "<input class='input__text' name='{$name}' type='text' value='{$args['value']}' {$disabled} />";
		} else if ( 'password' === $element ) {
			return "<input name='{$name}' type='password' value='{$args['value']}' {$disabled} />";
		} elseif ( 'checkbox' === $element ) {
			$checked = checked( $args['value'], true, false );

			return "<input class='input__checkbox' name='{$name}' value='true' {$checked} {$disabled} type='checkbox' />";
		} elseif ( 'email' === $element ) {
			return "<input class='input__text' name='{$name}' type='email' value='{$args['value']}' {$disabled} />";
		} elseif ( 'file' === $element ) {
			return "<input class='input__text' name='{$name}' type='file' value=''  accept='{$args['accept']}'  {$disabled} />";
		} elseif ( 'select' === $element ) {
			$res = '';

			$res .= "<select class='selectbox' name='{$name}' {$disabled}>";
			foreach ( $args['options'] as $key => $val ) {
				$selected = selected( $key, $val, false );
				$res      .= "<option value='{$key}' {$selected} >{$val}</option>";
			}
			$res .= "</select>";

			return $res;
		}

	}


}