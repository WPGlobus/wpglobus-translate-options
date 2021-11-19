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
			echo '- ' . esc_html__( 'the theme stores a copyright notice in the theme options; it will be displayed in the footer, and needs to be translatable', 'wpglobus-translate-options' ); ?>
		</li>
		<li><?php
			echo '- ' . esc_html__( 'need to translate texts displayed in picture sliders', 'wpglobus-translate-options' ); ?>
		</li>
	</ul><?php	
	
	// For instance, the slider used in the Ample theme stores the textual overlays in the options table. 
	// With the WPGlobus Translate Options plugin, all you need is to add 'ample' into the Options to translate, and all the slider texts will be multilingual!
	
	$active_theme = '';
	$theme_option = '';
	if ( $themes['child'] ) {
		$active_theme = $themes['child']['name'];
		$theme_option = $themes['child']['themeModsOption'];
	} else {
		$active_theme = $themes['parent']['name'];
		$theme_option = $themes['parent']['themeModsOption'];
	} ?>
	<div><?php
		echo esc_html__( 'The active theme', 'wpglobus-translate-options' ) . '&nbsp;<strong>' . $active_theme . '</strong>&nbsp;';
		echo esc_html__( 'uses an option', 'wpglobus-translate-options' ) . '&nbsp;<strong>' . $theme_option . '</strong>'; ?>
	</div><?php
	if ( $themes['child'] ) {	?>
		<div><?php
			echo esc_html__( 'Parent theme', 'wpglobus-translate-options' ) . '&nbsp;<strong>' . $themes['parent']['name'] . '</strong>&nbsp;';
			echo esc_html__( 'uses an option', 'wpglobus-translate-options' ) . '&nbsp;<strong>' . $themes['parent']['themeModsOption'] . '</strong>'; ?>
		</div><?php
	}	?>
	<div><?php
		$_piece = esc_html__( 'theme options', 'wpglobus-translate-options' );
		if ( $themes['child'] ) {
			$_piece = esc_html__( 'theme options', 'wpglobus-translate-options' );
		}
		echo sprintf( 
			esc_html__( 'We recommend using %1$s for translation (see the table)', 'wpglobus-translate-options' ),
			$_piece
		) ?>
	</div><?php
	
?>
</div>
