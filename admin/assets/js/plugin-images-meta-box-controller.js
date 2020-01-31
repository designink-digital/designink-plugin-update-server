document.addEventListener( 'DOMContentLoaded', function() {

	/**
	 * Tracks inputs related to the 'plugin images' Meta Box and events triggered on them.
	 * 
	 * @constructor
	 * 
	 * @property {object} buttons			- The button HTML elements for adding and removing images.
	 * @property {object} images			- The image HTML elements representing selected inputs.
	 * @property {object} inputs			- The hidden inputs which hold the eventual submission values.
	 * @property {object} wpMediaInstance	- The WordPress admin media modal box for selecting media posts.
	 * @property {object} activeInstance	- The image type and image size of the input currently hooked to the media modal.
	 */
	function PluginImagesMetaBoxController() {

		this.buttons = {
			'icons': {
				'1x': {
					'select': document.getElementById( 'di_hosted_plugin_images_icons_1x_select_button' ),
					'remove': document.getElementById( 'di_hosted_plugin_images_icons_1x_remove_button' ),
				},
				'2x': {
					'select': document.getElementById( 'di_hosted_plugin_images_icons_2x_select_button' ),
					'remove': document.getElementById( 'di_hosted_plugin_images_icons_2x_remove_button' ),
				},
			},
			'banners': {
				'1x': {
					'select': document.getElementById( 'di_hosted_plugin_images_banners_1x_select_button' ),
					'remove': document.getElementById( 'di_hosted_plugin_images_banners_1x_remove_button' ),
				},
				'2x': {
					'select': document.getElementById( 'di_hosted_plugin_images_banners_2x_select_button' ),
					'remove': document.getElementById( 'di_hosted_plugin_images_banners_2x_remove_button' ),
				},
			},
		};

		this.images = {
			'icons': {
				'1x': document.getElementById( 'di_hosted_plugin_images_icons_1x_image' ),
				'2x': document.getElementById( 'di_hosted_plugin_images_icons_2x_image' ),
			},
			'banners': {
				'1x': document.getElementById( 'di_hosted_plugin_images_banners_1x_image' ),
				'2x': document.getElementById( 'di_hosted_plugin_images_banners_2x_image' ),
			},
		};

		this.inputs = {
			'icons': {
				'1x': document.getElementById( 'di_hosted_plugin_images_icons_1x_input' ),
				'2x': document.getElementById( 'di_hosted_plugin_images_icons_2x_input' ),
			},
			'banners': {
				'1x': document.getElementById( 'di_hosted_plugin_images_banners_1x_input' ),
				'2x': document.getElementById( 'di_hosted_plugin_images_banners_2x_input' ),
			},
		};

		this.wpMediaInstance = wp.media({
			title: 'Select Image',
			button: {
				text: 'Use Media'
			},
			multiple: false
		});
	
		this.activeInstance = null;
		this.wpMediaInstance.on( 'select', this.handleFileSelect.bind( this ) );
		this.addButtonListeners();
		this.initWordpressData();
	}

	/**
	 * Grab the localized data from WordPress and set images.
	 */
	PluginImagesMetaBoxController.prototype.initWordpressData = function() {
		const that = this;

		if ( ids && 'object' === typeof ids.icons ) {
			const iconKeys = Object.keys( ids.icons );
			iconKeys.forEach( size => {
				if ( '' !== urls.icons[ size ] ) {
					const image = { id: ids.icons[ size ], url: urls.icons[ size ] };
					that.setUpImage( 'icons', size, image );
				}
			} );
		}

		if ( ids && 'object' === typeof ids.banners ) {
			const bannersKeys = Object.keys( ids.banners );
			bannersKeys.forEach( size => {
				if ( '' !== urls.banners[ size ] ) {
					const image = { id: ids.banners[ size ], url: urls.banners[ size ] };
					that.setUpImage( 'banners', size, image );
				}
			} );
		}
	};

	/**
	 * Add the event listeners to the add/remove buttons on initialization.
	 */
	PluginImagesMetaBoxController.prototype.addButtonListeners = function() {
		const selectButtons = [
			this.buttons['icons']['1x']['select'],
			this.buttons['icons']['2x']['select'],
			this.buttons['banners']['1x']['select'],
			this.buttons['banners']['2x']['select'],
		];

		const removeButtons = [
			this.buttons['icons']['1x']['remove'],
			this.buttons['icons']['2x']['remove'],
			this.buttons['banners']['1x']['remove'],
			this.buttons['banners']['2x']['remove'],
		];

		selectButtons.forEach( button => {
			button.addEventListener( 'click', this.handleSelectEvent.bind( this ) );
		} );

		removeButtons.forEach( button => {
			button.addEventListener( 'click', this.handleRemoveEvent.bind( this ) );
		} );
	};

	/**
	 * Handle a select event.
	 * 
	 * @param {Event} event The click event triggered on the select button.
	 */
	PluginImagesMetaBoxController.prototype.handleSelectEvent = function( event ) {
		this.activeInstance = {
			type: event.target.getAttribute( 'data-type' ),
			size: event.target.getAttribute( 'data-size' ),
		};

		this.wpMediaInstance.open();
	};

	/**
	 * Handle a remove event.
	 * 
	 * @param {Event} event The click event triggered on the remove button.
	 */
	PluginImagesMetaBoxController.prototype.handleRemoveEvent = function( event ) {
		const instance = {
			type: event.target.getAttribute( 'data-type' ),
			size: event.target.getAttribute( 'data-size' ),
		};

		this.images[ instance.type ][ instance.size ].src = '';
		this.inputs[ instance.type ][ instance.size ].value = '';
		this.buttons[ instance.type ][ instance.size ].select.classList.remove( 'hidden' );
		this.buttons[ instance.type ][ instance.size ].remove.classList.add( 'hidden' );
	};

	/**
	 * The event handler for the WP admin media modal, gets the selected value and applies it to the active media instance.
	 */
	PluginImagesMetaBoxController.prototype.handleFileSelect = function() {
		if ( null !== this.activeInstance ) {
			const image = this.wpMediaInstance.state().get( 'selection' ).first().toJSON();
			this.setUpImage( this.activeInstance.type, this.activeInstance.size, image );
		}
	};

	/**
	 * Set up an image and its inputs when given a media Post ID.
	 * 
	 * @property {string} type The type of image (banners or icons).
	 * @property {string} size The size of the image (1x or 2x).
	 * @property {object} image An object with `url` and `id` properties.
	 */
	PluginImagesMetaBoxController.prototype.setUpImage = function( type, size, image ) {
		this.images[ type ][ size ].src = image.url;
		this.inputs[ type ][ size ].value = image.id;
		this.buttons[ type ][ size ].select.classList.add( 'hidden' );
		this.buttons[ type ][ size ].remove.classList.remove( 'hidden' );
	};


	// Initialize controller.
	new PluginImagesMetaBoxController();


} );