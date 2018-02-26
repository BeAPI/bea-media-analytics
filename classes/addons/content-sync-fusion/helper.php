<?php namespace BEA\Media_Analytics\Addons\Content_Sync_Fusion;

class Helper {
	/**
	 * Get synchronization objects based on the current emitter blog id
	 * Sync are set to be attachment and "auto"
	 *
	 * @param int $blog_id The wanted emitter blog id to get syncs from
	 *
	 * @since  1.0.1
	 * @author Maxime CULEA
	 *
	 * @return \BEA_CSF_Synchronization[]
	 */
	public static function get_syncronizations( $blog_id = 0 ) {
		return \BEA_CSF_Synchronizations::get( [
			'post_type' => 'attachment',
			'emitters'  => ! empty( $blog_id ) ? $blog_id : get_current_blog_id(),
		], 'AND', false, true );
	}

	/**
	 * Get receivers from given synchronizations
	 *
	 * @param \BEA_CSF_Synchronization[] $syncs
	 *
	 * @since  1.0.1
	 * @author Maxime CULEA
	 *
	 * @return array
	 */
	public static function get_receivers_blogs_ids( $syncs ) {
		$receivers_blogs_ids = [];
		foreach ( $syncs as $sync ) {
			if ( empty( $sync->receivers ) ) {
				continue;
			}

			if ( 'all' === $sync->receivers[0] ) {
				// Get all sites
				$blogs = \BEA_CSF_Synchronizations::get_sites_from_network();
				foreach ( $blogs as $blog ) {
					// Exclude emitters
					if ( ! in_array( $blog['blog_id'], $sync->emitters ) ) {
						$receivers_blogs_ids[] = $blog['blog_id'];
					}
				}
			} else {
				foreach ( $sync->receivers as $blog_id ) {
					$receivers_blogs_ids[] = $blog_id;
				}
			}
		}

		// Unique blog ids as int
		return array_unique( array_map( 'intval', $receivers_blogs_ids ) );
	}

	/**
	 * Get a receiver attachment id from an emitter attachment id
	 *
	 * @param int $emitter_blog_id
	 * @param int $receiver_blog_id
	 * @param int $media_id
	 *
	 * @author Maxime CULEA
	 * @since  1.0.1
	 *
	 * @return int
	 */
	public static function get_receiver_obj_id_from_emitter_obj_id( $emitter_blog_id, $receiver_blog_id, $media_id ) {
		$receiver_media_id = \BEA_CSF_Relations::get_object_id_for_receiver( 'attachment', $emitter_blog_id, $receiver_blog_id, $media_id );

		return empty( $receiver_media_id ) ? 0 : (int) $receiver_media_id->receiver_id;
	}
}