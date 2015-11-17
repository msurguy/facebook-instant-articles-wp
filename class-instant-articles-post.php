<?php

/**
 * Class responsible for constructing our content and preparing it for rendering
 *
 * @since 0.1
 */
class Instant_Articles_Post {

	/** @var int ID of the post */
	protected $_ID = 0;

	/**
	 * Setup data and build the content
	 *
	 * @since 0.1
	 * @param int  $post_id  ID of the post
	 */
	function __construct( $post_id ) {
		$this->_ID = $post_id;
	}

	/**
	 * Get the ID for this post
	 *
	 * @since 0.1
	 * @return int  The post ID
	 */
	public function get_the_ID() {
		return $this->_ID;
	}

	/**
	 * Get the title for this post
	 *
	 * @since 0.1
	 * @return string The title
	 */
	public function get_the_title() {
		$title = get_the_title( $this->get_the_ID() );

		/**
		 * Filter the post title for use in instant articles
		 *
		 * @since 0.1
		 *
		 * @param string                 $title                 The current post title.
		 * @param Instant_Arcticle_Post  $instant_article_post  The instant article post
		 */
		$title = apply_filters( 'instant_articles_title', $title, $this );

		return $title;
	}

	/**
	 * Get the title for this post
	 *
	 * @since 0.1
	 * @return string  The title
	 */
	public function get_the_title_rss() {
		$title = $this->get_the_title();

		/**
		 * Apply the default WP Filters for the post title for use in a feed. This ensures proper escaping.
		 *
		 * @since 0.1
		 *
		 * @param string  $title  The current post title.
		 */
		$title = apply_filters( 'the_title_rss', $title );

		return get_the_title( $this->get_the_ID() );
	}

	/**
	 * Get the canonical URL for this post
	 *
	 * A little warning here: It is extremely important that this is the same canonical URL as is used on the web site.
	 * This is the identificator Facebook use to connect the "read" web article with the instant article.
	 * Do not add any querystring params or URL fragments it. Not any. Not even for tracking.
	 *
	 * @since 0.1
	 * @return string  The canonical URL
	 */
	public function get_canonical_url() {
		$url = get_permalink( $this->get_the_ID() );

		return $url;
	}

	/**
	 * Get the excerpt for this post
	 *
	 * @since 0.1
	 * @return string  The excerpt
	 */
	public function get_the_excerpt() {

		$post = get_post( $this->get_the_ID() );

		// This should ideally not happen, but it may do so if someone tampers with the query.
		// Returning the same protected post excerpt as "usual" may help them identify what’s going on.
		if ( post_password_required( $this->get_the_ID() ) ) {
			return __( 'There is no excerpt because this is a protected post.' );
		}

		/**
		 * Apply the default WP Filters for the post excerpt
		 *
		 * @since 0.1
		 *
		 * @param string  $post_excerpt  The post excerpt.
		 */
		$excerpt = apply_filters( 'get_the_excerpt', $post->post_excerpt );

		/**
		 * Filter the post excerpt for instant articles
		 *
		 * @since 0.1
		 *
		 * @param string                 $excerpt               The current post excerpt.
		 * @param Instant_Arcticle_Post  $instant_article_post  The instant article post
		 */
		$excerpt = apply_filters( 'instant_articles_excerpt', $excerpt, $this );

		return $excerpt;
	}

	/**
	 * Get the excerpt for this post
	 *
	 * @since 0.1
	 * @return string The excerpt
	 */
	public function get_the_excerpt_rss() {

		$excerpt = $this->get_the_excerpt();

		/**
		 * Apply the default WP Filters for the post excerpt for use in a feed. This ensures proper escaping.
		 *
		 * @since 0.1
		 *
		 * @param string  $excerpt  The current post excerpt.
		 */
		$excerpt = apply_filters( 'the_excerpt_rss', $excerpt );

		return $excerpt;
	}

	/**
	 * Get the pubdate for the RSS
	 *
	 * @since 0.1
	 * @return string  The pubdate formatted suitable for use in the RSS feed (ISO 8601)
	 */
	public function get_pubdate_rss() {
		return mysql2date( 'c', get_post_time( 'Y-m-d H:i:s', true, $this->get_the_ID() ), false );
	}

	/**
	 * Render post
	 *
	 * @since 0.1
	 */
	function render() {
		
		$post_id = $this->get_the_ID();

		/**
	     * Fires before the instant article is rendered
	     *
	     * @since 0.1
	     * @param Instant_Arcticle_Post  $instant_article_post  The instant article post
	     */
		do_action( 'pre_instant_article_render', $this );
		
		$default_template = dirname( __FILE__ ) . '/template.php';

		/**
	     * Filter the path to the template to use to render the instant article
	     *
	     * @since 0.1
	     * @param string                    $template               Path to the current (default) template.
	     * @param Instant_Arcticle_Post     $instant_article_post   The instant article post
	     */
		$template = apply_filters( 'instant_articles_render_post_template', $default_template, $this );
		
		// Make sure the template exists. Devs do the darndest things.
		if ( ! file_exists( $template ) ) {
			$template = $default_template;
		}
		include $template;
		
		/**
	     * Fires after the instant article is rendered
	     *
	     * @since 0.1
	     * @param Instant_Arcticle_Post  $instant_article_post  The instant article post
	     */
		do_action( 'after_instant_article_render', $this );
	}

	/**
	 * Article <head> style
	 *
	 * @since 0.1
	 */
	function get_article_style() {

		/**
	     * Filter the article style to use
	     *
	     * @since 0.1
	     * @param string                    $template               Path to the current (default) template.
	     * @param Instant_Arcticle_Post     $instant_article_post   The instant article post
	     */
		$article_style = apply_filters( 'instant_articles_style', 'default', $this );

		printf( '', esc_attr( $article_style ) );
	}

}
