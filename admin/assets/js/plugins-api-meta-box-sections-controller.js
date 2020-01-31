document.addEventListener( 'DOMContentLoaded', function() {

	/**
	 * Controls the functionality of the 'sections' in the 'plugins api' Meta Box.
	 * 
	 * @constructor
	 * 
	 * @property {HTMLElement} sectionsContainer		- The root element holding all sections added.
	 * @property {HTMLInputElement} addSectionButton	- The button to control adding a new section.
	 * @property {array} textareas						- An array to hold the 'textarea' elements used to initialize the WP Editors.
	 */
	function PluginsApiMetaBoxSectionsController() {
		this.sectionsContainer = document.getElementById( 'di-hosted-plugin-sections' );
		this.addSectionButton = document.getElementById( 'di-hosted-plugin-sections-add-section' );
		this.textareas = [];

		this.addSectionButton.addEventListener( 'click', this.addSection.bind( this ) );
		this.initializeData();
	}

	/**
	 * Take any section data passed from WordPress and turn it into section instances.
	 */
	PluginsApiMetaBoxSectionsController.prototype.initializeData = function() {
		if ( Array.isArray( sections ) ) {
			var that = this;

			sections.forEach( function( section ) {
				that.addSection( section.slug, section.content );
			} );
		}
	}

	/**
	 * Add a section to the list of sections and create all HTML elements.
	 * 
	 * @param {string} name The name of the section.
	 * @param {string} content The content of the section.
	 */
	PluginsApiMetaBoxSectionsController.prototype.addSection = function( name = '', content = '' ) {
		name = ( 'string' === typeof name ) ? name : '';
		content = ( 'string' === typeof content ) ? content : '';

		var container = document.createElement( 'div' ),
			table = document.createElement( 'table' );

		table.appendChild( this.createSectionNameContainer( name ) );
		table.appendChild( this.createSectionContentContainer( content ) );
		table.appendChild( this.createRemoveSectionContainer( container ) );

		container.appendChild( table );
		container.className = 'section';

		this.sectionsContainer.appendChild( container );
		this.renderSections();
	};

	/**
	 * Create and return an HTMLTableRowElement element holding the name input and label for a section.
	 * 
	 * @param {string} name The name to create the element with.
	 * 
	 * @return {HTMLTableRowElement} The row element to be inserted into a table.
	 */
	PluginsApiMetaBoxSectionsController.prototype.createSectionNameContainer = function( name ) {
		var containerRow = document.createElement( 'tr' ),
			labelCol = document.createElement( 'td' ),
			nameCol = document.createElement( 'td' ),
			label = document.createElement( 'label' ),
			input = document.createElement( 'input' );

		label.innerHTML = 'Section Slug';

		input.type = 'text';
		input.value = name;
		input.className = 'name-input';

		labelCol.appendChild( label );
		nameCol.appendChild( input );

		containerRow.appendChild( labelCol );
		containerRow.appendChild( nameCol );

		return containerRow;
	};

	/**
	 * Create and return an HTMLTableRowElement element holding the textarea and content for a section.
	 * 
	 * @param {string} content The content to create the element with.
	 * 
	 * @return {HTMLTableRowElement} The row element to be inserted into a table.
	 */
	PluginsApiMetaBoxSectionsController.prototype.createSectionContentContainer = function( content ) {
		var containerRow = document.createElement( 'tr' ),
			labelCol = document.createElement( 'td' ),
			contentCol = document.createElement( 'td' ),
			label = document.createElement( 'label' ),
			input = document.createElement( 'textarea' );

		label.innerHTML = 'Section Content';

		input.rows = 10;
		input.value = content;
		input.className = 'content-input';

		this.textareas.push( input );

		labelCol.appendChild( label );
		contentCol.appendChild( input );

		containerRow.appendChild( labelCol );
		containerRow.appendChild( contentCol );

		return containerRow;
	};

	/**
	 * Create and return an HTMLTableRowElement holding the remove button for the section.
	 * 
	 * @param {HTMLDivElement} container The containing DIV element to bind the the remove event.
	 */
	PluginsApiMetaBoxSectionsController.prototype.createRemoveSectionContainer = function ( container ) {
		var containerRow = document.createElement( 'tr' ),
			buttonCol = document.createElement( 'td' ),
			button = document.createElement( 'input' );

		button.type = 'button';
		button.value = 'Remove';
		button.className = 'button button-delete';

		button.addEventListener( 'click', function() {
			container.parentElement.removeChild( container );
		} );

		buttonCol.appendChild( button );
		containerRow.appendChild( buttonCol );

		return containerRow;
	}

	/**
	 * A simple list of tasks required to refresh the WP Editor instances when things change.
	 */
	PluginsApiMetaBoxSectionsController.prototype.renderSections = function() {
		this.unsetEditorInstances();
		this.updateSectionIndexes();
		this.setEditorInstances();
	};

	/**
	 * 
	 */
	PluginsApiMetaBoxSectionsController.prototype.updateSectionIndexes = function() {
		for ( var i = 0; i < this.sectionsContainer.children.length; i++ ) {
			var content = this.sectionsContainer.children[ i ].querySelector( 'textarea.content-input' );
			var slug = this.sectionsContainer.children[ i ].querySelector( 'input.name-input' );

			slug.name = 'di_hosted_plugin[plugins_api][sections][' + i + '][slug]';
			content.id = 'di-hosted-plugin-section-' + i;
			content.name = 'di_hosted_plugin[plugins_api][sections][' + i + '][content]';
		}
	};

	/**
	 * 
	 */
	PluginsApiMetaBoxSectionsController.prototype.unsetEditorInstances = function() {
		var id = null;

		for ( var i = 0; i < this.textareas.length; i++ ) {
			id = this.textareas[ i ].id;

			if ( id ) {
				this.textareas[ i ].value = wp.editor.getContent( id );
				wp.editor.remove( id );
			}
		}
	};
	
	/**
	 * 
	 */
	PluginsApiMetaBoxSectionsController.prototype.setEditorInstances = function() {
		var id = null;

		for ( var i = 0; i < this.textareas.length; i++ ) {
			id = this.textareas[ i ].id;
			name = this.textareas[ i ].name;

			if ( id ) {
				wp.editor.initialize( id, { tinymce: true, quicktags: true } );
			}
		}
	};

	// Initialize controller
	new PluginsApiMetaBoxSectionsController()

} );