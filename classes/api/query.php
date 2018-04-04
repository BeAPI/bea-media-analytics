<?php namespace BEA\Media_Analytics\API;

use BEA\Media_Analytics\DB_Table;
use BEA\Media_Analytics\Singleton;

class Query {
	use Singleton;

	protected function init() {
		add_action( 'query_vars', [ $this, 'query_vars' ], 10 );
		add_filter( 'posts_join', [ $this, 'posts_join' ], 10, 2 );
		add_filter( 'posts_where', [ $this, 'posts_where' ], 10, 2 );
		add_filter( 'posts_groupby', [ $this, 'posts_groupby' ], 10, 2 );
	}

	/**
	 * Append a custom query var for this new filter on WP_Query
	 *
	 * @param array $vars
	 *
	 * @author Amaury Balmer
	 * @since  2.1.0
	 *
	 * @return array
	 */
	public static function query_vars( $vars ) {
		$vars[] = "bea_media_analytics";

		return $vars;
	}

	/**
	 * Add join with plugin table for filter unused or used medias
	 *
	 * @param           $join
	 * @param \WP_Query $query
	 *
	 * @author Amaury Balmer
	 * @since  2.1.0
	 *
	 * @return string
	 */
	public static function posts_join( $join, \WP_Query $query ) {
		global $wpdb;

		if ( empty( $query->get( 'bea_media_analytics' ) ) ) {
			return $join;
		}

		$join_type = $query->get( 'bea_media_analytics' ) == 'unused' ? 'LEFT' : 'INNER';

		$join .= " $join_type JOIN " . DB_Table::get_instance()->get_table_name() . " AS bma ON ( $wpdb->posts.ID = bma.media_id AND bma.blog_id = " . get_current_blog_id() . " ) ";

		return $join;
	}

	/**
	 * Add join with plugin table for unused medias
	 *
	 * @param          $where
	 * @param  |WP_Query $query
	 *
	 * @author Amaury Balmer
	 * @since  2.1.0
	 *
	 * @return string
	 */
	public static function posts_where( $where, \WP_Query $query ) {
		if ( empty( $query->get( 'bea_media_analytics' ) ) || $query->get( 'bea_media_analytics' ) != 'unused' ) {
			return $where;
		}

		$where .= " AND bma.media_id IS NULL ";

		return $where;
	}

	/**
	 * Add join with plugin table for unused medias
	 *
	 * @param          $groupby
	 * @param  |WP_Query $query
	 *
	 * @author Amaury Balmer
	 * @since  2.1.0
	 *
	 * @return string
	 */
	public static function posts_groupby( $groupby, \WP_Query $query ) {
		global $wpdb;

		if ( empty( $query->get( 'bea_media_analytics' ) ) || $query->get( 'bea_media_analytics' ) != 'used' ) {
			return $groupby;
		}

		$groupby = " $wpdb->posts.ID ";

		return $groupby;
	}
}