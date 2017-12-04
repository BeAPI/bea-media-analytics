<?php namespace BEA\Find_Media\Admin;

use BEA\Find_Media\Singleton;
use BEA\Find_Media\DB;

class Media_Template {
	use Singleton;

	protected function init() {
		add_action( 'wp_enqueue_media', [ $this, 'javascript_for_view' ], 99 );
	}

	public function javascript_for_view() { ?>
		<script type='application/javascript'>
            var params = {};
            window.location.search.replace(/[?&]+([^=&]+)=([^&]*)/gi, function (str, key, value) {
                params[key] = value;
            });
            if ( params && 0 != params.item.length) {
                jQuery('.attachment-info .settings').append('<p>' + params.item + '</p>');
            }
		</script>
		<?php

		/**
		 * '<label class="setting">' +
		 * '<span class="name">Times used</span>' +
		 * '<span class="value">' +
		 * '<a href="post.php?post=' + params.item + '&action=edit">' +
		 * wp.media.attachment(params.item).attributes.bea_find_media_counter; +
		 * '</a>' +
		 * '</span>' +
		 * '</label>'
		 */
	}
}