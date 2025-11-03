(function () {
	if (typeof tinymce === 'undefined') return;

	tinymce.PluginManager.add('running_routes', function (editor, url) {
		editor.addButton('running_routes', {
			text: 'Running Route',
			icon: false,
			onclick: function () {
				editor.windowManager.open({
					title: 'Insert Running Route',
					body: [
						{ type: 'textbox', name: 'id', label: 'Route ID' },
						{ type: 'textbox', name: 'height', label: 'Height (px)', value: '500' },
						{
							type: 'listbox',
							name: 'layer',
							label: 'Map Layer',
							values: [
								{ text: 'OpenTopoMap', value: 'opentopomap' },
								{ text: 'OpenStreetMap', value: 'osm' },
								{ text: 'Mapy.cz', value: 'mapycz' }
							],
							value: 'opentopomap'
						}
					],
					onsubmit: function (e) {
						editor.insertContent(
							'[running_route id="' + e.data.id + '" height="' + e.data.height + '" layer="' + e.data.layer + '"]'
						);
					}
				});
			}
		});
	});
})();