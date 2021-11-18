<?php 
/**
 * File: about.php
 *
 * @package WPGlobus Translate Options
 * @subpackage Templates.
 * @since 2.0.0
 */

$themes = $this->args['themes'];

?>
<div><?php
	esc_html_e( 'In the WPGlobus core plugin, we are keeping the amount of filters as low as possible, to minimize the potential performance hit.', 'wpglobus-translate-options' );
	echo '&nbsp;';
	echo sprintf( 
		esc_html__( 'Therefore, only two WordPress options, %1$sblogdescription%2$s and %1$sblogname%2$s are supported by default.', 'wpglobus-translate-options' ),
		'<strong>',
		'</strong>'
	);
?>
</div>
<div>
	<div><?php
		esc_html_e( 'Sometimes, it is necessary to allow multiple languages in other options.', 'wpglobus-translate-options' ); ?>
	</div>
	<div><?php
		esc_html_e( 'For instance', 'wpglobus-translate-options' ); ?>:
	</div>
	<ul>
		<li><?php
			echo '- ' . esc_html__( 'в опциях темы требуется многоязычный текст об авторских правах для вывода в подвале сайта', 'wpglobus-translate-options' ); ?>
		</li>
		<li><?php
			echo '- ' . esc_html__( 'нужны многоязычные подписи для картинок, которые используются в слайдере', 'wpglobus-translate-options' ); ?>
		</li>		
	</ul><?php	
	
	// For instance, the slider used in the Ample theme stores the textual overlays in the options table. 
	// With the WPGlobus Translate Options plugin, all you need is to add 'ample' into the Options to translate, and all the slider texts will be multilingual!
	
	$active_theme = '';
	$theme_option = '';
	if ( $themes['child'] ) {
		$active_theme = $themes['child']['name'];
		$theme_option = $themes['child']['themeOption'];
	} else {
		$active_theme = $themes['parent']['name'];
		$theme_option = $themes['parent']['themeOption'];
	} error_log( print_r( $themes, true ) );?>
	<div><?php
		echo esc_html__( 'Текущая активная тема', 'wpglobus-translate-options' ) . '&nbsp;<strong>' . $active_theme . '</strong>&nbsp;';
		echo esc_html__( 'использует опцию', 'wpglobus-translate-options' ) . '&nbsp;<strong>' . $theme_option . '</strong>'; ?>
	</div><?php
	if ( $themes['child'] ) {	?>
		<div><?php
			echo esc_html__( 'Parent theme', 'wpglobus-translate-options' ) . '&nbsp;<strong>' . $themes['parent']['name'] . '</strong>&nbsp;';
			echo esc_html__( 'использует опцию', 'wpglobus-translate-options' ) . '&nbsp;<strong>' . $themes['parent']['themeOption'] . '</strong>'; ?>			
		</div><?php
	}	?>
	<div><?php
		$_piece = esc_html__( 'опции темы', 'wpglobus-translate-options' );
		if ( $themes['child'] ) {
			$_piece = esc_html__( 'опций тем', 'wpglobus-translate-options' );
		}
		echo sprintf( 
			esc_html__( 'Мы рекомендуем активировать использование %1$s для перевода (см. таблицу)', 'wpglobus-translate-options' ),
			$_piece
		) ?>
	</div><?php
	
?>
</div>
