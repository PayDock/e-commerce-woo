const envPluginPrefix                   = window.widgetSettings.pluginPrefix;
const environmentPluginIdentifier       = envPluginPrefix + '_' + envPluginPrefix;
const checkoutVersionSelectBoxId        = 'woocommerce_' + environmentPluginIdentifier + '_CHECKOUT_VERSION';
const selectElementsToUpdateOnEnvChange = [checkoutVersionSelectBoxId, 'woocommerce_' + environmentPluginIdentifier + '_CHECKOUT_CONFIGURATION_ID', 'woocommerce_' + environmentPluginIdentifier + '_CHECKOUT_CUSTOMISATION_ID']
const elementsToUpdateOnEnvChange       = ['woocommerce_' + environmentPluginIdentifier + '_CREDENTIALS_ACCESS_KEY', ...selectElementsToUpdateOnEnvChange]
const environmentSelectBoxId            = 'woocommerce_' + environmentPluginIdentifier + '_ENVIRONMENT_ENVIRONMENT';

// noinspection JSUnresolvedReference
jQuery( document ).ready(
	function () {
		const environmentSelectBoxElement = document.getElementById( environmentSelectBoxId );
		if (!environmentSelectBoxElement) {
			return;
		}
		const selectedEnvironmentSavedToDB = environmentSelectBoxElement.value;
		const form                         = document.getElementById( 'mainform' );

		let savedElements = saveSelectOptionsByEnvironment();
		const formData    = getFormData();

		environmentSelectBoxElement.addEventListener(
			'change',
			() => {
				elementsToUpdateOnEnvChange.forEach(
					( element ) => {
						if ( selectedEnvironmentSavedToDB !== environmentSelectBoxElement.value ) {
							removeElementsValues( element );
						} else {
							addElementsValueAndOptions( element );
						}
					}
				);
			}
		);

		function removeElementsValues( element ) {
			form.elements[ element ].value = '';
			if ( selectElementsToUpdateOnEnvChange.includes( form.elements[ element ].id ) ) {
				if (form.elements[ element ].id !== checkoutVersionSelectBoxId) {
					form.elements[ element ].innerHTML = '';
				}
				form.elements[ element ].selectedIndex = -1;
			}
		}

		function addElementsValueAndOptions( element ) {
			form.elements[ element ].value = formData[ element ];
			if ( savedElements[ environmentSelectBoxElement.value ] ) {
				form.elements[ element ].innerHTML = savedElements[ environmentSelectBoxElement.value ][ element ];

				if ( !formData[ element ] ) {
					form.elements[ element ].selectedIndex = -1;
				}
			}
		}

		function getFormData() {
			const formData = {};
			for ( let element of form.elements ) {
				if ( element.name?.includes( 'woocommerce_' + environmentPluginIdentifier ) ) {
					formData[ element.name ] = element.value;
				}
			}
			return formData;
		}

		function saveSelectOptionsByEnvironment() {
			let savedElements                                     = {}
			selectElementsToUpdateOnEnvChange.forEach(
				( id ) => {
					savedElements[ selectedEnvironmentSavedToDB ] = {
						...savedElements[ selectedEnvironmentSavedToDB ],
						[ id ]: form.elements[ id ].innerHTML,
					};
				}
			);
			return savedElements;
		}
	}
);
